<?php
namespace Sfynx\AuthBundle\Application\EventListener\HandlerProfiler\Handler;

use Sfynx\AuthBundle\Application\EventListener\Profiler\Generalisation\ResponseProfilerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class JsonResponseProfiler
 * @category   Sfynx\AuthBundle
 * @package    Infrastructure
 * @subpackage EventListener\HandlerProfiler\Handler
 */
class JsonResponseProfiler implements ResponseProfilerInterface
{
    protected $response;

    /**
     * JsonResponseProfiler constructor.
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrettyPrintClass()
    {
        return 'prettyprint lang-js';
    }

    /**
     * {@inheritdoc}
     */
    public function parseContent()
    {
        return htmlspecialchars(json_encode(json_decode($this->response->getContent()), JSON_PRETTY_PRINT));
    }
}
