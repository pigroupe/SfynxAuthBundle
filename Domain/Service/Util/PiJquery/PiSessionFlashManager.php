<?php
/**
 * This file is part of the <Auth> project.
 *
 * @subpackage   Auth
 * @package    Jquery
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @since 2012-01-11
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\AuthBundle\Domain\Service\Util\PiJquery;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Sfynx\ToolBundle\Twig\Extension\PiJqueryExtension;
use Sfynx\CoreBundle\Layers\Infrastructure\Exception\ExtensionException;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Session flash Jquery plugin
 *
 * @subpackage   Auth
 * @package    Jquery
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
class PiSessionFlashManager extends PiJqueryExtension
{
    /**
     * @var array
     * @static
     */
    static $actions = array('renderfancybox');

    /**
     * Constructor.
     *
     * @param ContainerInterface $container The service container
     * @param TranslatorInterface $translator The service translator
     */
    public function __construct(ContainerInterface $container, TranslatorInterface $translator)
    {
        parent::__construct($container, $translator);
    }

    /**
     * Sets init.
     *
     * @access protected
     * @return void
     *
     * @author Etienne de Longeaux <etienne_delongeaux@hotmail.com>
     */
    protected function init($options = null)
    {
        if ($options == 'fancybox'){
            $this->container->get('sfynx.tool.twig.extension.layouthead')->addJsFile("bundles/sfynxtemplate/js/fancybox/jquery.fancybox.pack.js");
        }
    }

    /**
      * Set progress text for Progress flash dialog.
      *
      * @param    $options    tableau d'options.
      * @access protected
      * @return void
      *
      * @author Etienne de Longeaux <etienne_delongeaux@hotmail.com>
      */
    protected function render($options = null)
    {
        // Options management
        if (!isset($options['action']) || empty($options['action']) || (isset($options['action']) && !in_array(strtolower($options['action']), self::$actions)) )
            throw ExtensionException::optionValueNotSpecified('action', __CLASS__);
        if (!isset($options['dialog-name']) || empty($options['dialog-name']))
            throw ExtensionException::optionValueNotSpecified('dialog-name', __CLASS__);

        $method = strtolower($options['action']) . "Action";
        if (method_exists($this, $method)) {
            return $this->$method($options);
        } else {
            throw ExtensionException::MethodUnDefined($method);
        }
    }

    /**
     * Set progress text for Progress flash dialog.
     *
     * @param    $options    tableau d'options.
     * @access protected
     * @return void
     *
     * @author Etienne de Longeaux <etienne_delongeaux@hotmail.com>
     */
    protected function renderfancyboxAction($options = null)
    {
        // We open the buffer.
        ob_start ();
        ?>
                $(document).ready(function() {
                    // Messages are injected into the overlay fancybox
                    var layout_flash_message = $("#<?php echo $options['dialog-name']; ?>").html();
                    if (layout_flash_message != null && layout_flash_message.length != 0) {
                        $.fancybox({
                        	'wrapCSS': 'fancybox-sfynx',
                            'type': 'inline',
                            'autoDimensions':true,
                            'height': 'auto',
                            'padding':0,
                            'content': layout_flash_message
                        });
                    }
                });
        <?php
        // We retrieve the contents of the buffer.
        $_content_js = ob_get_contents ();
        // We clean the buffer.
        ob_clean ();
        // We close the buffer.
        ob_end_flush ();

        return  $this->renderScript($_content_js, '', 'auth/sessionflash/');
    }
}
