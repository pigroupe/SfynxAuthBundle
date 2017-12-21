<?php
namespace Sfynx\AuthBundle\Features\Context;

use Sfynx\BehatBundle\Behat\MinkExtension\Context\FeatureContext as baseFeatureContext;

/**
 * Defines application features from the specific context.
 *
 * class FeatureContext extends MinkContext implements Context, SnippetAcceptingContext
 *
 * @category   Auth
 * @package    Feature
 * @subpackage Extends
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2015 PI-GROUPE
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    2.3
 * @link       http://opensource.org/licenses/gpl-license.php
 * @since      2015-03-02
 */
class FeatureContext extends baseFeatureContext
{
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }
}
