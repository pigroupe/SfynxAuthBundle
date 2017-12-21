<?php
namespace Sfynx\AuthBundle\Domain\ValueObject;

use Doctrine\ODM\CouchDB\Mapping\Annotations as CouchDB;
use Doctrine\ORM\Mapping as ORM;

use Sfynx\CoreBundle\Layers\Domain\Model\Interfaces\TraitEnabledInterface;
use Sfynx\CoreBundle\Layers\Domain\ValueObject\Generalisation\Traits;
use Sfynx\CoreBundle\Layers\Domain\ValueObject\Generalisation\AbstractVO;

/**
 * Class RoleVO
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
final class RoleVO extends AbstractVO implements TraitEnabledInterface
{
    use Traits\TraitEnabled;
    use Traits\TraitName;

    /**
     * @ORM\Column(type="string",length=55, nullable=false)
     * @Assert\NotBlank(message="core.field.required")
     * @Assert\Length(min = 3, minMessage = "core.field.min_length")
     */
    protected $label;

    /**
     * @ORM\Column(name="name", type="string", length=25, nullable = true)
     * @Assert\Length(min = 8, max = 25, minMessage = "core.field.min_length", maxMessage="core.field.max_length")
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
     * @var string $route_name
     *
     * @ORM\Column(name="route_login", type="string", nullable=true)
     * @Assert\Length(min = 3, minMessage = "core.field.min_length")
     * @Assert\Blank
     */
    protected $route_login;

    /**
     * @var string $route_name
     *
     * @ORM\Column(name="route_logout", type="string", nullable=true)
     * @Assert\Length(min = 3, minMessage = "core.field.min_length")
     * @Assert\Blank
     */
    protected $route_logout;

    /**
     * Returns the label.
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Returns the comment.
     *
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * Returns the route_login.
     *
     * @return string
     */
    public function getRouteLogin(): string
    {
        return $this->routeLogin;
    }

    /**
     * Returns the route_logout.
     *
     * @return string
     */
    public function getRouteLogout(): string
    {
        return $this->routeLogout;
    }
}
