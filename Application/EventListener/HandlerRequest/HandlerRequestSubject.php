<?php
namespace Sfynx\AuthBundle\Application\EventListener\HandlerRequest;

use SplSubject;
use SplObserver;

use Sfynx\AuthBundle\Infrastructure\Role\Generalisation\RoleFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Sfynx\AuthBundle\Application\EventListener\HandlerRequest\Observer\LayoutConfiguration;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class HandlerRequestSubject.
 * Register the mobile/desktop format.
 *
 * @category   Sfynx\AuthBundle
 * @package    Infrastructure
 * @subpackage EventListener\HandlerRequest
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2015 PI-GROUPE
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    2.3
 * @link       https://github.com/pigroupe/cmf-sfynx/blob/master/web/COPYING.txt
 * @since      2014-07-18
 */
class HandlerRequestSubject implements SplSubject
{
    const OBSERVER_LAYOUT = 0;

    /**
     * List of concrete handlers that can be built using this factory.
     * @var string[]
     */
    protected static $handlerList = [
        self::OBSERVER_LAYOUT => LayoutConfiguration::class,
    ];

    /** @var \SplObserver[] */
    protected $observers = [];
    /** @var RoleFactoryInterface */
    public $role;
    /** @var ContainerInterface */
    public $container;
    /** @var  Request */
    public $request;
    /** @var array */
    public $param;

    /**
     * Constructor.
     *
     * @param RoleFactoryInterface $role
     * @param ContainerInterface $container
     */
    public function __construct(
        RoleFactoryInterface $role,
        ContainerInterface $container
    ) {
        $this->role = $role;
        $this->container = $container;
    }

    /**
     * Invoked to modify the controller that should be executed.
     *
     * @param GetResponseEvent $event The event
     * @access public
     * @return void
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        $this->request = $event->getRequest();

        // Set the heritage.json file if does not exist
        $this->role->setJsonFileRoles(false);

        $this->notify();
    }

    /**
     * Sets parameter template values.
     *
     * @access protected
     * @return void
     */
    public function setParams(array $option)
    {
        $this->param = (object) $option;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(SplObserver $observer)
    {
        $this->observers[] = $observer;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function detach(SplObserver $observer)
    {
        $key = array_search($observer,$this->observers, true);
        if($key){
            unset($this->observers[$key]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function notify()
    {
        $this->observers = array_merge(self::$handlerList, $this->observers);
        foreach ($this->observers as $observer) {
            (new $observer())->update($this);
        }
    }
}
