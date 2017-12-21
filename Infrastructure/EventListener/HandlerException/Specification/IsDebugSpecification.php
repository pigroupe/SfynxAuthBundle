<?php
namespace Sfynx\AuthBundle\Infrastructure\EventListener\HandlerException\Specification;

use stdClass;
use Sfynx\SpecificationBundle\Specification\Logical\AbstractSpecification;

/**
 * Class IsDebugSpecification
 * @category   Sfynx\AuthBundle
 * @package    Infrastructure
 * @subpackage EventListener\HandlerException\Specification
 */
class IsDebugSpecification extends AbstractSpecification
{
    /**
     * always true
     *
     * @param stdClass $object
     * @return bool
     */
    public function isSatisfiedBy(stdClass $object)
    {
        if ($object->factory->getParam('is_debug', false)) {
            return true;
        }
        return false;
    }
}
