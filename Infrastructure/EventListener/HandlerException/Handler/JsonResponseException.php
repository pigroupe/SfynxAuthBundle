<?php
namespace Sfynx\AuthBundle\Infrastructure\EventListener\HandlerException\Handler;

use Sfynx\AuthBundle\Infrastructure\EventListener\HandlerException\Generalisation\ResponseExceptionInterface;
use Sfynx\AuthBundle\Infrastructure\EventListener\HandlerException\HandlerExceptionFactory;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class JsonResponseException
 * @category   Sfynx\AuthBundle
 * @package    Infrastructure
 * @subpackage EventListener\HandlerException\Handler
 */
class JsonResponseException implements ResponseExceptionInterface
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
        $exception = $this->factory->exception;
        $message = $exception->getMessage();
        $httpCode = $this->factory::getHttpCode($exception);

        // If the Exception type managed data, set it to the body.
        $data = method_exists($exception, 'getData') ? $exception->getData() : '';

        $body = [
            'status' => 'error',
            'code' => $httpCode,
            'message' => $message,
            'results' => $data
        ];

        // HttpExceptionInterface is a special type of exception that holds status code.
        if (method_exists($exception, 'getStatusCode')) {
            $httpCode = $exception->getStatusCode();
        }

        // new Response object
        $response = new JsonResponse(array_filter($body), $httpCode);

        // HttpExceptionInterface also holds header details.
        if (method_exists($exception, 'getHeaders')) {
            $response->headers->replace($exception->getHeaders());
        }

        return $response;
    }
}
