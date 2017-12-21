<?php
namespace Sfynx\AuthBundle\Domain\ValueObject;

use Doctrine\ODM\CouchDB\Mapping\Annotations as CouchDB;
use Doctrine\ORM\Mapping as ORM;

use Sfynx\CoreBundle\Layers\Domain\ValueObject\Generalisation\Traits;
use Sfynx\CoreBundle\Layers\Domain\ValueObject\Generalisation\AbstractVO;

/**
 * Class PermissionVO
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
final class PermissionVO extends AbstractVO
{
    use Traits\TraitEnabled;
    use Traits\TraitName;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=25, nullable=true)
     * @Assert\Length(min = 3, max = 25, minMessage = "core.field.min_length", maxMessage="core.field.max_length")
     * @Assert\NotBlank(message="core.field.required")
     * @Assert\Regex(pattern="/^[[:alpha:]\s'\x22\-_&@!?()\[\]-]*$/u", message="core.field.regex")
     */
    protected $name;

    /**
     * @var text $comment
     *
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(message = "You must enter a comment")
     * @Assert\Length(min = 25, minMessage = "core.field.min_length")
     */
    protected $comment;

    /**
     * Returns the comment.
     *
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }
}
