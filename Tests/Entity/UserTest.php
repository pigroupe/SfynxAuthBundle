<?php
/**
 * This file is part of the <Auth> project.
 *
 * @category   Auth
 * @package    Tests
 * @subpackage Entity
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
namespace Sfynx\AuthBundle\Tests\Entity;

use Sfynx\AuthBundle\Tests\WebTestCase;
use Sfynx\AuthBundle\Domain\Entity\User;

/**
 * Tests the user entity
 *
 * @category   Auth
 * @package    Tests
 * @subpackage Entity
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @group      functional
 * @group      database
 */
class UserTest extends WebTestCase
{
    /**
     * Form data fixtures. In longer term this could be moved to a YAML file?
     */
    private static $fixtures = array(
        'site_login_creation' => array(
            'form' => array(
                'username' => 'AdminUsername',
                'nickname' => 'Admin',
                'name' => 'Admin',
                'email' => array('email' => 'example@example.org', 'confirm_email' => 'example@example.org'),
                'plainPassword' => array('password' => 'password', 'confirm' => 'password'),
                //'global_opt_in' => false,
                'zip_code' => '75017',
                'city' => 'Paris',
                'site_opt_in' => false,
                'captcha' => '1234'
            ),
            'doctrine' => array(
                'Username' => 'AdminUsername',
                'Nickname' => 'Admin',
                'Name' => 'Admin',
                'Email' => 'test@example.org',
                'PlainPassword' => '123456789',
                'ZipCode' => '75017',
                'City' => 'Paris',
            )
        ),
        'site_personal_data' => array(
            'form' => array(
                'birthday' => array('year' => 1980, 'month' => 1, 'day' => 1),
                'address' => '48, bd des Batignolles',
                'country' => 'FR',
            ),
            'doctrine' => array(
                'Address' => '48, bd des Batignolles',
                'Country' => 'FR',
                'Birthday' => '2000-01-02',
            ),
        ),
    );

    public function testInterface()
    {
        $user = new User();
        $this->assertInstanceOf('FOS\UserBundle\Model\UserInterface', $user);
    }

    public function testDefaults()
    {
        $user = new User();

        $this->assertNull($user->getName());
        $this->assertNull($user->getNickname());
        $this->assertNull($user->getGlobalOptIn());
        $this->assertNull($user->getSiteOptIn());
    }

    /**
     * @dataProvider getInvalidData
     */
    public function testRegistrationValidator($data, $field)
    {
        $user = new User();
        $user->fromArray($data);
        $user->setPlainPassword($data['PlainPassword']);

        $errors = $this->getValidator()->validate($user, array('registration'));

        $this->assertCount(1, $errors, (string) $errors);
        $this->assertRegExp('/' . $field . '/', (string) $errors);
    }

    /**
     * @dataProvider getInvalidPersonalData
     */
    public function testPersonalDataValidator($data, $field)
    {
        $user = new User();
        $user->fromArray($data);

        $errors = $this->getValidator()->validate($user, array('personal_data'));
        $this->assertCount(1, $errors, (string) $errors);
        $this->assertRegExp('/' . $field . '/', (string) $errors);
    }

    /**
     * @return array An array containing multiple arrays in form of
     *               array($formValues, $invalidFieldName)
     */
    public function getInvalidPersonalData()
    {
        return array(
            array(
                static::getFixtures(
                    'site_personal_data',
                    array('Address' => str_repeat(implode('', range('a', 'z')), 6)),
                    array(),
                    'doctrine'
                ),
                'address'
            ),
            array(
                static::getFixtures(
                    'site_personal_data',
                    array('Address' => '%$24'),
                    array(),
                    'doctrine'
                ),
                'address'
            ),
        );
    }

    /**
     * @return array An array containing multiple arrays in form of
     *               array($formValues, $invalidFieldName)
     */
    public function getInvalidData()
    {
        return array(
            array(
                static::getFixtures(
                    'site_login_creation',
                    array('Nickname' => ''),
                    array('Name' => 'âéèìùò'),
                    'doctrine'
                ),
                'nickname'
            ),
            array(
                static::getFixtures('site_login_creation', array('Email' => 'xyz@test'), array(), 'doctrine'),
                'email'
            ),
            array(
                static::getFixtures(
                    'site_login_creation',
                    array('City' => str_repeat(implode('', range('a', 'z')), 6)),
                    array(),
                    'doctrine'
                ),
                'city'
            ),
            array(
                static::getFixtures(
                    'site_login_creation',
                    array('ZipCode' => 'WC1E 6HJ'),
                    array('City' => 'Maïüöùîû'),
                    'doctrine'
                ),
                'zip_code'
            ),
        );
    }

    public function testIsConnected()
    {
        $user = new User();
        $this->assertFalse($user->isConnected());

        $date = new \DateTime();
        $user->setLastLogin($date);
        $this->assertTrue($user->isConnected());

        $date->add(new \DateInterval('PT3600S'));
        $user->setLastLogin($date);
        $this->assertTrue($user->isConnected());

        $date->sub(new \DateInterval('PT7200S'));
        $user->setLastLogin($date);
        $this->assertFalse($user->isConnected());
    }

    /**
     * Common getter for all fixtures
     *
     * @param string $name          The form name
     * @param array  $invalidValues Values to override expected to be invalid
     * @param array  $valudValues   Values to override expected to be valid
     * @param string $format        The values format (doctrine|form)
     */
    public static function getFixtures(
        $name,
        array $invalidValues = array(),
        array $validValues = array(),
        $type = 'form'
    ) {
        if (!isset(self::$fixtures[$name][$type])) {
            throw new \InvalidArgumentException('Undefined fixture: ' . $name . ' or type: ' . $type);
        }

        return array_merge(self::$fixtures[$name][$type], $invalidValues, $validValues);
    }
}
