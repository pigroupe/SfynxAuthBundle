<?php
namespace Sfynx\AuthBundle\Infrastructure\Security;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler;
use Symfony\Component\Security\Http\HttpUtils;
use Sfynx\AuthBundle\Infrastructure\Event\ViewObject\ResponseEvent;
use Sfynx\AuthBundle\Infrastructure\Event\SfynxAuthEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AuthenticationFailureHandler extends DefaultAuthenticationFailureHandler
{
    private $dispatcher;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        HttpKernelInterface $httpKernel,
        HttpUtils $httpUtils,
        array $options,
        LoggerInterface $logger = null
    ) {
        parent::__construct($httpKernel, $httpUtils, $options, $logger);
        $this->dispatcher = $dispatcher;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($request->isXmlHttpRequest()) {
            $response = new JsonResponse([
                'success' => false,
                'message' => $exception->getMessage()
            ]);
        } else {
            $response = parent::onAuthenticationFailure($request, $exception);
        }
        $event_response = new ResponseEvent($response, $request, null, 'fr');
        $this->dispatcher->dispatch(SfynxAuthEvents::HANDLER_LOGIN_FAILURE, $event_response);
        $response       = $event_response->getResponse();

        return $response;
    }
}
