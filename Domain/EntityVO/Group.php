<?php
namespace Sfynx\AuthBundle\Domain\EntityVO;

use Doctrine\ODM\CouchDB\Mapping\Annotations as CouchDB;
use Doctrine\ORM\Mapping as ORM;
use Sfynx\CoreBundle\Layers\Domain\Model\Interfaces\EntityInterface;
use Sfynx\AuthBundle\Domain\ValueObject\GroupVO;

/**
 * Class Group
 *
 * @category Sfynx\AuthBundle
 * @package Domain
 * @subpackage Entity
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="Fos_group")
 *
 * ODM\Document(collection="Fos_group")
 * ODM\HasLifecycleCallbacks
 *
 * CouchDB\Document
 */
class Group implements EntityInterface
{
    /**
     * @var integer|string Unique identifier of the Group.
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
     * @var GroupVO
     *
     * @JMS\Serializer\Annotation\Since("1")
     * @ORM\Embedded(class="Sfynx\AuthBundle\Domain\ValueObject\GroupVO", columnPrefix=false)
     * ODM\EmbedOne(targetDocument="Sfynx\AuthBundle\Domain\ValueObject\GroupVO")
     * CouchDB\EmbedOne(targetDocument="Sfynx\AuthBundle\Domain\ValueObject\GroupVO")
     */
    protected $group;

    /**
     * Builds the entity based on the value object associated.
     *
     * @param GroupVO $group
     * @return Group
     */
    public static function build(GroupVO $group): Group
    {
        return new self($group);
    }

    /**
     * Group constructor.
     *
     * @param GroupVO $group
     */
    protected function __construct(GroupVO $group)
    {
        $this->setGroup($group);
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
     * @return Group
     */
    public function setId($id): Group
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Returns the group.
     *
     * @return GroupVO
     */
    public function getGroup(): GroupVO
    {
        return $this->group;
    }

    /**
     * Sets the group.
     *
     * @param GroupVO $group
     * @return Group
     */
    public function setGroup(GroupVO $group): Group
    {
        $this->group = $group;
        return $this;
    }
}
