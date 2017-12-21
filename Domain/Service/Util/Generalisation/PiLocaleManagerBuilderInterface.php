<?php
/**
 * This Locale is part of the <Auth> project.
 *
 * @subpackage   Auth
 * @package    Builder
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @since 2012-01-18
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\AuthBundle\Domain\Service\Util\Generalisation;

/**
 * PiLocaleManagerBuilderInterface interface.
 *
 * @subpackage   Auth
 * @package    Builder
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
interface PiLocaleManagerBuilderInterface
{
    public function parseDefaultLanguage($deflang = "fr");
    public function getAllLocales($all = false);
    public function setJsonFileLocales();
}
