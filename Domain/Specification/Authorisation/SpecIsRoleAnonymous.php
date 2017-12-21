<?php
namespace Sfynx\AuthBundle\Domain\Specification\Authorisation;

use Sfynx\SpecificationBundle\Specification\AbstractSpecification;
use stdClass;

/**
 * Class SpecIsRoleUser
 *
 * @category Sfynx\AuthBundle
 * @package Domain
 * @subpackage Specification\Authorisation
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
class SpecIsRoleAnonymous extends AbstractSpecification
{
    /**
     * Returns true if the role "anonymous" is granted by the given object. False otherwise.
     *
     * @param stdClass $object
     * @return bool
     */
    public function isSatisfiedBy(stdClass $object): bool
    {
        return $object->value->isGranted('IS_AUTHENTICATED_ANONYMOUSLY');
    }
}
