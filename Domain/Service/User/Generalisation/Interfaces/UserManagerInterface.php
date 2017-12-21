<?php
namespace Sfynx\AuthBundle\Domain\Service\User\Generalisation\Interfaces;

use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Sfynx\AuthBundle\Domain\Generalisation\Interfaces\UserInterface;
use Sfynx\CoreBundle\Layers\Domain\Service\Manager\Generalisation\Interfaces\ManagerInterface;

/**
 * User Manager Interface
 *
 * @category   Auth
 * @package    User
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2015 PI-GROUPE
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    2.3
 * @link       http://opensource.org/licenses/gpl-license.php
 * @since      2015-02-16
 */
interface UserManagerInterface extends ManagerInterface
{
    /**
     * Authenticate a user with Symfony Security.
     *
     * @param UserInterface $user
     * @param null|Response $response
     * @param boolean       $deleteToken
     * @return Response
     * @access public
     */
    public function authenticateUser(UserInterface $user = null, &$response = null, $deleteToken = false);

    /**
     * Disconnect a user with Symfony Security.
     *
     * @return void
     * @access public
     */
    public function disconnectUser();

    /**
     * Return the token object.
     *
     * @param UserInterface $user
     * @return UsernamePasswordToken
     * @access public
     */
    public function tokenUser(UserInterface $user);

    /**
     * we check if the user ID exists in the authentication service.
     *
     * @param integer $userId
     * @return boolean
     * @access public
     */
    public function isUserdIdExisted($userId);

    /**
     * @param UserInterface|string $user A UserInterface instance or a class name
     * @return PasswordEncoderInterface
     */
    public function getEncoder(UserInterface $user);

    /**
     * we return the token associated to the user ID.
     *
     * @param integer $userId
     * @param string  $application
     * @return string
     * @access public
     */
    public function getTokenByUserIdAndApplication($userId, $application);

    /**
     * we associate the token to the userId.
     *
     * @param integer $userId
     * @param string  $token
     * @param string  $application
     * @return boolean
     * @access public
     */
    public function setAssociationUserIdWithApplicationToken($userId, $token, $application);

    /**
     * Finds one user by the given criteria.
     *
     * @param array $criteria
     *
     * @return UserInterface
     */
    public function findUserBy(array $criteria);

    /**
     * Find a user by its username.
     *
     * @param string $username
     *
     * @return UserInterface or null if user does not exist
     */
    public function findUserByUsername($username);

    /**
     * Finds a user by its email.
     *
     * @param string $email
     *
     * @return UserInterface or null if user does not exist
     */
    public function findUserByEmail($email);

    /**
     * Finds a user by its username or email.
     *
     * @param string $usernameOrEmail
     *
     * @return UserInterface or null if user does not exist
     */
    public function findUserByUsernameOrEmail($usernameOrEmail);

    /**
     * Finds a user by its confirmationToken.
     *
     * @param string $token
     *
     * @return UserInterface or null if user does not exist
     */
    public function findUserByConfirmationToken($token);

    /**
     * Updates the canonical username and email fields for a user.
     *
     * @param UserInterface $user
     *
     * @return void
     */
    public function updateCanonicalFields(UserInterface $user);

    /**
     * Updates a user password if a plain password is set.
     *
     * @param UserInterface $user
     *
     * @return void
     */
    public function updatePassword(UserInterface $user);
}
