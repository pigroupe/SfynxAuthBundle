<?php
namespace Sfynx\AuthBundle\Application\EventListener\HandlerProfiler\Specification;

use Sfynx\SpecificationBundle\Specification\Logical\AbstractSpecification;

/**
 * Class NoProfilerSpecification
 * @category   Sfynx\AuthBundle
 * @package    Infrastructure
 * @subpackage EventListener\HandlerProfiler\Specification
 */
class NoProfilerSpecification extends AbstractSpecification
{
    /**
     * always true
     *
     * @param $object
     * @return bool
     */
    public function isSatisfiedBy(\stdClass $object)
    {
        $request      = $object->event->getRequest();
        $headerAccept = (string)$request->headers->get('Accept');
        $condition    = (false !== strpos($headerAccept, 'text/html') || false !== strpos($headerAccept, '*/*'));

        if (!$object->event->isMasterRequest()
            || !($request->query->has('_profiler') && $request->query->get('_profiler'))
            || !($request->headers->has('Accept') && $condition)
        ) {
            return true;
        }
        return false;
    }
}
