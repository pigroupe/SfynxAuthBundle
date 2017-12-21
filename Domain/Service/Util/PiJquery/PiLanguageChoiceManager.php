<?php
/**
 * This file is part of the <Auth> project.
 *
 * @subpackage   Auth
 * @package    Jquery
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @since 2012-02-29
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\AuthBundle\Domain\Service\Util\PiJquery;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Sfynx\ToolBundle\Twig\Extension\PiJqueryExtension;
use Sfynx\CoreBundle\Layers\Infrastructure\Exception\ExtensionException;

/**
 * Backstretch Jquery plugin
 *
 * @subpackage   Auth
 * @package    Jquery
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
class PiLanguageChoiceManager extends PiJqueryExtension
{
    /**
     * @var array
     * @static
     */
    static $actions = array('renderbox','renderlist');

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
    protected function init($options = null) {
        // css
        $this->container->get('sfynx.tool.twig.extension.layouthead')->addCssFile("bundles/sfynxtemplate/js/languagechoice/css/languagechoice.css");
    }

    /**
     * Set the render of the list of languages.
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
    		$options['action'] = "renderbox";

    	$method = strtolower($options['action']) . "Action";
    	if (method_exists($this, $method)) {
    		return $this->$method($options);
    	} else {
    		return $this->renderbox($options);
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
    protected function renderboxAction($options = null)
    {
        if (!isset($options['class']) || empty($options['class']))
            throw ExtensionException::optionValueNotSpecified('class', __CLASS__);
        if (!isset($options['img-arrow']) || empty($options['img-arrow']))
            throw ExtensionException::optionValueNotSpecified('img-arrow', __CLASS__);

        $em         = $this->container->get('doctrine')->getManager();
        $locale        = $this->container->get('request_stack')->getCurrentRequest()->getLocale();
        $entities     = $em->getRepository("SfynxAuthBundle:Langue")->getAllEnabled($locale, 'object', false);

        // if the file doesn't exist, we call an exception
        $img = "bundles/sfynxtemplate/images/arrow/".$options['img-arrow'];
        $is_file_exist = realpath($this->container->get('kernel')->getRootDir(). '/../web/' . $img);
        if (!$is_file_exist)
            throw ExtensionException::FileUnDefined('img', __CLASS__);

        $Urlpath = $this->container->get('templating.helper.assets')->getUrl($img);

        // We open the buffer.
        ob_start ();
        ?>
                $("#selected_language").bind("click", function(){
                    $(this).next('ul').toggle();    //.slideToggle();
                });
        <?php
        // We retrieve the contents of the buffer.
        $_content_js = ob_get_contents ();
        // We clean the buffer.
        ob_clean ();
        // We close the buffer.
        ob_end_flush ();


        // We open the buffer.
        ob_start ();
        ?>
                <div class="<?php echo $options['class']; ?>" id="language_menu" style="background:#238BDB url('<?php echo $Urlpath; ?>') no-repeat 95% 10px;">
                    <div id="selected_language">
                        <?php echo locale_get_display_name(strtolower($locale), strtolower($locale)); ?>
                    </div>
                    <ul>
                        <!-- <li id="selected_language"><?php echo locale_get_display_name(strtolower($locale), strtolower($locale)); ?></li> -->
                        <?php foreach($entities as $key=>$entity){
                            $url = $this->container->get('router')->generate('pi_layout_choisir_langue', array('langue' => $entity->getId()));
                            if ($entity->getId() != $locale){

                            $name_language = locale_get_display_name(strtolower($entity->getId()), strtolower($locale));
                        ?>
                        <li>
                            <a href="<?php echo $url; ?>"  id="lang_select_<?php echo $entity->getId(); ?>" title="<?php echo $name_language; ?>"><?php echo $name_language; ?></a>
                        </li>
                        <?php } } ?>
                    </ul>
                    <!-- <span id="language_menu_scroll" style="background:url('<?php echo $Urlpath; ?>') no-repeat left center;"></span> -->
                </div>
        <?php
        // We retrieve the contents of the buffer.
        $_content_html = ob_get_contents ();
        // We clean the buffer.
        ob_clean ();
        // We close the buffer.
        ob_end_flush ();

        return  $this->renderScript($_content_js, $_content_html, 'auth/languagechoice/');
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
    protected function renderlistAction($options = null)
    {
        if (!isset($options['class']) || empty($options['class']))
            throw ExtensionException::optionValueNotSpecified('class', __CLASS__);
        if (!isset($options['img-arrow']) || empty($options['img-arrow']))
            throw ExtensionException::optionValueNotSpecified('img-arrow', __CLASS__);

        $em       = $this->container->get('doctrine')->getManager();
        $locale   = $this->container->get('request_stack')->getCurrentRequest()->getLocale();

        $entities = $em->getRepository("SfynxAuthBundle:Langue")->getAllEnabled($locale, 'object', false);

        // if the file doesn't exist, we call an exception
        $img = "bundles/sfynxtemplate/images/arrow/".$options['img-arrow'];
        $is_file_exist = realpath($this->container->get('kernel')->getRootDir(). '/../web/' . $img);
        if (!$is_file_exist)
            throw ExtensionException::FileUnDefined('img', __CLASS__);

        $Urlpath = $this->container->get('templating.helper.assets')->getUrl($img);

    	// We open the buffer.
    	ob_start ();
    	?>
					<div class="clear">&nbsp;</div>
					<div class="acc-line">&nbsp;</div>
                    <a href="" class="language-subtitle">Local language</a>

                    <div class="clear">&nbsp;</div>
					<div class="acc-line">&nbsp;</div>
                    <a href=""><?php echo locale_get_display_name(strtolower($locale), strtolower($locale)); ?></a>

					<div class="clear">&nbsp;</div>
					<div class="acc-line">&nbsp;</div>
                    <a href="" class="language-subtitle"><?php echo $this->container->get('translator')->trans('pi.form.label.field.other'); ?></a>

                    <?php foreach($entities as $key=>$entity){
                        $url = $this->container->get('router')->generate('pi_layout_choisir_langue', array('langue' => $entity->getId()));
                        if ($entity->getId() != $locale){

                        $name_language = locale_get_display_name(strtolower($entity->getId()), strtolower($locale));
                    ?>
					<div class="clear">&nbsp;</div>
					<div class="acc-line">&nbsp;</div>
                    <a href="<?php echo $url; ?>"  id="lang_select_<?php echo $entity->getId(); ?>" title="<?php echo $name_language; ?>"><?php echo $name_language; ?></a>
                    <?php } } ?>
        <?php
        // We retrieve the contents of the buffer.
        $_content = ob_get_contents ();
        // We clean the buffer.
        ob_clean ();
        // We close the buffer.
        ob_end_flush ();

        return $_content;
    }

}
