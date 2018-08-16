<?php
namespace Sfynx\AuthBundle\Application\Validation\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Sfynx\ToolBundle\Twig\Extension\PiToolExtension;
use Sfynx\CoreBundle\Layers\Application\Validation\Type\AbstractType;
use Sfynx\CoreBundle\Layers\Domain\Service\Manager\Generalisation\Interfaces\ManagerInterface;
use Sfynx\AuthBundle\Application\Validation\Type\SecurityPermissionsType;

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
    /** @var PiToolExtension */
    protected $tool;
    /** @var string */
    protected $locale;

    /**
     * Constructor.
     *
     * @param ManagerInterface $manager
     * @param PiToolExtension $tool
     * @param string $locale
     * @return void
     */
    public function __construct(ManagerInterface $manager, PiToolExtension $tool, string $locale)
    {
        parent::__construct($manager);

        $this->tool = $tool;
        $this->locale = $locale;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $groups = $this->data_form['groups'];
        $langCode = $this->data_form['langCode'];

        $format_date = $this->tool->getDatePatternByLocalFunction($this->locale);

        $builder
        ->add('enabled', Type\CheckboxType::class, array(
            'data'  => true,
            'label'	=> 'pi.form.label.field.enabled',
            'label_attr' => array(
                'class'=>"connexion_collection",
            ),
        ))
        ->add('startAt', Type\DateType::class, [
            'widget' => 'single_text', // choice, text, single_text
            'input' => 'datetime',
            'format' => $format_date,// 'dd/MM/yyyy', 'MM/dd/yyyy',
            "attr" => array(
                "class"=>"pi_datepicker",
            ),
            'label_attr' => array(
                'class'=>"connexion_collection",
            ),
            'label' => 'Date d\'ouverture'
        ])
        ->add('endAt', Type\DateType::class, [
            'widget' => 'single_text', // choice, text, single_text
            'input' => 'datetime',
            'format' => $format_date,// 'dd/MM/yyyy', 'MM/dd/yyyy',
            "attr" => array(
                "class"=>"pi_datepicker",
            ),
            'label_attr' => array(
                'class'=>"connexion_collection",
            ),
            'label' => 'Date de fermeture'
        ])
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
        ->add('username', Type\TextType::class, array(
            'label' => 'pi.form.label.field.username',
        ))
        ->add('email', Type\EmailType::class, array(
            'label' => 'pi.form.label.field.email',
        ))
        ->add('name', Type\TextType::class, array(
            'label' => 'pi.form.label.field.name',
        ))
        ->add('nickname', Type\TextType::class, array(
            'label' => 'pi.form.label.field.nickname',
        ))
        ->add('groups', EntityType::class, array(
            'class' => 'SfynxAuthBundle:Group',
//            'query_builder' => function (EntityRepository $er) use ($groups) {
//                return $groups;
//            },
            'label' => 'pi.form.label.field.usergroup',
            'label_attr' => array(
                'class'=>"permission_collection",
            ),
            'property' => 'name',
            'multiple' => true,
            'expanded' => false,
            'required' => true,
        ))
        ->add('permissions', SecurityPermissionsType::class, [
            'multiple' => true,
            'required' => false,
            'label_attr' => array(
                'class'=>"permission_collection",
            ),
        ])
        ->add('plainPassword', Type\RepeatedType::class, [
            'type' => Type\PasswordType::class,
            'options' => array('translation_domain' => 'messages'),
            'first_options' => array('label' => 'form.password', 'label_attr' => ['class'=> "pwd_collection"]),
            'second_options' => array('label' => 'form.password_confirmation', 'label_attr' => ['class'=> "pwd_collection"]),
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
