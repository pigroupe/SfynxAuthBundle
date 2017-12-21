<?php
namespace Sfynx\AuthBundle\Application\EventListener\HandlerProfiler;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

use Sfynx\SpecificationBundle\Specification\Logical\AndSpec;
use Sfynx\SpecificationBundle\Specification\Logical\TrueSpec;
use Sfynx\AuthBundle\Infrastructure\Exception\InvalidArgumentException;
use Sfynx\AuthBundle\Application\EventListener\HandlerProfiler\HandlerProfilerBuild;
use Sfynx\AuthBundle\Application\EventListener\HandlerProfiler\Specification\NoProfilerSpecification;

/**
 * Class HandlerProfilerFactory
 * @category   Sfynx\AuthBundle
 * @package    Infrastructure
 * @subpackage EventListener\HandlerProfiler
 */
class HandlerProfilerFactory
{
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $specs = new NoProfilerSpecification();
        if ($specs->isSatisfiedBy((object)['event' => $event])) {
            return;
        }

        try {
            $response = $event->getResponse();
            $responseProfiler = HandlerProfilerBuild::build(
                $event->getRequest()->getRequestFormat(),
                $response
            );
        } catch (InvalidArgumentException $e) {
            //In this case, we want to ignore if the request is bad or the factory cannot build the responseProfiler.
            return;
        }

        $prettyPrintLang = $responseProfiler->getPrettyPrintClass();
        $content         = $responseProfiler->parseContent();

        $newcontent = <<<HTML
<html><body>
    <pre class="{$prettyPrintLang}">{$content}</pre>
</body></html>
HTML;

        $response->setContent($newcontent);
        $response->headers->set('Content-Type', 'text/html; charset=UTF-8');
        $event->getRequest()->setRequestFormat('html');
        $event->setResponse($response);
    }
}
