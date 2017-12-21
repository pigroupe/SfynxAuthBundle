<?php
namespace Sfynx\AuthBundle\Domain\Generalisation;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Sfynx\AuthBundle\Domain\Generalisation\Interfaces\GroupInterface;
use Sfynx\AuthBundle\Domain\Entity\Group;

/**
 * trait class for enabled attributs.
 *
 * @category   Generalisation
 * @package    Trait
 * @subpackage Entity
 * @abstract
 */
trait TraitGroups
{
    /**
     * @ORM\ManyToMany(targetEntity="Sfynx\AuthBundle\Domain\Entity\Group")
     * @ORM\JoinTable(name="fos_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;

    /**
     * Gets the groups granted to the user.
     *
     * @return ArrayCollection
     */
    public function getGroups()
    {
        return $this->groups ?: $this->initGroups();
    }

    /**
     * Gets the groups granted to the user.
     *
     * @return ArrayCollection
     */
    public function initGroups()
    {
        return $this->groups = new ArrayCollection();
    }

    /**
     * @return array
     */
    public function getGroupNames()
    {
        $names = [];
        foreach ($this->getGroups() as $group) {
            $names[] = $group->getName();
        }
        return $names;
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasGroup($name): bool
    {
        return in_array($name, $this->getGroupNames());
    }

    /**
     * @param GroupInterface $group
     * @return self
     */
    public function addGroup(GroupInterface $group): self
    {
        if (!$this->getGroups()->contains($group)) {
            $this->getGroups()->add($group);
        }
        return $this;
    }

    /**
     * @param GroupInterface $group
     * @return self
     */
    public function removeGroup(GroupInterface $group): self
    {
        if ($this->getGroups()->contains($group)) {
            $this->getGroups()->removeElement($group);
        }
        return $this;
    }
}
