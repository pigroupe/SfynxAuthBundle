<?php
namespace Sfynx\AuthBundle\Application\Validation\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Description of the LayoutType form.
 *
 * @category   Sfynx\AuthBundle
 * @package    Application
 * @subpackage Validation\Type
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @since 2012-01-07
 */
class LayoutType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('enabled', 'checkbox', array(
                'data'  => true,
                'label'    => 'pi.form.label.field.enabled',
            ))
            ->add('name', 'text', array(
                'label' => "pi.form.label.field.name"
             ))
            ->add('filePc')
            ->add('fileMobile')
            ->add('configXml')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'piapp_adminbundle_layouttype';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Sfynx\AuthBundle\Domain\Entity\Layout'
        ]);
    }
}
