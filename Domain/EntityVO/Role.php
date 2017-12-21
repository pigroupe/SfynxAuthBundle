<?php
namespace Sfynx\AuthBundle\Domain\EntityVO;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\CouchDB\Mapping\Annotations as CouchDB;
use Doctrine\ORM\Mapping as ORM;

use Sfynx\CoreBundle\Layers\Domain\Model\Interfaces\EntityInterface;
use Sfynx\AuthBundle\Domain\ValueObject\RoleVO;
use Sfynx\AuthBundle\Domain\Generalisation as TraitsAuth;

/**
 * Class Role
 *
 * @category Sfynx\AuthBundle
 * @package Domain
 * @subpackage Entity
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="fos_role")
 *
 * ODM\Document(collection="fos_role")
 * ODM\HasLifecycleCallbacks
 *
 * CouchDB\Document
 *
 * @copyright Copyright (c) 2016-2017, Aareon Group
 * @license http://www.pigroupe.com under a proprietary license
 * @version 1.1.1
 */
class Role implements EntityInterface
{
    use TraitsAuth\TraitHeritage;
    use TraitsAuth\TraitAccessControl;

    /**
     * @var integer|string Unique identifier of the Role.
     *
     * @JMS\Serializer\Annotation\Since("1")
     * @ORM\Column(type="integer", name="ID")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * ODM\Id(strategy="AUTO", type="string", name="ID")
     * CouchDB\Id(strategy="UUID")
     */
    protected $id;

    /**
     * @var RoleVO
     *
     * @JMS\Serializer\Annotation\Since("1")
     * @ORM\Embedded(class="Sfynx\AuthBundle\Domain\ValueObject\RoleVO", columnPrefix=false)
     * ODM\EmbedOne(targetDocument="Sfynx\AuthBundle\Domain\ValueObject\RoleVO")
     * CouchDB\EmbedOne(targetDocument="Sfynx\AuthBundle\Domain\ValueObject\RoleVO")
     */
    protected $role;

    /**
     * @var Layout
     *
     * @JMS\Serializer\Annotation\Since("1")
     * @JMS\Serializer\Annotation\Exclude
     * @ORM\ManyToOne(targetEntity="Layout", inversedBy="roles", cascade={"persist"})
     * @ORM\JoinColumn(name="layout_id", referencedColumnName="id")
     */
    protected $layout;

    /**
     * Builds the entity based on the value object associated.
     *
     * @param Layout $layout
     * @param RoleVO $role
     * @return Role
     */
    public static function build(Layout $layout, RoleVO $role): Role
    {
        return new self($layout, $role);
    }

    /**
     * Role constructor.
     *
     * @param Layout $layout
     * @param RoleVO $role
     */
    protected function __construct(Layout $layout, RoleVO $role)
    {
        $this->accessControl = new ArrayCollection();
        $this->setLayout($layout);
        $this->setRole($role);
    }

    /**
     * Returns the id.
     *
     * @return int|string|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the id.
     *
     * @param int|string $id
     * @return Role
     */
    public function setId($id): Role
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Returns the layout.
     *
     * @return Layout
     */
    public function getLayout(): Layout
    {
        return $this->layout;
    }

    /**
     * Sets the layout.
     *
     * @param Layout $layout
     * @return Role
     */
    public function setLayout(Layout $layout): Role
    {
        $this->layout = $layout;
        return $this;
    }

    /**
     * Returns the role.
     *
     * @return RoleVO
     */
    public function getRole(): RoleVO
    {
        return $this->role;
    }

    /**
     * Sets the role.
     *
     * @param RoleVO $role
     * @return Role
     */
    public function setRole(RoleVO $role): Role
    {
        $this->role = $role;
        return $this;
    }
}
