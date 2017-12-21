<?php
/**
 * This file is part of the <Auth> project.
 *
 * @category   Auth
 * @package    DependencyInjection
 * @subpackage Configuration
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

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
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
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sfynx_auth');
        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        $this->addMapping($rootNode);
        $this->addLoginFailureConfig($rootNode);
        $this->addLocaleConfig($rootNode);
        $this->addBrowserConfig($rootNode);
        $this->addRedirectionLoginConfig($rootNode);
        $this->addLayoutConfig($rootNode);

        return $treeBuilder;
    }

    /**
     * Mapping config
     *
     * @param $rootNode \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     *
     * @return void
     * @access protected
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    protected function addMapping(ArrayNodeDefinition $rootNode)
    {
        $rootNode
        ->children()
            ->arrayNode('mapping')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('provider')->isRequired()->defaultValue('orm')->end()
                    ->scalarNode('firewall_name')->isRequired()->defaultValue('main')->end()
                    ->scalarNode('user_class')->isRequired()->defaultValue('Sfynx\AuthBundle\Domain\Entity\User')->end()
                    ->scalarNode('user_entitymanager_command')->defaultValue('doctrine.orm.entity_manager')->end()
                    ->scalarNode('user_entitymanager_query')->defaultValue('doctrine.orm.entity_manager')->end()
                    ->scalarNode('user_entitymanager')->defaultValue('doctrine.orm.entity_manager')->end()

                    ->scalarNode('group_class')->isRequired()->defaultValue('Sfynx\AuthBundle\Domain\Entity\Group')->end()
                    ->scalarNode('group_entitymanager_command')->defaultValue('doctrine.orm.entity_manager')->end()
                    ->scalarNode('group_entitymanager_query')->defaultValue('doctrine.orm.entity_manager')->end()
                    ->scalarNode('group_entitymanager')->defaultValue('doctrine.orm.entity_manager')->end()

                    ->scalarNode('langue_class')->isRequired()->defaultValue('Sfynx\AuthBundle\Domain\Entity\Langue')->end()
                    ->scalarNode('langue_entitymanager_command')->defaultValue('doctrine.orm.entity_manager')->end()
                    ->scalarNode('langue_entitymanager_query')->defaultValue('doctrine.orm.entity_manager')->end()
                    ->scalarNode('langue_entitymanager')->defaultValue('doctrine.orm.entity_manager')->end()
                ->end()
            ->end()
        ->end();
    }
    
    /**
     * Login failure config
     *
     * @param $rootNode \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     *
     * @return void
     * @access protected
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    protected function addLoginFailureConfig(ArrayNodeDefinition $rootNode)
    {
    	$rootNode
    	->children()
        	->arrayNode('loginfailure')
        	    ->addDefaultsIfNotSet()
                ->children()
                    ->booleanNode('authorized')->isRequired()->defaultValue(true)->end()
                    ->scalarNode('time_expire')->defaultValue(3600)->end()
                    ->scalarNode('connection_attempts')->defaultValue(3)->end()
                    ->scalarNode('cache_dir')->defaultValue('%kernel.root_dir%/cachesfynx/loginfailure/')->cannotBeEmpty()->end()
                ->end()
            ->end()
    	->end();
    }      
    
    /**
     * Locale config
     *
     * @param $rootNode \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     *
     * @return void
     * @access protected
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    protected function addLocaleConfig(ArrayNodeDefinition $rootNode)
    {
    	$rootNode
    	->children()
        	->arrayNode('locale')
        	    ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('authorized')->prototype('scalar')->end()->defaultValue(['fr_FR', 'en_GB', 'ar_SA'])->end()
                    ->scalarNode('cache_file')->defaultValue('%kernel.root_dir%/cachesfynx/languages.json')->cannotBeEmpty()->end()
                ->end()
            ->end()
    	->end();
    }  
    
    /**
     * Browser config
     *
     * @param $rootNode \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     *
     * @return void
     * @access protected
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    protected function addBrowserConfig(ArrayNodeDefinition $rootNode)
    {
    	$rootNode
    	->children()
        	->arrayNode('browser')
        	    ->addDefaultsIfNotSet()
        	    ->children()
        	        ->booleanNode('switch_language_authorized')->isRequired()->defaultValue(false)->end()
        	        ->booleanNode('switch_layout_mobile_authorized')->isRequired()->defaultValue(false)->end()
        	    ->end()
        	->end()        	
    	->end();
    }    
    
    /**
     * Redirection login config
     *
     * @param $rootNode \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     *
     * @return void
     * @access protected
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    protected function addRedirectionLoginConfig(ArrayNodeDefinition $rootNode)
    {
    	$rootNode
    	->children()
            ->arrayNode('default_login_redirection')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('redirection')->defaultValue('admin_homepage')->cannotBeEmpty()->end()
                    ->scalarNode('template')->defaultValue('layout-pi-admin.html.twig')->cannotBeEmpty()->end()
                ->end()
            ->end()
    	->end();
    } 

    /**
     * Layout default config
     *
     * @param $rootNode \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     *
     * @return void
     * @access protected
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    protected function addLayoutConfig(ArrayNodeDefinition $rootNode)
    {
    	$rootNode
    	->children()
        	->arrayNode('default_layout')
        	    ->addDefaultsIfNotSet()
        	    ->children()
                	    ->arrayNode('init_pc')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('template')->defaultValue('layout-pi-page1.html.twig')->cannotBeEmpty()->end()
                            ->end()
                        ->end()
                        
                        ->arrayNode('init_mobile')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('template')->defaultValue('Default')->cannotBeEmpty()->end()                                    
                            ->end()
                        ->end()
            	->end()
        	->end()
    	->end();
    }  
}
