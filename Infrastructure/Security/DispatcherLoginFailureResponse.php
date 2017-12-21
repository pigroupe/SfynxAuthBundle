<?php
/**
 * This file is part of the <Auth> project.
 *
 * @subpackage Dispatcher
 * @package    Event
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @since      2014-07-26
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\AuthBundle\Infrastructure\Security;

use Sfynx\CoreBundle\Layers\Domain\Service\Request\Generalisation\RequestInterface;
use Sfynx\AuthBundle\Infrastructure\Event\ViewObject\ResponseEvent;
use Sfynx\ToolBundle\Util\PiFileManager;

/**
 * Response handler of login failure connection
 *
 * @subpackage Dispatcher
 * @package    Event
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
class DispatcherLoginFailureResponse
{
   /**
    * @var
    */
   protected $store;

   /**
    * @var RequestInterface
    */
   protected $request;

   public $login_failure = true;
   public $login_failure_time_expire = 3600;
   public $login_failure_connection_attempts = 3;
   public $login_failure_cache_dir = '/tmp/failure_login_sfynx';

   /**
    * Constructor.
    *
    * @param $store
    * @param RequestInterface $request
    */
   public function __construct($store, RequestInterface $request)
   {
       $this->store = $store;
       $this->request = $request;
   }

   /**
    * Invoked to modify the controller that should be executed.
    *
    * @param ResponseEvent $event The event
    *
    * @return void
    * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
    */
   public function onPiLoginFailureResponse(ResponseEvent $event)
   {
       if ($this->login_failure && !empty($this->login_failure_time_expire)) {
           $value = $this->getKeyValue();
           if (!empty($value)) {
               if ( $value == 'stop-client') {
               } elseif (intval($value) >= $this->login_failure_connection_attempts) {
                   $this->store->fresh($this->setKey(), 'stop-client');
               } else {
                   $this->store->fresh($this->setKey(), $value+1);
               }
           } else {
               $this->store->set($this->setKey(), 1, $this->getTtl());
           }
       }
   }

   /**
    * We return the value of the key of the failure connection.
    *
    * @return integer
    * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
    */
   public function getKeyValue()
   {
       $this->setCachePath();
       return $this->store->get($this->setKey());
   }

   /**
    * We return the the key of the failure connection.
    *
    * @return string
    * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
    */
   public function setKey()
   {
       // we get the username login
       $username = "";
       if ($this->request->getSession()->has('login-username')) {
           $username = $this->request->getSession()->get('login-username') . '-';
       }
       // we return the key ID of failure connection
       $HTTP_USER_AGENT = $this->request->getServer()->get('HTTP_USER_AGENT');

       return $username . $this->request->getClientIp() . '-' . $HTTP_USER_AGENT;
   }

   /**
    * We return the ttl of the configuration.
    *
    * @return integer
    * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
    */
   public function getTtl()
   {
       // we create ttl of secure login failure by client
       if (is_numeric($this->login_failure_time_expire)) {
           return intVal($this->login_failure_time_expire);
       }
       return 3600;
   }

   /**
    * We set the path of the all failure login filecache.
    *
    * @return void
    * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
    */
   public function setCachePath()
   {
       PiFileManager::mkdirr($this->login_failure_cache_dir);
       $this->store->setPath($this->login_failure_cache_dir);
   }
}
