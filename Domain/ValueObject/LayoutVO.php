<?php
namespace Sfynx\AuthBundle\Domain\ValueObject;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\CouchDB\Mapping\Annotations as CouchDB;
use Doctrine\ORM\Mapping as ORM;

use Sfynx\CoreBundle\Layers\Domain\Model\Interfaces\TraitDatetimeInterface;
use Sfynx\CoreBundle\Layers\Domain\Model\Interfaces\TraitEnabledInterface;
use Sfynx\CoreBundle\Layers\Domain\ValueObject\Generalisation\Traits;
use Sfynx\CoreBundle\Layers\Domain\ValueObject\Generalisation\AbstractVO;

/**
 * Class LayoutVO
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
final class LayoutVO extends AbstractVO implements TraitDatetimeInterface, TraitEnabledInterface
{
    use Traits\TraitDatetime;
    use Traits\TraitEnabled;
    use Traits\TraitName;

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
        $this->enabled = true;
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
