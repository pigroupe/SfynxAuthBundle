<?php
namespace Sfynx\AuthBundle\Domain\Generalisation;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

use Sfynx\AuthBundle\Repository\RoleRepository;

/**
 * trait class for Heritage attributs.
 *
 * @category   Generalisation
 * @package    Trait
 * @subpackage Entity
 * @abstract
 */
trait TraitHeritage
{
    /**
     * @var array
     * @ORM\Column(type="array", nullable=true)
     */
    protected $heritage;

    /**
     * Set heritage
     *
     * @param array $heritage
     */
    public function setHeritage( array $heritage)
    {
        $this->heritage = [];
        foreach ($heritage as $role) {
            $this->addRoleInHeritage($role);
        }
    }

    /**
     * Get heritage
     *
     * @return array
     */
    public function getHeritage()
    {
        $roles = $this->heritage;
        // we need to make sure to have at least one role
        $roles[] = RoleRepository::ShowDefaultRole();

        return array_unique($roles);
    }

    /**
     * Adds a role heritatge to the role.
     *
     * @param string $role
     */
    public function addRoleInHeritage($role)
    {
        $role = strtoupper($role);
        if (!in_array($role, $this->heritage, true)) {
            $this->heritage[] = $role;
        }
    }
}
