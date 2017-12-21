<?php
/**
 * This file is part of the <Auth> project.
 *
 * @subpackage   Auth
 * @package    Controller
 * @since 2012-01-03
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\AuthBundle\Presentation\Coordination;

use Sfynx\ToolBundle\Builder\RouteTranslatorFactoryInterface;
use Sfynx\CoreBundle\Layers\Domain\Service\Request\Generalisation\RequestInterface;
use Sfynx\AuthBundle\Domain\Service\User\Generalisation\Interfaces\UserManagerInterface;

use Sfynx\CoreBundle\Controller\abstractController;
use Sfynx\AuthBundle\Mailer\PiMailerManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller managing the resetting of the password
 *
 * @subpackage Auth
 * @package    Controller
 */
class ResettingController extends abstractController
{
    /** @var RouteTranslatorFactoryInterface  */
    protected $router;
    /** @var RequestInterface */
    protected $request;
    /** @var UserManagerInterface */
    protected $UserManager;

    /**
     * FrontendController constructor.
     *
     * @param RouteTranslatorFactoryInterface $router
     * @param RequestInterface $request
     * @param UserManagerInterface $UserManager
     */
    public function __construct(
        RouteTranslatorFactoryInterface $router,
        RequestInterface $request,
        UserManagerInterface $UserManager
    ) {
        $this->router = $router;
        $this->request = $request;
        $this->UserManager = $UserManager;
    }

    /**
     * Request reset user password: show form
     */
    public function requestAction()
    {
        $NoLayout   = $this->request->getQuery()->get('NoLayout');

        $templateFile = str_replace('::', ':', $this->container->getParameter('sfynx.template.theme.login')).'Resetting:request.html.twig';

        return $this->container->get('templating')->renderResponse(
            $templateFile,
            array('NoLayout' => $NoLayout)
        );
    }

    /**
     * Request reset user password: submit form and send email
     *
     * @param Request $request
     *
     * @throws \InvalidArgumentException
     */
    public function sendEmailAction()
    {
        $username   = $this->request->get('username');
        $template   = $this->request->get('template');
        $routereset = $this->request->get('routereset');
        $type       = $this->request->get('type');

        if (empty($template)) {
            $template = str_replace('::', ':', $this->container->getParameter('sfynx.template.theme.login')).'Resetting:request.html.twig';
        }

        $user  =  $this->UserManager->getQueryRepository()->findOneBy(array('username' => $username));

        if($this->request->isXmlHttpRequest()){
            $response = new JsonResponse();
            if (null === $user) {
                return $response->setData(
                    json_encode(array(
                            'text'  => 'Identifiant inconnu',
                            'error' => true,
                            'type'  => 'unknown'
                        )
                    )
                );
            } else if ($user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl')) && $type == 'send') {
                return $response->setData(
                    json_encode(array(
                            'text'=> 'Vous devez au préalable activer votre compte en cliquant sur le mail de Confirmation d\'inscription reçu',
                            'error' => true,
                            'type' => '24h'
                        )
                    )
                );
            } else {
                $this->UserManager->tokenUser($user);
                $this->container->get('sfynx.auth.mailer')->sendResettingEmailMessage($user, $routereset);
                $this->UserManager->update($user);

                return $response->setData(
                    json_encode(array(
                        'text'  => 'Un email vous a été envoyé pour créer un nouveau mot de passe sur le site',
                        'error' => false)
                    )
                );
            }
        } else {
            if (null === $user) {
                return $this->container->get('templating')
                        ->renderResponse(
                            $template,
                            array('invalid_username' => $username)
                        );
            }
            if ($user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
                return $this->container->get('templating')->renderResponse(
                    str_replace('::', ':', $this->container->getParameter('sfynx.template.theme.login')) . 'Resetting:passwordAlreadyRequested.html.twig'
                );
            }
            $this->UserManager->tokenUser($user);
            $this->container->get('sfynx.auth.mailer')->sendResettingEmailMessage($user, $routereset);
            $this->UserManager->update($user);

            try {
                return $this->container->get('templating')
                ->renderResponse(
                    $template,
                    array('success' => true)
                );
            } catch (\Exception $e) {
                $response     = new RedirectResponse(
                    $this->router->generate('sfynx_auth_resetting_check_email')
                );
            }

            return $response->getContent();
        }
    }

    /**
     * Tell the user to check his email provider
     */
    public function checkEmailAction()
    {
        $templateFile = str_replace('::', ':', $this->container->getParameter('sfynx.template.theme.login')).'Resetting:checkEmail.html.twig';
        $session = $this->request->getSession();
        $email   = $session->get(PiMailerManager::SESSION_EMAIL);
        $session->remove(PiMailerManager::SESSION_EMAIL);

        if (empty($email)) {
            // the user does not come from the sendEmail action
            return new RedirectResponse(
                $this->router->generate('sfynx_auth_resetting_request')
            );
        }

        return $this->container->get('templating')->renderResponse(
            $templateFile,
            array(
                'email' => $email,
            )
        );
    }

    /**
     * Reset user password
     */
    public function resetAction(Request $request, $token)
    {
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->container->get('fos_user.resetting.form.factory');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        $user = $this->UserManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);

        if ('POST' === $this->request->getMethod()) {
            $form->bind($this->request);

            if ($form->isValid()) {
                $event = new FormEvent($form, $this->request);
                $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_SUCCESS, $event);
                $userManager->update($user);

                if (null === $response = $event->getResponse()) {
                    $url = $this->router->generate('sfynx_user_profile_show');
                    $response = new RedirectResponse($url);
                }
                $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                return $response;
            }
        }
        $templateFile = str_replace('::', ':', $this->container->getParameter('sfynx.template.theme.login')).'Resetting:reset.html.twig';

        return $this->container->get('templating')->renderResponse(
            $templateFile,
            array(
                'token' => $token,
                'form'  => $form->createView(),
            )
        );
    }

    protected function setFlash($action, $value)
    {
        $this->request->getSession()->getFlashBag()->add($action, $value);
    }
}
