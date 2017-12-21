<?php
namespace Sfynx\AuthBundle\Domain\EntityVO;

use Doctrine\ODM\CouchDB\Mapping\Annotations as CouchDB;
use Doctrine\ORM\Mapping as ORM;
use Sfynx\CoreBundle\Layers\Domain\Model\Interfaces\EntityInterface;
use Sfynx\AuthBundle\Domain\ValueObject\LangueTranslationVO;

/**
 * Class LangueTranslation
 *
 * @category Sfynx\AuthBundle
 * @package Domain
 * @subpackage Entity
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="Pi_langue_translations")
 *
 * ODM\Document(collection="Pi_langue_translations")
 * ODM\HasLifecycleCallbacks
 *
 * CouchDB\Document
 *
 * @copyright Copyright (c) 2016-2017, Aareon Group
 * @license http://www.pigroupe.com under a proprietary license
 * @version 1.1.1
 */
class LangueTranslation implements EntityInterface
{
    /**
     * @var integer|string Unique identifier of the LangueTranslation.
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
     * @var Langue
     *
     * @JMS\Serializer\Annotation\Since("1")
     * @JMS\Serializer\Annotation\Exclude
     * @ORM\ManyToOne(targetEntity="Langue")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id")
     */
    protected $object_id;

    /**
     * @var LangueTranslationVO
     *
     * @JMS\Serializer\Annotation\Since("1")
     * @ORM\Embedded(class="Sfynx\AuthBundle\Domain\ValueObject\LangueTranslationVO", columnPrefix=false)
     * ODM\EmbedOne(targetDocument="Sfynx\AuthBundle\Domain\ValueObject\LangueTranslationVO")
     * CouchDB\EmbedOne(targetDocument="Sfynx\AuthBundle\Domain\ValueObject\LangueTranslationVO")
     */
    protected $langueTranslation;

    /**
     * Builds the entity based on the value object associated.
     *
     * @param Langue $object_id
     * @param LangueTranslationVO $langueTranslation
     * @return LangueTranslation
     */
    public static function build(Langue $object_id, LangueTranslationVO $langueTranslation): LangueTranslation
    {
        return new self($object_id, $langueTranslation);
    }

    /**
     * LangueTranslation constructor.
     *
     * @param Langue $object_id
     * @param LangueTranslationVO $langueTranslation
     */
    protected function __construct(Langue $object_id, LangueTranslationVO $langueTranslation)
    {
        $this->setObject_id($object_id);
        $this->setLangueTranslation($langueTranslation);
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
     * @return LangueTranslation
     */
    public function setId($id): LangueTranslation
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Returns the object_id.
     *
     * @return Langue
     */
    public function getObject_id(): Langue
    {
        return $this->object_id;
    }

    /**
     * Sets the object_id.
     *
     * @param Langue $object_id
     * @return LangueTranslation
     */
    public function setObject_id(Langue $object_id): LangueTranslation
    {
        $this->object_id = $object_id;
        return $this;
    }

    /**
     * Gets the langue id.
     *
     * @JMS\Serializer\Annotation\VirtualProperty
     * @JMS\Serializer\Annotation\SerializedName("object_idId")
     * @return mixed
     */
    public function getObject_idId()
    {
        return $this->object_id->getId();
    }

    /**
     * Returns the langueTranslation.
     *
     * @return LangueTranslationVO
     */
    public function getLangueTranslation(): LangueTranslationVO
    {
        return $this->langueTranslation;
    }

    /**
     * Sets the langueTranslation.
     *
     * @param LangueTranslationVO $langueTranslation
     * @return LangueTranslation
     */
    public function setLangueTranslation(LangueTranslationVO $langueTranslation): LangueTranslation
    {
        $this->langueTranslation = $langueTranslation;
        return $this;
    }
}
