<?php
/**
 * This file is part of the <Auth> project.
 *
 * @subpackage   Auth
 * @package    Controller
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @since 2012-01-03
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\AuthBundle\Presentation\Coordination;

use Sfynx\CoreBundle\Layers\Domain\Service\Cookie\Generalisation\CookieInterface;
use Sfynx\CoreBundle\Layers\Domain\Service\Request\Generalisation\RequestInterface;
use Sfynx\ToolBundle\Builder\RouteTranslatorFactoryInterface;
use Sfynx\AuthBundle\Domain\Service\User\Generalisation\Interfaces\UserManagerInterface;
use Sfynx\AuthBundle\Domain\Generalisation\Interfaces\UserInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Sfynx\AuthBundle\Presentation\Coordination\Generalisation\TraitParameters;
use Sfynx\AuthBundle\Domain\Entity\User;
use Sfynx\CoreBundle\Controller\abstractController;
use Sfynx\AuthBundle\Infrastructure\Event\ViewObject\ResponseEvent;
use Sfynx\AuthBundle\Infrastructure\Event\SfynxAuthEvents;
use Sfynx\AuthBundle\Domain\Service\User\UserStorage;

/**
 * Frontend controller.
 *
 * @subpackage Auth
 * @package    Controller
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
class FrontendController extends abstractController
{
    use TraitParameters;

    /** @var CookieInterface */
    protected $cookie;
    /** @var RequestInterface */
    protected $request;
    /** @var RouteTranslatorFactoryInterface $routeFactory */
    protected $routeFactory;
    /** @var UserManagerInterface $UserManager */
    protected $UserManager;
    /** @var EngineInterface $templating */
    protected $templating;
    /** @var EventDispatcherInterface $EventDispatcher */
    protected $EventDispatcher;
    /** @var UserStorage $TokenStorage */
    protected $TokenStorage;
    /** @var RegistryInterface $registry */
    protected $registry;

    /**
     * FrontendController constructor.
     *
     * @param CookieInterface                 $cookie
     * @param RequestInterface                $request
     * @param RouteTranslatorFactoryInterface $routeFactory
     * @param UserManagerInterface            $UserManager
     * @param EngineInterface                 $templating
     * @param EventDispatcherInterface        $EventDispatcher
     * @param TokenStorageInterface           $TokenStorage
     * @param RegistryInterface               $registry
     */
    public function __construct(
        CookieInterface $cookie,
        RequestInterface $request,
        RouteTranslatorFactoryInterface $routeFactory,
        UserManagerInterface $UserManager,
        EngineInterface $templating,
        EventDispatcherInterface $EventDispatcher,
        TokenStorageInterface $TokenStorage,
        RegistryInterface $registry
    ) {
        $this->cookieFactory = $cookie;
        $this->request = $request;
        $this->routeFactory = $routeFactory;
        $this->UserManager = $UserManager;
        $this->templating = $templating;
        $this->EventDispatcher = $EventDispatcher;
        $this->tokenStorage = new UserStorage($TokenStorage);
        $this->registry = $registry;
    }

    /**
     * Main default page
     *
     * @Secure(roles="ROLE_EDITOR")
     * @return Response
     * @since  2012-01-24
     *
     * @throws \RuntimeException if the template cannot be rendered
     */
    public function indexAction()
    {
        return $this->templating->renderResponse(
            $this->getParameter('sfynx.template.theme.layout.admin.home'),
            []
        );
    }

    /**
     * Licence page
     *
     * @return Response
     * @since  2012-01-24
     *
     * @throws \RuntimeException if the template cannot be rendered
     */
    public function licenceAction()
    {
    	return $this->templating->renderResponse(
            'SfynxAuthBundle:Frontend:licence.html.twig',
            []
        );
    }

    /**
     * Configures the local language
     *
     * @param string $locale
     *
     * @return RedirectResponse
     * @since  2011-12-29
     *
     * @throws \InvalidArgumentException
     */
    public function setLocalAction($langue = '')
    {
        // It tries to redirect to the original page.
        $referer  = $this->routeFactory->getRefererRoute($langue, null, true);
        $response = new RedirectResponse($referer);
        $response->headers->setCookie(new Cookie('_locale', $langue, $this->cookieFactory->getDateExpire()));
        // we register the new local value
        $user = $this->tokenStorage->getUser();
        if ($user instanceof UserInterface) {
            $entity = $this->registry->getManager()->getRepository("SfynxAuthBundle:Langue")->find($langue);
            $user->setLangCode($entity);
            $this->UserManager->update($user);
        }

        return $response;
    }

    /**
     * Redirection by routename
     *
     * @param string $routename Route name value
     * @param string $locale    Locale value
     *
     * @return RedirectResponse
     * @since  2015-03-17
     *
     * @throws \InvalidArgumentException
     */
    public function redirectionAction($routename, $locale)
    {
        $url = $this->routeFactory->generate($routename, ['locale' => $locale]);

        return new RedirectResponse($url);
    }

    /**
     * Redirection function
     *
     * @return Response
     * @since  2012-01-24
     *
     * @throws \InvalidArgumentException
     */
    public function redirectionuserAction()
    {
    	if ($this->request->cookies->has('sfynx-redirection')) {
            $parameters  = [];
            $redirection = $this->request->cookies->get('sfynx-redirection');

            return $this->redirect(
                $this->routeFactory->generate($redirection, $parameters)
            );
    	}

        return new RedirectResponse(
            $this->routeFactory->generate('home_page')
        );
    }

    /**
     * Login failure function
     *
     * @return Response
     * @since  2014-07-26
     *
     * @throws \InvalidArgumentException
     */
    public function loginfailureAction()
    {
        if ($this->request->isXmlHttpRequest()) {
            $response = new Response(json_encode('error'));
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }
        $response = new RedirectResponse(
            $this->routeFactory->generate('sfynx_auth_security_login')
        );
        $event_response = new ResponseEvent($response);
        $this->EventDispatcher->dispatch(
            SfynxAuthEvents::HANDLER_LOGIN_FAILURE,
            $event_response
        );

        return $event_response->getResponse();
    }
}
