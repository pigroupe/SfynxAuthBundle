<?php
declare(strict_types = 1);

namespace Sfynx\AuthBundle\Domain\ValueObject;

// Import for annotations.
//use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
// Import from Sfynx\DddBundle
use Doctrine\ODM\CouchDB\Mapping\Annotations as CouchDB;
use Doctrine\ORM\Mapping as ORM;
use Sfynx\CoreBundle\Layers\Domain\ValueObject\Generalisation\AbstractVO;

/**
 * Class LangueTranslationVO
 *
 * @category Sfynx\AuthBundle
 * @package Domain
 * @subpackage ValueObject
 * @final
 *
 * @ORM\Embeddable
 * ODM\EmbeddedDocument
 * CouchDB\EmbeddedDocument
 *
 * @copyright Copyright (c) 2016-2017, Aareon Group
 * @license http://www.pigroupe.com under a proprietary license
 * @version 1.1.1
 */
final class LangueTranslationVO extends AbstractVO
{
    /**
     * @var string
     *
     * @JMS\Serializer\Annotation\Since("1")
     * @ORM\Column(name="locale", type="string")
     * ODM\Field(name="locale", type="string")
     * CouchDB\Field(type="string")
     */
    protected $locale;

    /**
     * @var string
     *
     * @JMS\Serializer\Annotation\Since("1")
     * @ORM\Column(name="field", type="string")
     * ODM\Field(name="field", type="string")
     * CouchDB\Field(type="string")
     */
    protected $field;

    /**
     * @var string
     *
     * @JMS\Serializer\Annotation\Since("1")
     * @ORM\Column(name="content", type="string")
     * ODM\Field(name="content", type="string")
     * CouchDB\Field(type="string")
     */
    protected $content;

    /**
     * Returns the locale.
     *
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * Returns the field.
     *
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * Returns the content.
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }
}
