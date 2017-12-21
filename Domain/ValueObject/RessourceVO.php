<?php
namespace Sfynx\AuthBundle\Domain\ValueObject;

use DateTime;
use Doctrine\ODM\CouchDB\Mapping\Annotations as CouchDB;
use Doctrine\ORM\Mapping as ORM;

use Sfynx\CoreBundle\Layers\Domain\ValueObject\Generalisation\Traits;
use Sfynx\CoreBundle\Layers\Domain\ValueObject\Generalisation\AbstractVO;

/**
 * Class RessourceVO
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
final class RessourceVO extends AbstractVO
{
    use Traits\TraitDatetime;
    use Traits\TraitEnabled;
    use Traits\TraitSlugify;

    /**
     * @var string $route_name
     *
     * @ORM\Column(name="route_name", type="text", nullable=true)
     * @Assert\Length(min = 3, minMessage = "core.field.min_length")
     */
    protected $route_name;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", nullable=true)
     * @Assert\NotBlank(message="core.field.required")
     * @Assert\Length(min = 3, minMessage = "core.field.min_length")
     */
    protected $slug;

    /**
     * @var string $url
     *
     * @ORM\Column(name="url", type="string", nullable=true)
     */
    protected $url;

    /**
     * Returns the route_name.
     *
     * @return string
     */
    public function getRouteName(): string
    {
        return $this->routeName;
    }

    /**
     * Returns the url.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }
}
