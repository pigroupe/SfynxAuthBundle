<?php
/**
 * This file is part of the <Auth> project.
 *
 * @category   EventListener
 * @package    Handler
 * @subpackage Authentication
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2015 PI-GROUPE
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    2.3
 * @link       https://github.com/pigroupe/cmf-sfynx/blob/master/web/COPYING.txt
 * @since      2014-07-18
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\AuthBundle\Infrastructure\Security;

use Sfynx\CoreBundle\Layers\Domain\Service\Cookie\Generalisation\CookieInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Psr\Log\LoggerInterface;

use Sfynx\AuthBundle\Infrastructure\Event\ViewObject\ResponseEvent;
use Sfynx\AuthBundle\Infrastructure\Event\SfynxAuthEvents;


/**
 * Custom login handler.
 * This allow you to execute code right after the user succefully logs in.
 *
 * @category   EventListener
 * @package    Handler
 * @subpackage Authentication
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2015 PI-GROUPE
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    2.3
 * @link       https://github.com/pigroupe/cmf-sfynx/blob/master/web/COPYING.txt
 * @since      2014-07-18
 */
class AuthenticationLoginHandler
{
    /**
     * @var CookieInterface
     */
    protected $cookie;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var TokenStorageInterface
     */
    protected $TokenStorage;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var InteractiveLoginEvent
     */
    protected $event;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var string
     */
    protected $locale;

    /**
     * Constructs a new instance of SecurityListener.
     *
     * @param CookieInterface          $cookie
     * @param LoggerInterface          $logger
     * @param TokenStorageInterface    $security   The security context
     * @param EventDispatcherInterface $dispatcher The event dispatcher
     * @param RegistryInterface        $doctrine   The doctrine service
     * @param ContainerInterface       $container  The container service
     */
    public function __construct(
        CookieInterface $cookie,
        LoggerInterface $logger,
        TokenStorageInterface $TokenStorage,
        EventDispatcherInterface $dispatcher,
        RegistryInterface $doctrine,
        ContainerInterface $container
    ) {
        $this->cookieFactory = $cookie;
        $this->logger        = $logger;
        $this->tokenStorage  = $TokenStorage;
        $this->dispatcher = $dispatcher;
        $this->em         = $doctrine->getManager();
        $this->container  = $container;
    }

    /**
     * Invoked after a successful login.
     *
     * @param InteractiveLoginEvent $event The event
     *
     * @access public
     * @return void
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent  $event)
    {
        // Sets event.
        $this->event    = $event;
        // Sets the user local value.
        $this->setLocaleUser();
        // Associate to the dispatcher the onKernelResponse event.
        $this->dispatcher->addListener(
            KernelEvents::RESPONSE,
            [$this, 'onKernelResponse']
        );
        // Return the success connecion flash message.
        $this->getFlashBag()->clear();
    }

    /**
     * Sets the user local value.
     *
     * @access protected
     * @return void
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    protected function setLocaleUser()
    {
    	if (method_exists($this->getUser()->getLangCode(), 'getId')) {
            $this->locale = $this->getUser()
                ->getLangCode()
                ->getId();
    	} else {
            $this->locale = $this->container->get('request_stack')
                ->getCurrentRequest()
                ->getPreferredLanguage();
    	}
    	$this->getRequest()->setLocale($this->locale);
    }

    /**
     * Invoked after the response has been created.
     * Invoked to allow the system to modify or replace the Response object after its creation.
     *
     * @param FilterResponseEvent $event The event
     *
     * @access public
     * @return void
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        // we delete the username info in session if it exists
        if ($this->container->get('request_stack')
            ->getCurrentRequest()
            ->getSession()
            ->has('login-username'))
        {
            $this->container->get('request_stack')
                ->getCurrentRequest()
                ->getSession()
                ->remove('login-username');
        }
        // we apply all events allowed to change the redirection response
        $event_response = new ResponseEvent(
            null,
            $this->cookieFactory->getDateExpire(),
            $this->getRequest(),
            $this->getUser(),
            $this->locale
        );
        $this->container->get('event_dispatcher')->dispatch(SfynxAuthEvents::HANDLER_LOGIN_CHANGERESPONSE, $event_response);
        $response       = $event_response->getResponse();

        // we set logs
        $this->logger->info(
            "User ".$this->getUser()." has been saved",
            ['user' => $this->getUser()]
        );

        //
        $event->setResponse($response);
    }

    /**
     * Invoked to modify the controller that should be executed.
     *
     * @param FilterControllerEvent $event The event
     *
     * @access public
     * @return void
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function onKernelController(FilterControllerEvent $event)
    {
/*         $request = $event->getRequest();
        //$controller = $event->getController();

        //...

        // the controller can be changed to any PHP callable
        $event->setController($controller); */
    }

    /**
     * Invoked to allow some other return value to be converted into a Response.
     *
     * @param FilterControllerEvent $event The event
     *
     * @access public
     * @return void
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        /*         $val = $event->getControllerResult();
         $response = new Response();
        // some how customize the Response from the return value

        $event->setResponse($response); */
    }

    /**
     * Invoked to allow to create and set a Response object, create and set a new Exception object, or do nothing.
     *
     * @param FilterControllerEvent $event The event
     *
     * @access public
     * @return void
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        /*         $exception = $event->getException();
         $response = new Response();
        // setup the Response object based on the caught exception
        $event->setResponse($response); */

        // you can alternatively set a new Exception
        // $exception = new \Exception('Some special exception');
        // $event->setException($exception);
    }

    /**
     * Return the request object.
     *
     * @access protected
     * @return \Symfony\Component\HttpFoundation\Request
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    protected function getRequest()
    {
        return $this->event->getRequest();
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
        return $this->event->getAuthenticationToken()->getUser();
    }

    /**
     * Gets the flash bag.
     *
     * @access protected
     * @return \Symfony\Component\HttpFoundation\Session\Flash\FlashBag
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    protected function getFlashBag()
    {
        return $this->getRequest()->getSession()->getFlashBag();
    }

    /**
     * Sets the welcome flash message.
     *
     * @access protected
     * @return void
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    protected function setFlash()
    {
        $this->getFlashBag()->add('notice', "pi.session.flash.welcom");
    }
}
