<?php
namespace Sfynx\AuthBundle\Domain\EntityVO;

use Doctrine\ODM\CouchDB\Mapping\Annotations as CouchDB;
use Doctrine\ORM\Mapping as ORM;

use Sfynx\CoreBundle\Layers\Domain\Model\Interfaces\EntityInterface;
use Sfynx\AuthBundle\Domain\ValueObject\RessourceVO;

/**
 * Class Ressource
 *
 * @category Sfynx\AuthBundle
 * @package Domain
 * @subpackage Entity
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="fos_ressource")
 *
 * ODM\Document(collection="fos_ressource")
 * ODM\HasLifecycleCallbacks
 *
 * CouchDB\Document
 */
class Ressource implements EntityInterface
{
    /**
     * @var integer|string Unique identifier of the Ressource.
     *
     * @JMS\Serializer\Annotation\Since("1")
     * @ORM\Column(type="integer", name="ID")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * ODM\Id(strategy="AUTO", type="string", name="ID")
     * CouchDB\Id(strategy="UUID")
     */
    protected $id;

    /**
     * @var RessourceVO
     *
     * @JMS\Serializer\Annotation\Since("1")
     * @ORM\Embedded(class="Sfynx\AuthBundle\Domain\ValueObject\RessourceVO", columnPrefix=false)
     * ODM\EmbedOne(targetDocument="Sfynx\AuthBundle\Domain\ValueObject\RessourceVO")
     * CouchDB\EmbedOne(targetDocument="Sfynx\AuthBundle\Domain\ValueObject\RessourceVO")
     */
    protected $ressource;

    /**
     * Builds the entity based on the value object associated.
     *
     * @param RessourceVO $ressource
     * @return Ressource
     */
    public static function build(RessourceVO $ressource): Ressource
    {
        return new self($ressource);
    }

    /**
     * Ressource constructor.
     *
     * @param RessourceVO $ressource
     */
    protected function __construct(RessourceVO $ressource)
    {
        $this->setRessource($ressource);
    }

    /**
     * Returns the id.
     *
     * @return int|string|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the id.
     *
     * @param int|string $id
     * @return Ressource
     */
    public function setId($id): Ressource
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Returns the ressource.
     *
     * @return RessourceVO
     */
    public function getRessource(): RessourceVO
    {
        return $this->ressource;
    }

    /**
     * Sets the ressource.
     *
     * @param RessourceVO $ressource
     * @return Ressource
     */
    public function setRessource(RessourceVO $ressource): Ressource
    {
        $this->ressource = $ressource;
        return $this;
    }
}
