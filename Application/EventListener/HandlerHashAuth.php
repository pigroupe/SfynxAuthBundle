<?php
namespace Sfynx\AuthBundle\Application\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class HandlerHashAuth
{
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$token = $event->getRequest()->attributes->get('auth_token')) {
            return;
        }
        $response = $event->getResponse();
        // create a hash and set it as a response header
        $hash = sha1($response->getContent().$token);
        $response->headers->set('X-CONTENT-HASH', $hash);
        $event->setResponse($response);
    }
}
