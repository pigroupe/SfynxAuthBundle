<?php
namespace Sfynx\AuthBundle\Domain\EntityVO;

use Doctrine\ODM\CouchDB\Mapping\Annotations as CouchDB;
use Doctrine\ORM\Mapping as ORM;
use Sfynx\CoreBundle\Layers\Domain\Model\Interfaces\EntityInterface;
use Sfynx\AuthBundle\Domain\ValueObject\PermissionVO;

/**
 * Class Permission
 *
 * @category Sfynx\AuthBundle
 * @package Domain
 * @subpackage Entity
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="fos_permission")
 *
 * ODM\Document(collection="fos_permission")
 * ODM\HasLifecycleCallbacks
 *
 * CouchDB\Document
 */
class Permission implements EntityInterface
{
    /**
     * @var integer|string Unique identifier of the Permission.
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
     * @var PermissionVO
     *
     * @JMS\Serializer\Annotation\Since("1")
     * @ORM\Embedded(class="Sfynx\AuthBundle\Domain\ValueObject\PermissionVO", columnPrefix=false)
     * ODM\EmbedOne(targetDocument="Sfynx\AuthBundle\Domain\ValueObject\PermissionVO")
     * CouchDB\EmbedOne(targetDocument="Sfynx\AuthBundle\Domain\ValueObject\PermissionVO")
     */
    protected $permission;

    /**
     * Builds the entity based on the value object associated.
     *
     * @param PermissionVO $permission
     * @return Permission
     */
    public static function build(PermissionVO $permission): Permission
    {
        return new self($permission);
    }

    /**
     * Permission constructor.
     *
     * @param PermissionVO $permission
     */
    protected function __construct(PermissionVO $permission)
    {
        $this->setPermission($permission);
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
     * @return Permission
     */
    public function setId($id): Permission
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Returns the permission.
     *
     * @return PermissionVO
     */
    public function getPermission(): PermissionVO
    {
        return $this->permission;
    }

    /**
     * Sets the permission.
     *
     * @param PermissionVO $permission
     * @return Permission
     */
    public function setPermission(PermissionVO $permission): Permission
    {
        $this->permission = $permission;
        return $this;
    }
}
