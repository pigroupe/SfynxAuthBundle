<?php
namespace Sfynx\AuthBundle\Domain\Entity;

use Sfynx\CoreBundle\Layers\Domain\Model\Interfaces\EntityInterface;
use Sfynx\CoreBundle\Layers\Domain\Model\Interfaces\TraitEnabledInterface;
use Sfynx\CoreBundle\Layers\Domain\Model\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Sfynx\AuthBundle\Domain\Entity\Permission
 *
 * @ORM\Table(name="fos_permission")
 * @ORM\Entity(repositoryClass="Sfynx\AuthBundle\Infrastructure\Persistence\Adapter\PermissionRepository")
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
class Permission implements EntityInterface,TraitEnabledInterface
{
    use Traits\TraitBuild;
    use Traits\TraitEnabled;
    use Traits\TraitName;

    /**
     * @var bigint $id
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

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
}
