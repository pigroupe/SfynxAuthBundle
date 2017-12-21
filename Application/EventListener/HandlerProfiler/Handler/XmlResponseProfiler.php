<?php
namespace Sfynx\AuthBundle\Application\EventListener\HandlerProfiler\Handler;

use Sfynx\AuthBundle\Application\EventListener\Profiler\Generalisation\ResponseProfilerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class XmlResponseProfiler
 * @category   Sfynx\AuthBundle
 * @package    Infrastructure
 * @subpackage EventListener\HandlerProfiler\Handler
 */
class XmlResponseProfiler implements ResponseProfilerInterface
{
    protected $response;

    /**
     * XmlResponseProfiler constructor.
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
        return 'prettyprint lang-xml';
    }

    /**
     * {@inheritdoc}
     */
    public function parseContent()
    {
        return htmlspecialchars($this->response->getContent());
    }
}
