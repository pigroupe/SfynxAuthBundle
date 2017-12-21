<?php
namespace Sfynx\AuthBundle\Domain\Generalisation;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

use Sfynx\AuthBundle\Domain\Entity\Langue;

/**
 * abstract class for langue attributs.
 *
 * @category   Generalisation
 * @package    Trait
 * @subpackage Entity
 * @abstract
 */
trait TraitLangue
{
    /**
     * @var string $langCode
     *
     * @ORM\ManyToOne(targetEntity="Sfynx\AuthBundle\Domain\Entity\Langue", cascade={"persist", "detach"})
     * @ORM\JoinColumn(name="lang_code", referencedColumnName="id", nullable=true)
     */
    protected $langCode;

    /**
     * Set langCode
     *
     * @param Langue $langCode Language entity
     */
    public function setLangCode(Langue $langCode)
    {
        $this->langCode = $langCode;
    }

    /**
     * Get langCode
     *
     * @return Langue|null
     */
    public function getLangCode()
    {
        return $this->langCode;
    }
}
