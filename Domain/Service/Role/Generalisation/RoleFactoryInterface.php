<?php
/**
 * This file is part of the <Tool> project.
 *
 * @subpackage   User
 * @package    Builder
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @since 2012-02-23
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\AuthBundle\Domain\Service\Role\Generalisation;

/**
 * RoleFactory interface.
 *
 * @category   Sfynx\AuthBundle
 * @package    Domain
 * @subpackage Service\Role\Generalisation
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
interface RoleFactoryInterface
{
    /**
     * @return string
     */
    public function getJsonFile();

    /**
     * Gets all no authorize roles of an heritage of roles.
     *
     * @param array $heritage
     *
     * @return array the best roles of all roles.
     * @access public
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function getNoAuthorizeRoles(array $heritage);

    /**
     * Gets all user roles.
     *
     * @return array the best roles of all roles.
     * @access public
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function getAllUserRoles();

    /**
     * Gets the best role of all user roles.
     *
     * @return string    the best role of all user roles.
     * @access protected
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function getBestRoleUser();

    /**
     * Gets the best roles of many of roles.
     *
     * @param array $roles
     * @return array the best roles of all roles.
     * @access public
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function getBestRoles(?array $roles);

    /**
     * Return false if the json file does not existe
     *
     * @return boolean
     * @access public
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function isJsonFileExisted();

    /**
     * Create the json heritage file with all roles information.
     *
     * @param boolean $isForce
     * @return boolean
     * @access public
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function setJsonFileRoles(bool $isForce = true);
}
