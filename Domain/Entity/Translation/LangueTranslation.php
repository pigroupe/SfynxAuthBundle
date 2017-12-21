<?php
/**
 * This file is part of the <Auth> project.
 *
 * @category   Auth
 * @package    Entity
 * @subpackage Model
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2015 PI-GROUPE
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    2.3
 * @link       http://opensource.org/licenses/gpl-license.php
 * @since      2015-02-16
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\AuthBundle\Domain\Entity\Translation;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation;

/**
 * @ORM\Entity(repositoryClass="Sfynx\AuthBundle\Infrastructure\Persistence\Adapter\LangueRepository")
 * @ORM\Table(
 *         name="pi_langue_translations",
 *         uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_idx_sfynx_trans_auth_langue", columns={
 *             "locale", "object_id", "field"
 *         })}
 * )
 *
 * @category   Auth
 * @package    Entity
 * @subpackage Model
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2015 PI-GROUPE
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    2.3
 * @link       http://opensource.org/licenses/gpl-license.php
 * @since      2015-02-16
 */
class LangueTranslation extends AbstractPersonalTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="Sfynx\AuthBundle\Domain\Entity\Langue", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;

    /**
     * Convinient constructor
     *
     * @param string $locale
     * @param string $field
     * @param string $value
     */
    public function __construct($locale = null, $field = null, $value = null)
    {
        if (!(null === $locale)) {
            $this->setLocale($locale);
        }
        if (!(null === $field)) {
            $this->setField($field);
        }
        if (!(null === $value)) {
            $this->setContent($value);
        }
    }
}
