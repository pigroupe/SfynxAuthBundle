<?php
namespace Sfynx\AuthBundle\Domain\Generalisation;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

use Sfynx\AuthBundle\Infrastructure\Persistence\Adapter\PermissionRepository;

/**
 * trait class for default attributs.
 *
 * @category   Generalisation
 * @package    Trait
 * @subpackage Entity
 * @abstract
 */
trait TraitPermissions
{
    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    protected $permissions = array('VIEW', 'EDIT', 'CREATE', 'DELETE');

    /**
     * Set permissions
     *
     * @param array $permissions
     */
    public function setPermissions(array $permissions)
    {
        $this->permissions = [];
        if (count($permissions) > 0) {
            foreach ($permissions as $permission) {
                $this->addPermission($permission);
            }
        }
    }

    /**
     * Get permissions
     *
     * @return array
     */
    public function getPermissions()
    {
        $permissions = $this->permissions;
        if (method_exists($this, 'getGroups')) {
            foreach ($this->getGroups() as $group) {
                $permissions = array_merge($permissions, $group->getPermissions());
            }
        }
        // we need to make sure to have at least one role
        $permissions[] = PermissionRepository::ShowDefaultPermission();

        return array_unique($permissions);
    }

    /**
     * Adds a permission to the user.
     *
     * @param string $permission
     */
    public function addPermission($permission)
    {
        $permission = strtoupper($permission);
        if ($permission === PermissionRepository::ShowDefaultPermission()) {
            return;
        }
        if (!in_array($permission, $this->permissions, true)) {
            $this->permissions[] = $permission;
        }
    }

    /**
     * Remove a permission to the user.
     *
     * @param string $permission
     */
    public function removePermission($permission)
    {
        $permission = strtoupper($permission);
        if (in_array($permission, $this->permissions, true)) {
            $key = array_search($permission, $this->permissions);
            unset($this->permissions[$key]);
        }
    }
}
