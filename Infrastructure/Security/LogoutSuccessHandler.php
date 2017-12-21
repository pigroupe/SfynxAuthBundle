<?php
namespace Sfynx\AuthBundle\Infrastructure\Security;

use Sfynx\AuthBundle\Infrastructure\Role\Generalisation\RoleFactoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Sfynx\ToolBundle\Builder\RouteTranslatorFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Sfynx\AuthBundle\Infrastructure\Event\ViewObject\ResponseEvent;
use Sfynx\AuthBundle\Infrastructure\Event\SfynxAuthEvents;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class LogoutSuccessHandler implements LogoutHandlerInterface
{
    /** @var EntityManagerInterface */
    protected $em;
    /** @var RouteTranslatorFactoryInterface  */
    protected $router;
    /** @var EventDispatcherInterface  */
    protected $dispatcher;
    /** @var TokenInterface */
    protected $token;
    /** @var Request */
    protected $request;

    /**
     * FrontendController constructor.
     *
     * @param RoleFactoryInterface $roleFactory
     * @prama LoggerInterface $logger
     * @param EventDispatcherInterface $dispatcher
     * @param RouteTranslatorFactoryInterface $router
     * @param EntityManagerInterface $em
     */
    public function __construct(
        RoleFactoryInterface $roleFactory,
        LoggerInterface $logger,
        EventDispatcherInterface $dispatcher,
        RouteTranslatorFactoryInterface $router,
        EntityManagerInterface $em
    ) {
        $this->roleFactory = $roleFactory;
        $this->logger = $logger;
        $this->router = $router;
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    public function logout(
        Request $request,
        Response $response,
        TokenInterface $token
    ) {
        $this->token = $token;
        $this->request = $request;
        // Sets init.
        $this->setValues();

        return $this->redirection();
    }

    /**
     * Sets values.
     *
     * @access protected
     * @return void
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    protected function setValues()
    {
        try {
            // we get the best role of the user.
            $BEST_ROLE_NAME = $this->roleFactory->getBestRoleUser();
            if (!empty($BEST_ROLE_NAME)) {
                $role = $this->em
                    ->getRepository("SfynxAuthBundle:Role")
                    ->findOneBy(['name' => $BEST_ROLE_NAME]);
                if ($role instanceof Role) {
                    $this->redirection = $role->getRouteLogout();
                }
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * Set logout redirection value in order to the role deconnected user
     *
     * @access protected
     * @return RedirectResponse
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     * @throws \InvalidArgumentException
     */
    protected function redirection()
    {
        if (!empty($this->redirection)) {
            $response = new RedirectResponse(
                $this->router->generate($this->redirection)
            );
        } else {
            $response = new RedirectResponse(
                $this->router->generate('home_page')
            );
        }
        $response->headers->setCookie(new Cookie('sfynx-ws-user-id', '', time() - 3600));
        $response->headers->setCookie(new Cookie('sfynx-ws-application-id', '', time() - 3600));
        $response->headers->setCookie(new Cookie('sfynx-ws-key', '', time() - 3600));
        $response->headers->setCookie(new Cookie('sfynx-layout', '', time() - 3600));
        $response->headers->setCookie(new Cookie('sfynx-screen', '', time() - 3600));
        $response->headers->setCookie(new Cookie('sfynx-redirection', '', time() - 3600));
        $response->headers->setCookie(new Cookie('_locale', '', time() - 3600));
        // we apply all events allowed to change the redirection response
        $event_response = new ResponseEvent($response, time() - 3600);
        $this->dispatcher->dispatch(SfynxAuthEvents::HANDLER_LOGOUT_CHANGERESPONSE, $event_response);
        $response = $event_response->getResponse();
        // Set log
        $this->logger->info(
            "User ".$this->getUser()->getId()." has been saved",
            array('user' => $this->getUser())
        );

        return $response;
    }

    /**
     * Return the connected user entity object.
     *
     * @access protected
     * @return \Sfynx\AuthBundle\Domain\Entity\user
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    protected function getUser()
    {
        if ($this->isUsernamePasswordToken()) {
            return $this->token->getUser();
        }
        return 'UserPhpUnit';
    }

    /**
     * Return if yes or no the user is UsernamePassword token.
     *
     * @return boolean
     * @access protected
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    protected function isUsernamePasswordToken()
    {
        if ($this->token instanceof UsernamePasswordToken) {
            return true;
        }
        return false;
    }
}
