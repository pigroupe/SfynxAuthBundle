<?php
namespace Sfynx\AuthBundle\Domain\Generalisation;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Sfynx\AuthBundle\Domain\Entity\Ressource;

/**
 * trait class for AccessControl attributs.
 *
 * @category   Generalisation
 * @package    Trait
 * @subpackage Entity
 * @abstract
 */
trait TraitAccessControl
{
    /**
     * @JMS\Serializer\Annotation\Since("1")
     * @JMS\Serializer\Annotation\Exclude
     * @ORM\ManyToMany(targetEntity="Sfynx\AuthBundle\Domain\Entity\Ressource")
     * @ORM\JoinTable(name="fos_role_ressource",
     *      joinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="ressource_id", referencedColumnName="id")}
     * )
     */
    protected $accessControl;

    /**
     * Add Ressource
     *
     * @param Ressource $ressource
     */
    public function addRessource(Ressource $ressource)
    {
        if (!$this->accessControl->contains($ressource)) {
            $this->accessControl->add($ressource);
        }
    }

    /**
     * Get accessControl
     *
     * @return ArrayCollection
     */
    public function getAccessControl()
    {
        return $this->accessControl;
    }
}
