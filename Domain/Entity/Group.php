<?php
namespace Sfynx\AuthBundle\Domain\Entity;

use Sfynx\AuthBundle\Domain\Generalisation\Interfaces\GroupInterface;

use Sfynx\CoreBundle\Layers\Domain\Model\Interfaces\EntityInterface;
use Sfynx\CoreBundle\Layers\Domain\Model\Interfaces\TraitDatetimeInterface;
use Sfynx\CoreBundle\Layers\Domain\Model\Traits\TraitDatetime;
use Sfynx\CoreBundle\Layers\Domain\Model\Interfaces\TraitEnabledInterface;
use Sfynx\CoreBundle\Layers\Domain\Model\Traits\TraitEnabled;

use Sfynx\CoreBundle\Layers\Domain\Model\Traits;
use Sfynx\AuthBundle\Domain\Generalisation as TraitsAuth;
use Sfynx\AuthBundle\Infrastructure\Persistence\Adapter\PermissionRepository;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_group")
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
class Group implements EntityInterface,GroupInterface,TraitDatetimeInterface,TraitEnabledInterface
{
    use Traits\TraitBuild;

    use Traits\TraitName;
    use Traits\TraitDatetime;
    use Traits\TraitEnabled;
    use TraitsAuth\TraitRoles;
    use TraitsAuth\TraitPermissions;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="name", type="string", length=25, nullable = true)
     * @Assert\Length(min = 3, max = 25, minMessage = "core.field.min_length", maxMessage="core.field.max_length")
     * @Assert\NotBlank(message="core.field.required")
     * @Assert\Regex(pattern="/^[[:alpha:]\s'\x22\-_&@!?()\[\]-]*$/u", message="core.field.regex")
     */
    protected $name;

    public function __construct($name = "", $roles = [])
    {
        $this->name = $name;
        $this->roles = $roles;
        $this->setPermissions(array('VIEW', 'EDIT', 'CREATE', 'DELETE'));
    }

    /**
     *
     * This method is used when you want to convert to string an object of
     * this Entity
     * ex: $value = (string)$entity;
     *
     */
    public function __toString() {
        return (string) $this->name;
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
}
