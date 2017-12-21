<?php
namespace Sfynx\AuthBundle\Domain\ValueObject;

use DateTime;
use Doctrine\ODM\CouchDB\Mapping\Annotations as CouchDB;
use Doctrine\ORM\Mapping as ORM;
use Sfynx\CoreBundle\Layers\Domain\ValueObject\Generalisation\AbstractVO;

/**
 * Class GroupVO
 *
 * @category Sfynx\AuthBundle
 * @package Domain
 * @subpackage ValueObject
 * @final
 *
 * @ORM\Embeddable
 * ODM\EmbeddedDocument
 * CouchDB\EmbeddedDocument
 */
final class GroupVO extends AbstractVO
{
    /**
     * @ORM\Column(name="name", type="string", length=25, nullable = true)
     * @Assert\Length(min = 3, max = 25, minMessage = "core.field.min_length", maxMessage="core.field.max_length")
     * @Assert\NotBlank(message="core.field.required")
     * @Assert\Regex(pattern="/^[[:alpha:]\s'\x22\-_&@!?()\[\]-]*$/u", message="core.field.regex")
     */
    protected $name;

    /**
     * @var DateTime
     *
     * @JMS\Serializer\Annotation\Since("1")
     * @JMS\Serializer\Annotation\Type("DateTime<'Y-m-d H:i:s'>")
     * @ORM\Column(name="created_at", type="datetime")
     * ODM\Field(name="created_at", type="datetime")
     * CouchDB\Field(type="datetime")
     */
    protected $createdAt;

    /**
     * @var DateTime
     *
     * @JMS\Serializer\Annotation\Since("1")
     * @JMS\Serializer\Annotation\Type("DateTime<'Y-m-d H:i:s'>")
     * @ORM\Column(name="updated_at", type="datetime")
     * ODM\Field(name="updated_at", type="datetime")
     * CouchDB\Field(type="datetime")
     */
    protected $updatedAt;

    /**
     * @var DateTime
     *
     * @JMS\Serializer\Annotation\Since("1")
     * @JMS\Serializer\Annotation\Type("DateTime<'Y-m-d H:i:s'>")
     * @ORM\Column(name="published_at", type="datetime")
     * ODM\Field(name="published_at", type="datetime")
     * CouchDB\Field(type="datetime")
     */
    protected $publishedAt;

    /**
     * @var DateTime
     *
     * @JMS\Serializer\Annotation\Since("1")
     * @JMS\Serializer\Annotation\Type("DateTime<'Y-m-d H:i:s'>")
     * @ORM\Column(name="archive_at", type="datetime")
     * ODM\Field(name="archive_at", type="datetime")
     * CouchDB\Field(type="datetime")
     */
    protected $archiveAt;

    /**
     * @var bool
     *
     * @JMS\Serializer\Annotation\Since("1")
     * @ORM\Column(name="archived", type="boolean")
     * ODM\Field(name="archived", type="boolean")
     * CouchDB\Field(type="boolean")
     */
    protected $archived;

    /**
     * @var bool
     *
     * @JMS\Serializer\Annotation\Since("1")
     * @ORM\Column(name="enabled", type="boolean")
     * ODM\Field(name="enabled", type="boolean")
     * CouchDB\Field(type="boolean")
     */
    protected $enabled;

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the created_at.
     *
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * Returns the updated_at.
     *
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * Returns the published_at.
     *
     * @return DateTime
     */
    public function getPublishedAt(): DateTime
    {
        return $this->publishedAt;
    }

    /**
     * Returns the archive_at.
     *
     * @return DateTime
     */
    public function getArchiveAt(): DateTime
    {
        return $this->archiveAt;
    }

    /**
     * Returns the archived.
     *
     * @return bool
     */
    public function getArchived(): bool
    {
        return $this->archived;
    }

    /**
     * Returns the enabled.
     *
     * @return bool
     */
    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Returns the roles.
     *
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * Returns the permissions.
     *
     * @return array
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }
}
