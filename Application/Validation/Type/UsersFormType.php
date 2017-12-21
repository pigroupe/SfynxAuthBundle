<?php
namespace Sfynx\AuthBundle\Application\Validation\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Sfynx\AuthBundle\Application\Validation\Type\SecurityPermissionsType;
use Sfynx\CoreBundle\Layers\Application\Validation\Type\AbstractType;

/**
 * Class UsersFormType.
 *
 * @category   Sfynx\AuthBundle
 * @package    Application
 * @subpackage Validation\Type
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2015 PI-GROUPE
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    2.3
 * @link       http://opensource.org/licenses/gpl-license.php
 * @since      2015-02-16
 */
class UsersFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $groups = $this->data_form['groups'];
        $langCode = $this->data_form['langCode'];

        $builder
        ->add('enabled', CheckboxType::class, array(
            'data'  => true,
            'label'	=> 'pi.form.label.field.enabled',
        ))
        ->add('username', TextType::class, array(
            'label' => 'pi.form.label.field.username',
        ))
        ->add('email', EmailType::class, array(
            'label' => 'pi.form.label.field.email',
        ))
        ->add('langCode', EntityType::class, array(
            'class' => 'SfynxAuthBundle:Langue',
//            'query_builder' => function (EntityRepository $er) use ($langCode) {
//                return $langCode;
//            },
            'property' => 'label',
            "label"    => "pi.form.label.field.language",
            "attr" => array(
                "class"=>"pi_simpleselect",
            ),
            'required'  => false,
        ))
        ->add('name', TextType::class, array(
            'label' => 'pi.form.label.field.name',
        ))
        ->add('nickname', TextType::class, array(
            'label' => 'pi.form.label.field.nickname',
        ))
        ->add('groups', EntityType::class, array(
            'class' => 'SfynxAuthBundle:Group',
//            'query_builder' => function (EntityRepository $er) use ($groups) {
//                return $groups;
//            },
            'label' => 'pi.form.label.field.usergroup',
            'property' => 'name',
            'multiple'	=> true,
            'expanded'  => false,
            'required'  => true,
        ))
        ->add('permissions', SecurityPermissionsType::class, array( 'multiple' => true, 'required' => false))
        ->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'options' => array('translation_domain' => 'messages'),
            'first_options' => array('label' => 'form.password'),
            'second_options' => array('label' => 'form.password_confirmation'),
            'invalid_message' => 'fos_user.password.mismatch',
        ])
      ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->data_class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'user_from';
    }
}
