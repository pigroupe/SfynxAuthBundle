<?php
namespace Sfynx\AuthBundle\Domain\Entity;

use Sfynx\CoreBundle\Layers\Domain\Model\Interfaces\EntityInterface;
use Sfynx\CoreBundle\Layers\Domain\Model\Interfaces\TraitDatetimeInterface;
use Sfynx\CoreBundle\Layers\Domain\Model\Interfaces\TraitEnabledInterface;
use Sfynx\CoreBundle\Layers\Domain\Model\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Sfynx\AuthBundle\Domain\Entity\Layout
 *
 * @ORM\Table(name="pi_layout")
 * @ORM\Entity(repositoryClass="Sfynx\AuthBundle\Infrastructure\Persistence\Adapter\LayoutRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("name")
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
class Layout implements EntityInterface,TraitDatetimeInterface, TraitEnabledInterface
{
    use Traits\TraitBuild;
    use Traits\TraitDatetime;
    use Traits\TraitEnabled;
    use Traits\TraitName;

    /**
     * @var bigint $id
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Sfynx\AuthBundle\Domain\Entity\Role", mappedBy="layout", cascade={"persist"})
     */
    protected $roles;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false, unique=true)
     * @Assert\NotBlank(message="core.field.required")
     * @Assert\Length(min = 3, minMessage = "core.field.min_length")
     */
    protected $name;

    /**
     * @var string $filePc
     *
     * @ORM\Column(name="file_pc", type="string", nullable=false)
     * @Assert\NotBlank(message="core.field.required")
     * @Assert\Length(min = 5, minMessage = "core.field.min_length")
     */
    protected $filePc;

    /**
     * @var string $fileMobile
     *
     * @ORM\Column(name="file_mobile", type="string", nullable=false)
     * @Assert\NotBlank(message="core.field.required")
     * @Assert\Length(min = 5, minMessage = "core.field.min_length")
     */
    protected $fileMobile;

    /**
     * @var text $configXml
     *
     * @ORM\Column(name="configXml", type="text", nullable=true)
     */
    protected $configXml;

    /**
     * Layout constructor.
     */
    public function __construct()
    {
        $this->setEnabled(true);
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
     * @return bigint
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set pc layout file name
     *
     * @param string $filePc
     */
    public function setFilePc($filePc)
    {
        $this->filePc = $filePc;
    }

    /**
     * Get pc layout file name
     *
     * @return string
     */
    public function getFilePc()
    {
        return $this->filePc;
    }

    /**
     * Set mobile layout file name
     *
     * @param string $fileMobile
     */
    public function setFileMobile($fileMobile)
    {
        $this->fileMobile = $fileMobile;
    }

    /**
     * Get mobile layout file name
     *
     * @return string
     */
    public function getFileMobile()
    {
        return $this->fileMobile;
    }

    /**
     * Set configXml
     *
     * @param text $configXml
     */
    public function setConfigXml($configXml)
    {
        $this->configXml = $configXml;
    }

    /**
     * Get configXml
     *
     * @return text
     */
    public function getConfigXml()
    {
        return $this->configXml;
    }
}
