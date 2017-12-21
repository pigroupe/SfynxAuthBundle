<?php
/**
 * This file is part of the <Auth> project.
 *
 * @category   Auth
 * @package    Bunlde
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
namespace Sfynx\AuthBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Sfynx\AuthBundle\DependencyInjection\Compiler\ChangeProviderPass;

/**
 * Sfynx configuration and managment of the user Bundle
 *
 * @category   Auth
 * @package    Bunlde
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2015 PI-GROUPE
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    2.3
 * @link       http://opensource.org/licenses/gpl-license.php
 * @since      2015-02-16
 */
class SfynxAuthBundle extends Bundle
{
    const HTTP_TYPE = "http";

    /**
     * Builds the bundle.
     *
     * It is only ever called once when the cache is empty.
     *
     * This method can be overridden to register compilation passes,
     * other extensions, ...
     *
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @author <etienne de Longeaux> <etienne.delongeaux@gmail.com>
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ChangeProviderPass());

        // we get the heritage.jon file if it's created
        $roles_json = '';
        $path_heritages_file = realpath($container->getParameter("kernel.root_dir"). '/cachesfynx/heritage.json');
        if ($path_heritages_file) {
            $roles_json = file_get_contents($path_heritages_file);
        }

        // we inject all roles in the role_hierarchy param
        $heritage_role  = json_decode($roles_json);
        if (is_object($heritage_role)) {
            $heritage_role  = get_object_vars($heritage_role->HERITAGE_ROLES);
        } else {
            $heritage_role  = [
                'ROLE_SUBSCRIBER'       => array(),
                'ROLE_MEMBER'           => array('ROLE_SUBSCRIBER'),

                'ROLE_USER'             => array(),

                'ROLE_CUSTOMER'         => array('ROLE_USER'),
                'ROLE_PROVIDER'         => array('ROLE_USER'),

                'ROLE_EDITOR'           => array('ROLE_MEMBER', 'ROLE_USER'),
                'ROLE_MODERATOR'        => array('ROLE_EDITOR'),

                'ROLE_DESIGNER'         => array('ROLE_MEMBER', 'ROLE_USER'),

                'ROLE_CONTENT_MANAGER'  => array('ROLE_DESIGNER', 'ROLE_MODERATOR'),
                'ROLE_ADMIN'            => array('ROLE_CONTENT_MANAGER', 'ROLE_CUSTOMER', 'ROLE_PROVIDER', 'ROLE_ALLOWED_TO_SWITCH'),

                'SONATA'                => array('ROLE_SONATA_PAGE_ADMIN_PAGE_EDIT ', 'ROLE_SONATA_PAGE_ADMIN_BLOCK_EDIT'),

                'ROLE_SUPER_ADMIN'      => array('ROLE_ADMIN', 'ROLE_ALLOWED_TO_SWITCH', 'ROLE_SONATA_ADMIN', 'SONATA'),
            ];
        }
        $heritage_role['SONATA'] = array('ROLE_SONATA_PAGE_ADMIN_PAGE_EDIT ', 'ROLE_SONATA_PAGE_ADMIN_BLOCK_EDIT');
        $heritage_role['ROLE_SUPER_ADMIN'] = array('ROLE_ADMIN', 'ROLE_ALLOWED_TO_SWITCH', 'ROLE_SONATA_ADMIN', 'SONATA');

        //print_r($heritage_role);

        // Security
        $container->loadFromExtension('security', array(
                'role_hierarchy' => $heritage_role,
//                #
//                # The access_control section is where you specify the credentials necessary for users trying to access specific parts of your application.
//                #
//                'access_control' => array(
//                        #
//                        #  The bundle requires that the login form and all the routes used to create a user
//                        #  and reset the password be available to unauthenticated users but use the same firewall
//                        #  as the pages you want to secure with the bundle. This is why you have specified that
//                        #  the any request matching the /login pattern or starting with /register or /resetting have been made available to anonymous users.
//                        #  You have also specified that any request beginning with /admin will require a user to have the ROLE_ADMIN role.
//                        #
//
//                        # The WDT has to be allowed to anonymous users to avoid requiring the login with the AJAX request
//                        array('path' => '^/_wdt/', 'role' => 'IS_AUTHENTICATED_ANONYMOUSLY'),
//                        array('path' => '^/_profiler/', 'role' => 'IS_AUTHENTICATED_ANONYMOUSLY'),
//                        # AsseticBundle paths used when using the controller for assets
//                        array('path' => '^/js/', 'role' => 'IS_AUTHENTICATED_ANONYMOUSLY'),
//                        array('path' => '^/css/', 'role' => 'IS_AUTHENTICATED_ANONYMOUSLY'),
//                        array('path' => '^/login$', 'role' => 'IS_AUTHENTICATED_ANONYMOUSLY', 'requires_channel' => self::HTTP_TYPE),
//                        array('path' => '^/login_check$', 'role' => 'IS_AUTHENTICATED_ANONYMOUSLY', 'requires_channel' => self::HTTP_TYPE),
//                        # -> custom access control for the admin area of the URL
//                        array('path' => '^/client/', 'role' => 'ROLE_CUSTOMER', 'requires_channel' => self::HTTP_TYPE),
//                        array('path' => '^/provider/', 'role' => 'ROLE_PROVIDER', 'requires_channel' => self::HTTP_TYPE),
//                        array('path' => '^/admin/', 'role' => 'ROLE_EDITOR', 'requires_channel' => self::HTTP_TYPE),
//                        array('path' => '^/adminsonata/', 'role' => 'ROLE_SUPER_ADMIN', 'requires_channel' => self::HTTP_TYPE),
//                        # DESACTIVER LE FRONT ACCES AUX NON LOGGE
//                        #array('path' => '^/', 'role' => 'ROLE_USER', 'requires_channel' => self::HTTP_TYPE),
//                ),
        ));
    }

    /**
     * Boots the Bundle.
     */
    public function boot()
    {
    }

    /**
     * Shutdowns the Bundle.
     */
    public function shutdown()
    {
    }
}
