<?php
namespace Sfynx\AuthBundle\Application\EventListener\Profiler\Generalisation;

use Symfony\Component\HttpFoundation\Response;

/**
 * Interface ResponseProfilerInterface
 * @category   Sfynx\AuthBundle
 * @package    Infrastructure
 * @subpackage EventListener\HandlerProfiler\Handler
 */
interface ResponseProfilerInterface
{
    /**
     * Return the HTML class name of the pretty-print language style.
     *
     * @return string
     */
    public function getPrettyPrintClass();

    /**
     * Parse the content of the response to fit with the expected format.
     *
     * @param Response $response
     * @return string
     */
    public function parseContent(Response $response);
}
