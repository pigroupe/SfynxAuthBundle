<?php
namespace Sfynx\AuthBundle\Domain\EntityVO;

use Doctrine\ODM\CouchDB\Mapping\Annotations as CouchDB;
use Doctrine\ORM\Mapping as ORM;
use Sfynx\CoreBundle\Layers\Domain\Model\Interfaces\EntityInterface;
use Sfynx\AuthBundle\Domain\ValueObject\LangueVO;

/**
 * Class Langue
 *
 * @category Sfynx\AuthBundle
 * @package Domain
 * @subpackage Entity
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="Pi_langue")
 *
 * ODM\Document(collection="Pi_langue")
 * ODM\HasLifecycleCallbacks
 *
 * CouchDB\Document
 *
 * @copyright Copyright (c) 2016-2017, Aareon Group
 * @license http://www.pigroupe.com under a proprietary license
 * @version 1.1.1
 */
class Langue implements EntityInterface
{
    /**
     * @var integer|string Unique identifier of the Langue.
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
     * @var LangueVO
     *
     * @JMS\Serializer\Annotation\Since("1")
     * @ORM\Embedded(class="Sfynx\AuthBundle\Domain\ValueObject\LangueVO", columnPrefix=false)
     * ODM\EmbedOne(targetDocument="Sfynx\AuthBundle\Domain\ValueObject\LangueVO")
     * CouchDB\EmbedOne(targetDocument="Sfynx\AuthBundle\Domain\ValueObject\LangueVO")
     */
    protected $langue;

    /**
     * Builds the entity based on the value object associated.
     *
     * @param LangueVO $langue
     * @return Langue
     */
    public static function build(LangueVO $langue): Langue
    {
        return new self($langue);
    }

    /**
     * Langue constructor.
     *
     * @param LangueVO $langue
     */
    protected function __construct(LangueVO $langue)
    {
        $this->setLangue($langue);
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
     * @return Langue
     */
    public function setId($id): Langue
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Returns the langue.
     *
     * @return LangueVO
     */
    public function getLangue(): LangueVO
    {
        return $this->langue;
    }

    /**
     * Sets the langue.
     *
     * @param LangueVO $langue
     * @return Langue
     */
    public function setLangue(LangueVO $langue): Langue
    {
        $this->langue = $langue;
        return $this;
    }
}
