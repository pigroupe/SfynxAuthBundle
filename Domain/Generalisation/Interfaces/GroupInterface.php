<?php
namespace Sfynx\AuthBundle\Domain\Generalisation\Interfaces;

interface GroupInterface
{
    /**
     * @param string $role
     *
     * @return self
     */
    public function addRole(string $role);

    /**
     * @return integer
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $role
     *
     * @return boolean
     */
    public function hasRole(string $role): bool;

    /**
     * @return array
     */
    public function getRoles();

    /**
     * @param string $role
     *
     * @return self
     */
    public function removeRole(string $role);

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName($name);

    /**
     * @param array $roles
     *
     * @return self
     */
    public function setRoles(array $roles);
}
