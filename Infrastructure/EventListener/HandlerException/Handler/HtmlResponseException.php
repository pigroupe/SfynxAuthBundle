<?php
namespace Sfynx\AuthBundle\Infrastructure\EventListener\HandlerException\Handler;

use Symfony\Component\HttpFoundation\Response;
use Sfynx\AuthBundle\Infrastructure\EventListener\HandlerException\Generalisation\ResponseExceptionInterface;
use Sfynx\AuthBundle\Infrastructure\EventListener\HandlerException\HandlerExceptionFactory;
use Sfynx\AuthBundle\Infrastructure\EventListener\HandlerException\Specification\IsDebugSpecification;
use Sfynx\ToolBundle\Util\PiFileManager;
use Sfynx\AuthBundle\Infrastructure\Exception\InvalidArgumentException;

/**
 * Class HtmlResponseException
 * @category   Sfynx\AuthBundle
 * @package    Infrastructure
 * @subpackage EventListener\HandlerException\Handler
 */
class HtmlResponseException implements ResponseExceptionInterface
{
    /** @var HandlerExceptionFactory */
    protected $factory;

    /**
     * {@inheritdoc}
     */
    public function __construct(HandlerExceptionFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse()
    {
        $specs = new IsDebugSpecification();
        if ($specs->isSatisfiedBy((object)['factory' => $this->factory])) {
            throw InvalidArgumentException::invalidArgument();
        }

        // exception object
        $httpCode  = $this->factory::getHttpCode($this->factory->exception);

        // new Response object
        $response  = new Response();
        $response->setStatusCode($httpCode);

        // HttpExceptionInterface also holds header details.
        if (method_exists($this->factory->exception, 'getHeaders')) {
            $response->headers->replace($this->factory->exception->getHeaders());
        }
        // set the new $response object to the $event
        // server side caching
        $response->setSharedMaxAge(3600);

        // browser side caching
        $response->setMaxAge(3600);

        if (($error_html = $this->factory->getParam('error_html', '')) != '') {
            $response->setContent($this->factory->templating->render(
                $error_html,
                ['exception' => $exception]
            ));
        } else {
            $route_name = $this->factory->getParam('error_route_name', 'error_404');
            $url = $this->factory->router->generate($route_name, [
                'locale' => $this->factory->locale,
            ]);
            $getUriForPath = $this->factory->request->getUriForPath('');
            if (($error_uri_for_path = $this->factory->getParam('error_uri_for_path', '')) != '') {
                $getUriForPath = $error_uri_for_path;
            }
            $content = PiFileManager::getCurl('/' . $url, null, null, $getUriForPath);
            $response->setContent($content);
//                $requestDuplicate = $this->request->duplicate(null, null, ['_controller' => 'sfynx.auth.controller.error']);
//                $this->kernel = $event->getKernel();
//                $response = $this->kernel->handle($requestDuplicate, HttpKernelInterface::SUB_REQUEST);
        }
        return $response;
    }
}
