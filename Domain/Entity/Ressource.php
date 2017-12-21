<?php
namespace Sfynx\AuthBundle\Domain\Entity;

use Sfynx\CoreBundle\Layers\Domain\Model\Interfaces\EntityInterface;
use Sfynx\CoreBundle\Layers\Domain\Model\Interfaces\TraitDatetimeInterface;
use Sfynx\CoreBundle\Layers\Domain\Model\Interfaces\TraitEnabledInterface;
use Sfynx\CoreBundle\Layers\Domain\Model\Interfaces\TraitSlugifyInterface;
use Sfynx\CoreBundle\Layers\Domain\Model\Traits\TraitDatetime;
use Sfynx\CoreBundle\Layers\Domain\Model\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Sfynx\AuthBundle\Domain\Entity\Ressource
 *
 * @ORM\Table(name="fos_ressource")
 * @ORM\Entity(repositoryClass="Sfynx\AuthBundle\Infrastructure\Persistence\Adapter\RessourceRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @category   Sfynx\AuthBundle
 * @package    Domain
 * @subpackage Entity
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2015 PI-GROUPE
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    2.3
 * @link       http://opensource.org/licenses/gpl-license.php
 * @since      2015-02-16
 */
class Ressource implements EntityInterface,TraitDatetimeInterface, TraitEnabledInterface, TraitSlugifyInterface
{
    use Traits\TraitBuild;
    use Traits\TraitDatetime;
    use Traits\TraitEnabled;
    use Traits\TraitSlugify;

    /**
     * @var bigint $id
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $route_name
     *
     * @ORM\Column(name="route_name", type="text", nullable=true)
     * @Assert\Length(min = 3, minMessage = "core.field.min_length")
     */
    protected $route_name;

    /**
     * @var string $slug
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

    public function __toString()
    {
        return (string) $this->getName();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set route_name
     *
     * @param string $routeName
     */
    public function setRouteName($routeName)
    {
        $this->route_name = $routeName;
    }

    /**
     * Get route_name
     *
     * @return string
     */
    public function getRouteName()
    {
        return $this->route_name;
    }

    /**
     * Set url
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}
