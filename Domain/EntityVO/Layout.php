<?php

namespace Sfynx\AuthBundle\Domain\EntityVO;

use Doctrine\ODM\CouchDB\Mapping\Annotations as CouchDB;
use Doctrine\ORM\Mapping as ORM;
use Sfynx\CoreBundle\Layers\Domain\Model\Interfaces\EntityInterface;
use Sfynx\AuthBundle\Domain\ValueObject\LayoutVO;

/**
 * Class Layout
 *
 * @category Sfynx\AuthBundle
 * @package Domain
 * @subpackage Entity
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="Pi_layout")
 *
 * ODM\Document(collection="Pi_layout")
 * ODM\HasLifecycleCallbacks
 *
 * CouchDB\Document
 *
 * @copyright Copyright (c) 2016-2017, Aareon Group
 * @license http://www.pigroupe.com under a proprietary license
 * @version 1.1.1
 */
class Layout implements EntityInterface
{
    /**
     * @var integer|string Unique identifier of the Layout.
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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Sfynx\AuthBundle\Domain\Entity\Role", mappedBy="layout", cascade={"persist"})
     */
    protected $roles;

    /**
     * @var LayoutVO
     *
     * @JMS\Serializer\Annotation\Since("1")
     * @ORM\Embedded(class="Sfynx\AuthBundle\Domain\ValueObject\LayoutVO", columnPrefix=false)
     * ODM\EmbedOne(targetDocument="Sfynx\AuthBundle\Domain\ValueObject\LayoutVO")
     * CouchDB\EmbedOne(targetDocument="Sfynx\AuthBundle\Domain\ValueObject\LayoutVO")
     */
    protected $layout;

    /**
     * Builds the entity based on the value object associated.
     *
     * @param LayoutVO $layout
     * @return Layout
     */
    public static function build(LayoutVO $layout): Layout
    {
        return new self($layout);
    }

    /**
     * Layout constructor.
     *
     * @param LayoutVO $layout
     */
    protected function __construct(LayoutVO $layout)
    {
        $this->setLayout($layout);
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
     * @return Layout
     */
    public function setId($id): Layout
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Returns the layout.
     *
     * @return LayoutVO
     */
    public function getLayout(): LayoutVO
    {
        return $this->layout;
    }

    /**
     * Sets the layout.
     *
     * @param LayoutVO $layout
     * @return Layout
     */
    public function setLayout(LayoutVO $layout): Layout
    {
        $this->layout = $layout;
        return $this;
    }
}
