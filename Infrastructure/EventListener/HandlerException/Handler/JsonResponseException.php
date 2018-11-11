<?php
namespace Sfynx\AuthBundle\Infrastructure\EventListener\HandlerException\Handler;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Exception\RequestExceptionInterface;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

use Sfynx\AuthBundle\Infrastructure\EventListener\HandlerException\Generalisation\ResponseExceptionInterface;
use Sfynx\AuthBundle\Infrastructure\EventListener\HandlerException\HandlerExceptionFactory;
use Sfynx\CoreBundle\Layers\Infrastructure\Exception\ApiProblemException;
use Sfynx\CoreBundle\Layers\Infrastructure\ApiProblem\ApiProblem;

/**
 * Class JsonResponseException
 * @category   Sfynx\AuthBundle
 * @package    Infrastructure
 * @subpackage EventListener\HandlerException\Handler
 *
 * <code>
 *
 *      $data = json_decode($request->getContent(), true);
 *      if ($data === null) {
 *          $apiProblem = new ApiProblem(Response::HTTP_BAD_REQUEST, ApiProblem::TYPE_INVALID_REQUEST_BODY_FORMAT);
 *          throw new ApiProblemException($apiProblem);
 *      }
 *
 * </code>
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
        $apiProblem = $this->getApiProblrem($this->factory->exception);

        // new Response object
        $response = new JsonResponse(
            $apiProblem->toArray(),
            $apiProblem->getStatusCode(),
            $apiProblem->getHeaders()
        );

        $response->headers->set('Content-Type', 'application/problem+json');

        return $response;
    }

    /**
     * @param Exception $e
     * @return ApiProblem
     */
    protected function getApiProblrem(Exception $e): ApiProblem
    {
        $headers = [];

        if ($e instanceof ApiProblemException) {
            $this->apiProblem = $e->getApiProblem();
        } else {
            if ($e instanceof HttpExceptionInterface) {
                $statusCode = $e->getStatusCode();
                $headers = $e->getHeaders();
            } elseif ($e instanceof RequestExceptionInterface) {
                $statusCode = Response::HTTP_BAD_REQUEST;
            } else {
                $statusCode = $this->factory::getHttpCode($exception);
            }

            $this->apiProblem = new ApiProblem(
                $statusCode
            );
        }

        // Set instance value
        $this->apiProblem->setExtraData('instance', $this->factory->request->getPathInfo());

        // Set context value
        $context = [
            'class_name' => \get_class($e),
            'called' => [
                'file' => $e->getTrace()[0]['file'],
                'line' => $e->getTrace()[0]['line'],
            ],
            'occurred' => [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ],
        ];
        $this->apiProblem->setExtraData('context', $context);

        // If the Exception type managed data, set it to the body.
        $data =  \method_exists($exception, 'getData') ? $exception->getData() : null;
        if (null !== $data) {
            $this->apiProblem->setExtraData('data', $data);
        }

        // Set Headers value
        $headers['Content-Type'] = 'application/problem+json';
        $headers['X-Content-Type-Options'] = 'nosniff';
        $headers['X-Frame-Options'] = 'deny';
        $this->apiProblem->setHeaders($headers);
    }
}
