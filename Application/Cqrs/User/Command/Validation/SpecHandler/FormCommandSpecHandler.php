<?php
namespace Sfynx\AuthBundle\Application\Cqrs\User\Command\Validation\SpecHandler;

use Sfynx\SpecificationBundle\Specification\Generalisation\InterfaceSpecification;
use Sfynx\SpecificationBundle\Specification\Logical\XorSpec;
use Sfynx\SpecificationBundle\Specification\Logical\TrueSpec;
use Sfynx\CoreBundle\Layers\Application\Validation\Generalisation\SpecHandler\AbstractCommandSpecHandler;
use Sfynx\AuthBundle\Domain\Specification\Authorisation\SpecIsRoleAdmin;
use Sfynx\AuthBundle\Domain\Specification\Authorisation\SpecIsRoleUser;
use Sfynx\AuthBundle\Domain\Specification\Authorisation\SpecIsRoleAnonymous;

/**
 * Class UpdateCommandValidationHandler.
 *
 * @category   Sfynx\AuthBundle
 * @package    Application
 * @subpackage Cqrs\User\Command\Validation\SpecHandler
 */
class FormCommandSpecHandler extends AbstractCommandSpecHandler
{
    /**
     * @return XorSpec
     */
    public function initSpecifications(): InterfaceSpecification
    {
        return new TrueSpec();
    }
}
