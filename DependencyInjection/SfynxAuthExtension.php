<?php
/**
 * This file is part of the <Auth> project.
 *
 * @category   Auth
 * @package    DependencyInjection
 * @subpackage Extension
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
namespace Sfynx\AuthBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension,
    Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Loader,
    Symfony\Component\Config\FileLocator;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * @category   Bundle
 * @package    Sfynx\AuthBundle
 * @subpackage DependencyInjection
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2015 PI-GROUPE
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    2.3
 * @link       http://opensource.org/licenses/gpl-license.php
 * @since      2015-02-16
 */
class SfynxAuthExtension extends Extension{

    public function load(array $config, ContainerBuilder $container)
    {
        $loader  = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('serviceformtype.xml');

        // we load all services
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('repository/group.yml');
        $loader->load('repository/langue.yml');
//        $loader->load('repository/layout.yml');
//        $loader->load('repository/permission.yml');
//        $loader->load('repository/ressource.yml');
//        $loader->load('repository/role.yml');
        $loader->load('services.yml');
        $loader->load('services_cmd.yml');
        $loader->load('services_cmfconfig.yml');
        $loader->load('controllers.yml');

        $loader->load('repository/user.yml');
        $loader->load('controller/user/user_command.yml');
        $loader->load('controller/user/user_query.yml');

        // we load config
        $configuration = new Configuration();
        $config  = $this->processConfiguration($configuration, $config);

        /*
         * Firewall config parameter
         */
        if (isset($config['firewall_name'])) {
            $container->setParameter('sfynx.auth.firewall_name', $config['firewall_name']);
        }

        /*
         * Mapping config parameter
         */
        if (isset($config['mapping']['entities'])) {
            $container->setParameter("sfynx.auth.mapping.entities", $config['mapping']['entities']);
            foreach ($config['mapping']['entities'] as $entity => $param) {
                $container->setParameter("sfynx.auth.mapping.{$entity}.class", $param['class']);
            }
        }

        /**
         * Login failure config parameter
         */
        if (isset($config['loginfailure']['authorized'])) {
            $container->setParameter('sfynx.auth.loginfailure.authorized', $config['loginfailure']['authorized']);
        }
        if (isset($config['loginfailure']['time_expire'])) {
            $container->setParameter('sfynx.auth.loginfailure.time_expire', $config['loginfailure']['time_expire']);
        }
        if (isset($config['loginfailure']['connection_attempts'])) {
            $container->setParameter('sfynx.auth.loginfailure.connection_attempts', $config['loginfailure']['connection_attempts']);
        }
        if (isset($config['loginfailure']['cache_dir'])) {
            $container->setParameter('sfynx.auth.loginfailure.cache_dir', $config['loginfailure']['cache_dir']);
        }

        /**
         * Locale config parameter
         */
        if (isset($config['locale']['authorized'])) {
            $container->setParameter('sfynx.auth.locale.authorized', $config['locale']['authorized']);
        } else {
            $container->setParameter('sfynx.auth.locale.authorized', array());
        }
        if (isset($config['locale']['cache_file'])) {
            $container->setParameter('sfynx.auth.locale.cache_file', $config['locale']['cache_file']);
        }

        /**
         * Browser config parameter
         */
        if (isset($config['browser'])){
            if (isset($config['browser']['switch_language_authorized'])) {
                $container->setParameter('sfynx.auth.browser.switch_language_authorized', $config['browser']['switch_language_authorized']);
            }
            if (isset($config['browser']['switch_layout_mobile_authorized'])) {
                $container->setParameter('sfynx.auth.browser.switch_layout_mobile_authorized', $config['browser']['switch_layout_mobile_authorized']);
            }
        }

        /**
         * Redirection login config
         */
        if (isset($config['default_login_redirection'])){
            if (isset($config['default_login_redirection']['redirection'])) {
                $container->setParameter('sfynx.auth.login.redirect', $config['default_login_redirection']['redirection']);
            }
            if (isset($config['default_login_redirection']['template'])) {
                $container->setParameter('sfynx.auth.login.template', $config['default_login_redirection']['template']);
            }
        }

        /**
         * Layout config parameter
         */
        if (isset($config['default_layout'])){
            if (isset($config['default_layout']['init_pc'])){
                if (isset($config['default_layout']['init_pc']['template'])) {
                    $container->setParameter('sfynx.auth.layout.init.pc.template', $config['default_layout']['init_pc']['template']);
                }
            }
            if (isset($config['default_layout']['init_mobile'])){
                if (isset($config['default_layout']['init_mobile']['template'])) {
                    $container->setParameter('sfynx.auth.layout.init.mobile.template', $config['default_layout']['init_mobile']['template']);
                }
            }
        }
    }

    public function getAlias()
    {
        return 'sfynx_auth';
    }
}
