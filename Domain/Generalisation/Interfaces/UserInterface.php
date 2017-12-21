<?php
namespace Sfynx\AuthBundle\Domain\Generalisation\Interfaces;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Sfynx\CoreBundle\Layers\Domain\Model\Interfaces\TraitDatetimeInterface;
use Sfynx\CoreBundle\Layers\Domain\Model\Interfaces\TraitEnabledInterface;
use Sfynx\AuthBundle\Domain\Entity\Langue;

/**
 * User Interface
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
interface UserInterface extends AdvancedUserInterface,TraitDatetimeInterface,TraitEnabledInterface,\Serializable
{
    /**
     * phpname type
     * e.g. 'AuthorId'
     */
    const TYPE_PHPNAME = 'phpName';

    /**
     * studlyphpname type
     * e.g. 'authorId'
     */
    const TYPE_STUDLYPHPNAME = 'studlyPhpName';

    /**
     * column (peer) name type
     * e.g. 'book.AUTHOR_ID'
     */
    const TYPE_COLNAME = 'colName';

    /**
     * column part of the column peer name
     * e.g. 'AUTHOR_ID'
     */
    const TYPE_RAW_COLNAME = 'rawColName';

    /**
     * column fieldname type
     * e.g. 'author_id'
     */
    const TYPE_FIELDNAME = 'fieldName';

    /**
     * num type
     * simply the numerical array index, e.g. 4
     */
    const TYPE_NUM = 'num';

    /** the column name for the id field */
    const ID = 'sfynx_user.id';
    /** the column name for the username field */
    const USERNAME = 'sfynx_user.username';
    /** the column name for the username_canonical field */
    const USERNAME_CANONICAL = 'sfynx_user.username_canonical';
    /** the column name for the email field */
    const EMAIL = 'sfynx_user.email';
    /** the column name for the email_canonical field */
    const EMAIL_CANONICAL = 'sfynx_user.email_canonical';
    /** the column name for the enabled field */
    const ENABLED = 'sfynx_user.enabled';
    /** the column name for the salt field */
    const SALT = 'sfynx_user.salt';
    /** the column name for the password field */
    const PASSWORD = 'sfynx_user.password';
    /** the column name for the last_login field */
    const LAST_LOGIN = 'sfynx_user.last_login';
    /** the column name for the locked field */
    const LOCKED = 'sfynx_user.locked';
    /** the column name for the expired field */
    const EXPIRED = 'sfynx_user.expired';
    /** the column name for the expires_at field */
    const EXPIRES_AT = 'sfynx_user.expires_at';
    /** the column name for the confirmation_token field */
    const CONFIRMATION_TOKEN = 'sfynx_user.confirmation_token';
    /** the column name for the password_requested_at field */
    const PASSWORD_REQUESTED_AT = 'sfynx_user.password_requested_at';
    /** the column name for the credentials_expired field */
    const CREDENTIALS_EXPIRED = 'sfynx_user.credentials_expired';
    /** the column name for the credentials_expire_at field */
    const CREDENTIALS_EXPIRE_AT = 'sfynx_user.credentials_expire_at';
    /** the column name for the roles field */
    const ROLES = 'sfynx_user.roles';
    /** the column name for the last_name field */
    const NAME = 'sfynx_user.last_name';
    /** the column name for the first_name field */
    const NICKNAME = 'sfynx_user.first_name';
    /** the column name for the global_opt_in field */
    const GLOBAL_OPT_IN = 'sfynx_user.global_opt_in';
    /** the column name for the site_opt_in field */
    const SITE_OPT_IN = 'sfynx_user.site_opt_in';
    /** the column name for the birthday field */
    const BIRTHDAY = 'sfynx_user.birthday';
    /** the column name for the address field */
    const ADDRESS = 'sfynx_user.address';
    /** the column name for the zip_code field */
    const ZIP_CODE = 'sfynx_user.zip_code';
    /** the column name for the city field */
    const CITY = 'sfynx_user.city';
    /** the column name for the country field */
    const COUNTRY = 'sfynx_user.country';
    /** the column name for the created_at field */
    const CREATED_AT = 'sfynx_user.created_at';
    /** the column name for the updated_at field */
    const UPDATED_AT = 'sfynx_user.updated_at';

    /**
     * Set name
     *
     * @param text $name
     */
    public function setName($name);

    /**
     * Set nickname
     *
     * @param text $nickname
     */
    public function setNickname($nickname);

    /**
     * Sets the username.
     *
     * @param string $username
     *
     * @return self
     */
    public function setUsername($username);

    /**
     * Gets the canonical username in search and sort queries.
     *
     * @return string
     */
    public function getUsernameCanonical();

    /**
     * Sets the canonical username.
     *
     * @param string $usernameCanonical
     *
     * @return self
     */
    public function setUsernameCanonical($usernameCanonical);

    /**
     * Gets email.
     *
     * @return string
     */
    public function getEmail();

    /**
     * Sets the email.
     *
     * @param string $email
     *
     * @return self
     */
    public function setEmail($email);

    /**
     * Gets the canonical email in search and sort queries.
     *
     * @return string
     */
    public function getEmailCanonical();

    /**
     * Sets the canonical email.
     *
     * @param string $emailCanonical
     *
     * @return self
     */
    public function setEmailCanonical($emailCanonical);

    /**
     * Gets the plain password.
     *
     * @return string
     */
    public function getPlainPassword();

    /**
     * Sets the plain password.
     *
     * @param string $password
     *
     * @return self
     */
    public function setPlainPassword($password);

    /**
     * Sets the hashed password.
     *
     * @param string $password
     *
     * @return self
     */
    public function setPassword($password);

    /**
     * Tells if the the given user has the super admin role.
     *
     * @return boolean
     */
    public function isSuperAdmin();

    /**
     * Tells if the the given user is this user.
     *
     * Useful when not hydrating all fields.
     *
     * @param null|UserInterface $user
     *
     * @return boolean
     */
    public function isUser(UserInterface $user = null);

    /**
     * @param boolean $boolean
     *
     * @return self
     */
    public function setEnabled($boolean);

    /**
     * Sets the locking status of the user.
     *
     * @param boolean $boolean
     *
     * @return self
     */
    public function setLocked($boolean);

    /**
     * Gets the confirmation token.
     *
     * @return string
     */
    public function getConfirmationToken();

    /**
     * Sets the confirmation token
     *
     * @param string $confirmationToken
     *
     * @return self
     */
    public function setConfirmationToken($confirmationToken = '');

    /**
     * Sets the timestamp that the user requested a password reset.
     *
     * @param null|\DateTime $date
     *
     * @return self
     */
    public function setPasswordRequestedAt(\DateTime $date = null);

    /**
     * Checks whether the password reset request has expired.
     *
     * @param integer $ttl Requests older than this many seconds will be considered expired
     *
     * @return boolean true if the user's password request is non expired, false otherwise
     */
    public function isPasswordRequestNonExpired($ttl);

    /**
     * Sets the last login time
     *
     * @param \DateTime $time
     *
     * @return self
     */
    public function setLastLogin(\DateTime $time);

    /**
     * Never use this to check if this user has access to anything!
     *
     * Use the SecurityContext, or an implementation of AccessDecisionManager
     * instead, e.g.
     *
     *         $securityContext->isGranted('ROLE_USER');
     *
     * @param string $role
     *
     * @return boolean
     */
    public function hasRole(string $role): bool;

    /**
     * Sets the roles of the user.
     *
     * This overwrites any previous roles.
     *
     * @param array $roles
     *
     * @return self
     */
    public function setRoles(array $roles);

    /**
     * Adds a role to the user.
     *
     * @param string $role
     *
     * @return self
     */
    public function addRole(string $role);

    /**
     * Removes a role to the user.
     *
     * @param string $role
     *
     * @return self
     */
    public function removeRole(string $role);

    /**
     * Set permissions
     *
     * @param array $permissions
     */
    public function setPermissions(array $permissions);

    /**
     * Set langCode
     *
     * @param Langue $langCode Language entity
     */
    public function setLangCode(Langue $langCode);
}
