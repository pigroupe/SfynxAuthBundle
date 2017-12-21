<?php
/**
 * This file is part of the <Auth> project.
 *
 * @subpackage   Authentication
 * @package    DataFixtures
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @since 2011-12-28
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\AuthBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Sfynx\AuthBundle\Domain\Entity\User;
use Sfynx\ToolBundle\Util\PiStringManager;

/**
 * Users DataFixtures.
 *
 * @subpackage   Authentication
 * @package    DataFixtures
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
class UsersFixtures extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
{
    const USER_EMAIL     = 'user@example.org';
    const USER_USERNAME  = 'user';
    const USER_PASS      = 'testtest';
    const USER_PASSWORD  = 'jMhPNtk/r/aDmrihsK2jw+D+zpnSxBxCL5v1tvCWZd/I4N7/gJiAjVPS0Xy2XkbVpVOPjgSHBBsskDmHWqEo4Q==';

    const ADMIN_EMAIL    = 'admin@example.org';
    const ADMIN_USERNAME = 'admin';
    const ADMIN_PASS     = 'testtest';
    const ADMIN_PASSWORD = 'jMhPNtk/r/aDmrihsK2jw+D+zpnSxBxCL5v1tvCWZd/I4N7/gJiAjVPS0Xy2XkbVpVOPjgSHBBsskDmHWqEo4Q==';

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Load user fixtures
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     * @since 2011-12-28
     */
    public function load(ObjectManager $manager, $env = '')
    {
        $encoder = $this->container
            ->get('security.encoder_factory')
        ;

        $field1 = new User();
        $field1->setUsername('admin');
        $field1->getUsernameCanonical('admin');
//        $field1->setSalt(md5(uniqid()));
        $field1->setPassword($encoder->getEncoder($field1)->encodePassword('admin', $field1->getSalt()));
        $field1->setConfirmationToken();
        $field1->setEmail('admin@hotmail.com');
        $field1->setUsernameCanonical(PiStringManager::canonicalize($field1->getUsername()));
        $field1->setEmailCanonical(PiStringManager::canonicalize($field1->getEmail()));
        $field1->setEnabled(true);
        $field1->setRoles(array('ROLE_ADMIN'));
        $field1->setPermissions(array('VIEW', 'EDIT', 'CREATE', 'DELETE'));
        $field1->addGroup($this->getReference('group-admin'));
        $field1->setLangCode($this->getReference('lang-en'));
        $manager->persist($field1);

        $field2 = new User();
        $field2->setUsername('superadmin');
        $field2->setUsernameCanonical('superadmin');
//        $field2->setSalt(md5(uniqid()));
        $field2->setPassword($encoder->getEncoder($field2)->encodePassword('superadmin', $field2->getSalt()));
        $field2->setConfirmationToken();
        $field2->setEmail('superadmin@gmail.com');
        $field2->setUsernameCanonical(PiStringManager::canonicalize($field2->getUsername()));
        $field2->setEmailCanonical(PiStringManager::canonicalize($field2->getEmail()));
        $field2->setEnabled(true);
        $field2->setRoles(array('ROLE_ADMIN', 'ROLE_SUPER_ADMIN'));
        $field2->setPermissions(array('VIEW', 'EDIT', 'CREATE', 'DELETE'));
        $field2->addGroup($this->getReference('group-superadmin'));
        $field2->setLangCode($this->getReference('lang-en'));
        $manager->persist($field2);

        $field3 = new User();
        $field3->setUsername('user');
        $field3->getUsernameCanonical('user');
//        $field3->setSalt(md5(uniqid()));
        $field3->setPassword($encoder->getEncoder($field3)->encodePassword('user', $field3->getSalt()));
        $field3->setConfirmationToken();
        $field3->setEmail('user@gmail.com');
        $field3->setUsernameCanonical(PiStringManager::canonicalize($field3->getUsername()));
        $field3->setEmailCanonical(PiStringManager::canonicalize($field3->getEmail()));
        $field3->setEnabled(true);
        $field3->setRoles(array('ROLE_USER'));
        $field3->setPermissions(array('VIEW', 'EDIT', 'CREATE'));
        $field3->addGroup($this->getReference('group-user'));
        $field3->setLangCode($this->getReference('lang-fr'));
        $manager->persist($field3);

        $field4 = new User();
        $field4->setName('Islam');
        $field4->setNickname('Ahmad');
        $field4->getUsernameCanonical(self::ADMIN_USERNAME);
        $field4->setUsername(self::ADMIN_USERNAME);
        $field4->setPlainPassword(self::ADMIN_PASS);
        $field4->setConfirmationToken();
        $field4->setSalt('5467p78mqssowokg4gc0k4kcs08kkk8');
        $field4->setPassword(self::ADMIN_PASSWORD);
        $field4->setEmail(self::ADMIN_EMAIL);
        $field4->setEmailCanonical(self::ADMIN_EMAIL);
        $field4->setEnabled(true);
        $field4->setRoles(array('ROLE_ADMIN'));
        $field4->setPermissions(array('VIEW', 'EDIT', 'CREATE', 'DELETE'));
        $field4->addGroup($this->getReference('group-admin'));
        $field4->setLangCode($this->getReference('lang-en'));
        $manager->persist($field4);

        $field5 = new User();
        $field5->setName('Islam');
        $field5->setNickname('Issa');
        $field5->getUsernameCanonical(self::USER_USERNAME);
        $field5->setUsername(self::USER_USERNAME);
        $field5->setPlainPassword(self::USER_PASS);
        $field5->setConfirmationToken();
        $field5->setSalt('5467p78mqssowokg4gc0k4kcs08kkk8');
        $field5->setPassword(self::USER_PASSWORD);
        $field5->setEmail(self::USER_EMAIL);
        $field5->setEmailCanonical(self::USER_EMAIL);
        $field5->setEnabled(true);
        $field5->setRoles(array('ROLE_USER'));
        $field5->setPermissions(array('VIEW', 'EDIT', 'CREATE', 'DELETE'));
        $field5->addGroup($this->getReference('group-user'));
        $field5->setLangCode($this->getReference('lang-fr'));
        $manager->persist($field5);

//         $path   = "/var/www/rapp_mr_miles/app/cache/connexion.csv";
//         file_put_contents($path, 'username,password'."\n", LOCK_EX);
//         for ($i=1; $i< 10000; $i++){
//         	$field = new User();
//         	$field->setPlainPassword('user_'.$i);
//         	$field->setUsername('user_'.$i.'@mail.com');
//         	$field->getUsernameCanonical('user_'.$i.'@mail.com');
//         	$field->setFirstName('user_'.$i);
//         	$field->setLastName('user_'.$i);
//         	$field->setEmail('user_'.$i.'@mail.com');
//         	$field->setEmailCanonical('user_'.$i.'@mail.com');
//         	$field->setEnabled(true);
//         	$field->setRoles(array('ROLE_USER'));
//         	$manager->persist($field);
//         	file_put_contents($path, 'user_'.$i.'@mail.com,user_'.$i."\n", FILE_APPEND);
//         }

        $manager->flush();

        $this->addReference('user-admin', $field1);
        $this->addReference('user-superadmin', $field2);
        $this->addReference('user-user', $field3);
        $this->addReference('user-admin-test', $field4);
        $this->addReference('user-user-test', $field5);
    }

    /**
     * Retrieve the order number of current fixture
     *
     * @return integer
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     * @since 2011-12-28
     */
    public function getOrder()
    {
        // The order in which fixtures will be loaded
        return 2;
    }
}
