<?php
/**
 * This file is part of the <Auth> project.
 *
 * @subpackage   Auth
 * @package    Util
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @since 2012-01-18
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\AuthBundle\Domain\Service\Util;

use Sfynx\AuthBundle\Domain\Service\Util\Generalisation\PiLocaleManagerBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Description of the locale manager
 *
 * <code>
 *     $fileFormatter    = $this-container->get('sfynx.Auth.locale_manager');
 * </code>
 *
 * @subpackage   Auth
 * @package    Util
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
class PiLocaleManager implements PiLocaleManagerBuilderInterface
{
    protected $path_json_file;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container The service container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container      = $container;
        $this->path_json_file = $container->getParameter('sfynx.auth.locale.cache_file');
    }

    /**
     * Getting the Browser Default Language.
     *
     * @param string $deflang
     *
     * @return string
     * @access private
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function parseDefaultLanguage($deflang = "fr")
    {
        if (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])) {
            $http_accept = $_SERVER["HTTP_ACCEPT_LANGUAGE"];
        } else {
            $http_accept = NULL;
        }
        if (isset($http_accept) && strlen($http_accept) > 1)  {
            # Split possible languages into array
            $x = explode(",",$http_accept);

            foreach ($x as $val) {
                #check for q-value and create associative array. No q-value means 1 by rule
                if (preg_match("/(.*);q=([0-1]{0,1}\.\d{0,4})/i",$val,$matches))
                    $lang[$matches[1]] = (float)$matches[2];
                else
                    $lang[$val] = 1.0;
            }

            #return default language (highest q-value)
            $qval = 0.0;
            foreach ($lang as $key => $value) {
                if ($value > $qval) {
                    $qval = (float)$value;
                    $deflang = $key;
                }
            }
        }
        //return strtolower(substr($deflang,0,2));
        return str_replace('-', '_', $deflang);
    }

    /**
     * Getting all locales of the CMF.
     *
     *  <code>
     *    $allLocales = $this->container->get('sfynx.auth.locale_manager')->getAllLocales();
     *  </code>
     *
     * @param boolean $all
     * @return array
     * @access public
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function getAllLocales($all = false)
    {
        $return = true;
    	// we set the json file if does not exist
        if (!realpath($this->path_json_file)) {
            $return = $this->setJsonFileLocales();
        }
        $locales = array();
        if ($return) {
            // we get all locale values
            $entities     = json_decode(file_get_contents($this->path_json_file), true);
            foreach($entities as $locale){
                if ($all) {
                    $locales[] = $locale;
                } else {
            	    $locales[] = $locale['id'];
                }
            }
        }

    	return $locales;
    }

    /**
     * Create the json file with all languages information.
     *
     * @return boolean
     * @access public
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function setJsonFileLocales()
    {
        $allLocales = $this->container->getParameter('sfynx.auth.locale.authorized');
        if (is_array($allLocales) && (count($allLocales) >= 1)) {
            return $allLocales;
        } else {
            $db = $this->container->get('sfynx.tool.manager.db');
            $req = "
                SELECT
                    *
                FROM
                    pi_langue as a
                WHERE
    		    a.enabled = 1
    		";
            $entities     = $db->executeQuery($req, array());

            return file_put_contents($this->path_json_file, json_encode($entities,JSON_UNESCAPED_UNICODE));
        }
    }
}
