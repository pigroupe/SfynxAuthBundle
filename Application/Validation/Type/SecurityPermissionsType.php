<?php
/**
 * This file is part of the <Admin> project.
 *
 * @category   Auth
 * @package    Form
 * @subpackage Type
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2015 PI-GROUPE
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    2.3
 * @link       http://opensource.org/licenses/gpl-license.php
 * @since      2015-02-16
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\AuthBundle\Application\Validation\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Security Permissions
 *
 * @category   Auth
 * @package    Form
 * @subpackage Type
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2015 PI-GROUPE
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    2.3
 * @link       http://opensource.org/licenses/gpl-license.php
 * @since      2015-02-16
 */
class SecurityPermissionsType extends AbstractType
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $attr = $view->vars['attr'];
        $view->vars['attr'] = $attr;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $permissions = [];
           //$query = $this->pool->getContainer()->get('sfynx.auth.repository')->findAllEnabled('permission');
           $query = $this->container->get('sfynx.auth.repository')->getRepository('permission')->getAvailablePermissions();
           foreach ($query as $field => $value) {
           if (isset($value['name']) && !isset($permission[ $value['name'] ])) {
               $permissions[ $value['name'] ] = $value['name'];
           }
        }

        $resolver->setDefaults([
                'choices' => function (Options $options, $parentChoices) use ($permissions) {
                    return empty($parentChoices) ? $permissions : [];
                },
        ]);
    }

    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sfynx_security_permissions';
    }
}
