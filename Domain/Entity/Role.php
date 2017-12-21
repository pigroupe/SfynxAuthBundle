<?php
namespace Sfynx\AuthBundle\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Sfynx\AuthBundle\Domain\Entity\Layout;
use Sfynx\AuthBundle\Domain\Generalisation as TraitsAuth;
use Sfynx\CoreBundle\Layers\Domain\Model\Traits;

use Sfynx\CoreBundle\Layers\Domain\Model\Interfaces\EntityInterface;
use Sfynx\CoreBundle\Layers\Domain\Model\Interfaces\TraitEnabledInterface;

/**
 * Sfynx\AuthBundle\Domain\Entity\Role
 *
 * @ORM\Table(name="fos_role")
 * @ORM\Entity(repositoryClass="Sfynx\AuthBundle\Infrastructure\Persistence\Adapter\RoleRepository")
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
class Role implements EntityInterface,TraitEnabledInterface
{
    use Traits\TraitBuild;
    use Traits\TraitEnabled;
    use Traits\TraitName;
    use TraitsAuth\TraitHeritage;
    use TraitsAuth\TraitAccessControl;

    /**
     * @var bigint $id
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

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
     * @var integer $layout
     *
     * @ORM\ManyToOne(targetEntity="Sfynx\AuthBundle\Domain\Entity\Layout", inversedBy="roles", cascade={"persist"})
     * @ORM\JoinColumn(name="layout_id", referencedColumnName="id", nullable=true)
     */
    protected $layout;

    /**
     * Role constructor.
     */
    public function __construct()
    {
        $this->accessControl = new ArrayCollection();
    }

    /**
     * Set layout
     *
     * @param Layout $layout
     */
    public function setLayout(Layout $layout)
    {
        $this->layout = $layout;
    }

    /**
     * Get layout
     *
     * @return Layout
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     *
     * This method is used when you want to convert to string an object of
     * this Entity
     * ex: $value = (string)$entity;
     *
     */
    public function __toString() {
        return (string) $this->label;
    }

    /**
     * Get id
     *
     * @return bigint
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set label
     *
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set comment
     *
     * @param text $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get comment
     *
     * @return text
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set route_name
     *
     * @param string $routeName
     */
    public function setRouteLogin($routeName)
    {
        $this->route_login = $routeName;
    }

    /**
     * Get route_name
     *
     * @return string
     */
    public function getRouteLogin()
    {
        return $this->route_login;
    }

    /**
     * Set route_name
     *
     * @param string $routeName
     */
    public function setRouteLogout($routeName)
    {
    	$this->route_logout = $routeName;
    }

    /**
     * Get route_name
     *
     * @return string
     */
    public function getRouteLogout()
    {
    	return $this->route_logout;
    }
}
