<?php
namespace Sfynx\AuthBundle\Domain\Generalisation;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * trait class for role attributs.
 *
 * @category   Generalisation
 * @package    Trait
 * @subpackage Entity
 * @abstract
 */
trait TraitRoles
{
    static $ROLE_DEFAULT = 'ROLE_USER';
    static $ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    protected $roles;

    /**
     * Returns the roles
     *
     * @return array The roles
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        if (method_exists($this, 'getGroups')) {
            foreach ($this->getGroups() as $group) {
                if (null !== $group->getRoles()) {
                    if (is_array($roles)) {
                        $roles = array_merge($roles, $group->getRoles());
                    } else {
                        $roles = $group->getRoles();
                    }
                }
            }
        }
        // we need to make sure to have at least one role
        $roles[] = static::$ROLE_DEFAULT;

        return array_unique($roles);
    }

    /**
     * @param array $roles
     * @return self
     */
    public function setRoles(array $roles)
    {
        $this->roles = [];
        foreach ($roles as $role) {
            $this->addRole($role);
        }
        return $this;
    }

    /**
     * Adds a role to the user.
     *
     * @param string $role
     * @return self
     */
    public function addRole(string $role)
    {
        $role = strtoupper($role);
        if ($role === static::$ROLE_DEFAULT) {
            return $this;
        }
        if (!$this->hasRole($role)) {
            $this->roles[] = $role;
        }
        return $this;
    }

    /**
     * Never use this to check if this user has access to anything!
     *
     * Use the SecurityContext, or an implementation of AccessDecisionManager
     * instead, e.g.
     *
     *         $securityContext->isGranted('ROLE_USER');
     *
     * @param string $role
     *
     * @return boolean
     */
    public function hasRole(string $role): bool
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    /**
     * @param $role
     * @return self
     */
    public function removeRole(string $role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }
        return $this;
    }
}
