<?php
/**
 * This file is part of the <Auth> project.
 *
 * @subpackage   Auth
 * @package    Form
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @since 2012-01-07
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\AuthBundle\Application\Validation\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Description of the LangueType form.
 *
 * @subpackage   Auth
 * @package    Form
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
class LangueType extends AbstractType
{
    /**
     * @var string
     */
    protected $_locale;

    /**
     * @var string
     */
    protected $_isEdit;

    /**
     * Constructor
     *
     * @param string  $locale
     * @param boolean $isEdit
     */
    public function __construct($locale, $isEdit = false)
    {
        $this->_locale    = $locale;
        $this->_isEdit    = $isEdit;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $read_only = false;
        if ($this->_isEdit) {
            $read_only = true;
        }

        $builder
            ->add('enabled', 'checkbox', array(
                    'data'  => true,
                     'label'    => 'pi.form.label.field.enabled',
            ))
            ->add('id', 'choice', array(
                    'choices'   => \Sfynx\ToolBundle\Util\PiStringManager::allLocales($this->_locale), //array('fr_FR'=>'fr', 'en_GB'=>'en'),
                    'multiple'    => false,
                    'required'  => true,
                    'empty_value' => 'pi.form.label.select.choose.option',
                    "attr" => array(
                            "class"=>"pi_simpleselect",
                    ),
                    'read_only'    => $read_only,
            ))
            ->add('label')
        ;
    }

    public function getBlockPrefix()
    {
        return 'piapp_adminbundle_languetype';
    }
}
