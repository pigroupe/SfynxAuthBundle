<?php
namespace Sfynx\AuthBundle\Application\Cqrs\User\Command\Validation\ValidationHandler;

use Sfynx\CoreBundle\Layers\Application\Validation\Generalisation\ValidationHandler\AbstractCommandValidationHandler;
use Sfynx\CoreBundle\Layers\Application\Command\Generalisation\Interfaces\CommandInterface;
use Sfynx\CoreBundle\Layers\Application\Validation\Validator\Constraint\AssocAll;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Optional;
use Symfony\Component\Validator\Constraints\Required;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints\Callback;

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
            ->add('plainPassword', new Optional(new NotBlank()))
//            ->add('groups', new Optional(
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
            ->add('_token', new Optional(new NotBlank()))


            ->add('entityId', new Optional(new NotBlank()))
            ->add('salt', new Optional(new NotBlank()))
            ->add('password', new Optional(new NotBlank()))
            ->add('lastLogin', new Optional(new NotBlank()))
            ->add('confirmationToken', new Optional(new NotBlank()))
            ->add('passwordRequestedAt', new Optional(new NotBlank()))
            ->add('username', new Required(new NotBlank()))
            ->add('usernameCanonical', new Optional(new NotBlank()))
            ->add('name', new Required(new NotBlank()))
            ->add('nickname', new Required(new NotBlank()))
            ->add('email', new Required([
                new NotBlank(),
                new Email()
            ]))
            ->add('emailCanonical', new Optional(new NotBlank()))
            ->add('birthday', new Optional(new NotBlank()))
            ->add('address', new Optional(new NotBlank()))
            ->add('country', new Optional(new NotBlank()))
            ->add('city', new Optional(new NotBlank()))
            ->add('zipCode', new Optional(new NotBlank()))
            ->add('createdAt', new Optional(new NotBlank()))
            ->add('updatedAt', new Optional(new NotBlank()))
            ->add('publishedAt', new Optional(new NotBlank()))
            ->add('archiveAt', new Optional(new NotBlank()))
            ->add('archived', new Optional(new Type('boolean')))
            ->add('expired', new Optional(new Type('boolean')))
            ->add('expiresAt', new Optional(new NotBlank()))
            ->add('locked', new Optional(new Type('boolean')))
            ->add('credentialsExpired', new Optional(new Type('boolean')))
            ->add('credentialsExpireAt', new Optional(new NotBlank()))
            ->add('globalOptIn', new Optional(new Type('boolean')))
            ->add('siteOptIn', new Optional(new Type('boolean')))
            ->add('enabled', new Optional(new Type('boolean')))
            ->add('roles', new Optional(new NotBlank()))
            ->add('permissions', new Optional(new NotBlank()))
            ->add('applicationTokens', new Optional(new NotBlank()))
            ->add('langCode', new Required(new NotBlank()));
        ;
    }
}
