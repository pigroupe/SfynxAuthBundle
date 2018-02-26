<?php
namespace Sfynx\AuthBundle\Application\Cqrs\User\Command\Validation\ValidationHandler;

use Sfynx\CoreBundle\Layers\Application\Validation\Generalisation\ValidationHandler\AbstractCommandValidationHandler;
use Sfynx\CoreBundle\Layers\Application\Command\Generalisation\Interfaces\CommandInterface;
use Sfynx\CoreBundle\Layers\Application\Validation\Validator\Constraint\AssocAll;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class FormCommandValidationHandler.
 *
 * @category   Sfynx\AuthBundle
 * @package    Application
 * @subpackage Cqrs\User\Command\Validation\ValidationHandler
 */
class FormCommandValidationHandler extends AbstractCommandValidationHandler
{
    /** @var bool */
    protected $skipArrayValidator = ['groups'];

    protected function initConstraints(CommandInterface $command): void
    {
        # https://stackoverflow.com/questions/16050240/how-can-i-validate-array-keys-using-symfony-validation
        $this
            ->add('plainPassword', new Assert\Optional(new Assert\NotBlank()))
//            ->add('groups', new Assert\Optional(
//                new AssocAll(array(
//                    new Callback(
//                        array(
//                            'methods' => array(function($item, ExecutionContextInterface $context) {
//                                    $key = $item[0];
//                                    $value = $item[1];
//
////                                    $valueKey = preg_replace('/[^0-9]/','', $value);
////                                    if ($valueKey != 7) {
////                                        $context->addViolationAt('email', sprintf('E-Mail %s Has Has Key 7',$value), array(), null);
////                                    }
//                                }
//                            )
//                        )
//                    )
//                ))
//            ))
            ->add('_token', new Assert\Optional(new Assert\NotBlank()))


            ->add('entityId', new Assert\Optional([
                new Assert\NotBlank(),
                new Assert\Regex('/^[0-9]+$/')
            ]))
            ->add('salt', new Assert\Optional(new Assert\NotBlank()))
            ->add('password', new Assert\Optional(new Assert\NotBlank()))
            ->add('lastLogin', new Assert\Optional(new Assert\NotBlank()))
            ->add('confirmationToken', new Assert\Optional(new Assert\NotBlank()))
            ->add('passwordRequestedAt', new Assert\Optional(new Assert\NotBlank()))
            ->add('username', new Assert\Required(new Assert\NotBlank()))
            ->add('usernameCanonical', new Assert\Optional(new Assert\NotBlank()))
            ->add('name', new Assert\Required(new Assert\NotBlank()))
            ->add('nickname', new Assert\Required(new Assert\NotBlank()))
            ->add('email', new Assert\Required([
                new Assert\NotBlank(),
                new Assert\Email()
            ]))
            ->add('emailCanonical', new Assert\Optional(new Assert\NotBlank()))
            ->add('birthday', new Assert\Optional(new Assert\NotBlank()))
            ->add('address', new Assert\Optional(new Assert\NotBlank()))
            ->add('country', new Assert\Optional(new Assert\NotBlank()))
            ->add('city', new Assert\Optional(new Assert\NotBlank()))
            ->add('zipCode', new Assert\Optional(new Assert\NotBlank()))
            ->add('createdAt', new Assert\Optional([
                new Assert\NotBlank(),
//                new Assert\DateTime(['format' => 'Y-m-d'])
            ]))
            ->add('updatedAt', new Assert\Optional([
                new Assert\NotBlank(),
//                new Assert\DateTime(['format' => 'Y-m-d'])
            ]))
            ->add('publishedAt', new Assert\Optional([
                new Assert\NotBlank(),
//                new Assert\DateTime(['format' => 'Y-m-d'])
            ]))
            ->add('archiveAt', new Assert\Optional([
                new Assert\NotBlank(),
//                new Assert\DateTime(['format' => 'Y-m-d'])
            ]))
            ->add('archived', new Assert\Optional(new Assert\Type('boolean')))
            ->add('expired', new Assert\Optional(new Assert\Type('boolean')))
            ->add('expiresAt', new Assert\Optional([
                new Assert\NotBlank(),
//                new Assert\DateTime(['format' => 'Y-m-d'])
            ]))
            ->add('locked', new Assert\Optional(new Assert\Type('boolean')))
            ->add('credentialsExpired', new Assert\Optional(new Assert\Type('boolean')))
            ->add('credentialsExpireAt', new Assert\Optional(new Assert\NotBlank()))
            ->add('globalOptIn', new Assert\Optional(new Assert\Type('boolean')))
            ->add('siteOptIn', new Assert\Optional(new Assert\Type('boolean')))
            ->add('enabled', new Assert\Optional(new Assert\Type('boolean')))
            ->add('roles', new Assert\Optional(new Assert\NotBlank()))
            ->add('permissions', new Assert\Optional(new Assert\NotBlank()))
            ->add('applicationTokens', new Assert\Optional(new Assert\NotBlank()))
            ->add('langCode', new Assert\Required(new Assert\NotBlank()));
        ;
    }
}
