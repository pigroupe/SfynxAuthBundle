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

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sfynx\CoreBundle\Controller\abstractController;
use Sfynx\CoreBundle\Layers\Domain\Service\Request\Generalisation\RequestInterface;
use Sfynx\ToolBundle\Builder\RouteTranslatorFactoryInterface;
use Sfynx\AuthBundle\Domain\Service\User\Generalisation\Interfaces\UserManagerInterface;
use Sfynx\AuthBundle\Domain\Service\User\Mailer\PiMailerManager;

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
    /** @var EngineInterface */
    protected $templating;
    /** @var  PiMailerManager */
    protected $mailer;

    /**
     * FrontendController constructor.
     *
     * @param RouteTranslatorFactoryInterface $router
     * @param RequestInterface $request
     * @param UserManagerInterface $UserManager
     * @param EngineInterface $templating
     * @param $mailer
     */
    public function __construct(
        RouteTranslatorFactoryInterface $router,
        RequestInterface $request,
        UserManagerInterface $UserManager,
        EngineInterface $templating,
        PiMailerManager $mailer
    ) {
        $this->router = $router;
        $this->request = $request;
        $this->UserManager = $UserManager;

        $this->templating = $templating;
        $this->mailer = $mailer;
    }

    /**
     * Request reset user password: show form
     */
    public function requestAction()
    {
        $NoLayout   = $this->request->getQuery()->get('NoLayout');
        $templateFile = '@SfynxTheme/Login/Resetting/request.html.twig';

        return $this->templating->renderResponse($templateFile, ['NoLayout' => $NoLayout]);
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
        $username = $this->request->get('username');
        $template = $this->request->get('template');
        $routereset = $this->request->get('routereset');
        $type = $this->request->get('type');

        if (empty($template)) {
            $template = '@SfynxTheme/Login/Resetting/request.html.twig';
        }

        $user  =  $this->UserManager->getQueryRepository()->findOneBy(['username' => $username]);

        if($this->request->isXmlHttpRequest()){
            $response = new JsonResponse();
            if (null === $user) {
                return $response->setData(
                    json_encode([
                            'text'  => 'Identifiant inconnu',
                            'error' => true,
                            'type'  => 'unknown'
                        ]
                    )
                );
            } else if ($user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl')) && $type == 'send') {
                return $response->setData(
                    json_encode([
                            'text'=> 'Vous devez au préalable activer votre compte en cliquant sur le mail de Confirmation d\'inscription reçu',
                            'error' => true,
                            'type' => '24h'
                        ]
                    )
                );
            } else {
                $this->UserManager->tokenUser($user);
                $this->mailer->sendResettingEmailMessage($user, $routereset);
                $this->UserManager->update($user);

                return $response->setData(
                    json_encode([
                            'text'  => 'Un email vous a été envoyé pour créer un nouveau mot de passe sur le site',
                            'error' => false
                        ]
                    )
                );
            }
        } else {
            if (null === $user) {
                return $this->templating->renderResponse($template, ['invalid_username' => $username]);
            }
            if ($user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
                return $this->templating->renderResponse(
                    '@SfynxTheme/Login/Resetting/passwordAlreadyRequested.html.twig'
                );
            }
            $this->UserManager->tokenUser($user);
            $this->mailer->sendResettingEmailMessage($user, $routereset);
            $this->UserManager->update($user);

            try {
                return $this->templating->renderResponse($template, ['success' => true]);
            } catch (\Exception $e) {
                $response = new RedirectResponse(
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
        $templateFile = '@SfynxTheme/Login/Resetting/checkEmail.html.twig';
        $session = $this->request->getSession();
        $email = $session->get(PiMailerManager::SESSION_EMAIL);
        $session->remove(PiMailerManager::SESSION_EMAIL);

        if (empty($email)) {
            // the user does not come from the sendEmail action
            return new RedirectResponse(
                $this->router->generate('sfynx_auth_resetting_request')
            );
        }

        return $this->templating->renderResponse($templateFile, ['email' => $email,]);
    }

    /**
     * Reset user password
     */
    public function resetAction(Request $request, $token)
    {
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->container->get('fos_user.resetting.form.factory');

        $user = $this->UserManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);

        if ('POST' === $this->request->getMethod()) {
            $form->bind($this->request);

            if ($form->isValid()) {
                if (null === $response = $event->getResponse()) {
                    $url = $this->router->generate('sfynx_user_profile_show');
                    $response = new RedirectResponse($url);
                }

                return $response;
            }
        }
        $templateFile = '@SfynxTheme/Login/Resetting/reset.html.twig';

        return $this->templating->renderResponse(
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
