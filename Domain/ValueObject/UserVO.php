<?php
namespace Sfynx\AuthBundle\Domain\ValueObject;

use DateTime;
use Doctrine\ODM\CouchDB\Mapping\Annotations as CouchDB;
use Doctrine\ORM\Mapping as ORM;

use Sfynx\AuthBundle\Domain\Generalisation\Interfaces\UserInterface;
use Sfynx\CoreBundle\Layers\Domain\ValueObject\Generalisation\AbstractVO;
use Sfynx\CoreBundle\Layers\Domain\Model\Interfaces\TraitDatetimeInterface;
use Sfynx\CoreBundle\Layers\Domain\Model\Interfaces\TraitEnabledInterface;
use Sfynx\CoreBundle\Layers\Domain\ValueObject\Generalisation\Interfaces\ValueObjectInterface;
use Sfynx\CoreBundle\Layers\Domain\ValueObject\Generalisation\Traits;

/**
 * Class UserVO
 *
 * @category Sfynx\AuthBundle
 * @package Domain
 * @subpackage ValueObject
 * @final
 *
 * @ORM\Embeddable
 * ODM\EmbeddedDocument
 * CouchDB\EmbeddedDocument
 */
final class UserVO extends AbstractVO implements UserInterface,TraitDatetimeInterface,TraitEnabledInterface
{
    use Traits\TraitUsername;
    use Traits\TraitName;
    use Traits\TraitNickname;
    use Traits\TraitEmail;
    use Traits\TraitBirthday;
    use Traits\TraitAddress;
    use Traits\TraitCountry;
    use Traits\TraitCity;
    use Traits\TraitZipcode;
    use Traits\TraitDatetime;
    use Traits\TraitExpire;
    use Traits\TraitLocked;
    use Traits\TraitCredentialExpired;
    use Traits\TraitOptIn;
    use Traits\TraitEnabled;

    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. static::$fieldNames[static::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        self::TYPE_PHPNAME => array ('Id', 'Username', 'UsernameCanonical', 'Email', 'EmailCanonical', 'Enabled', 'Salt', 'Password', 'LastLogin', 'Locked', 'Expired', 'ExpiresAt', 'ConfirmationToken', 'PasswordRequestedAt', 'CredentialsExpired', 'CredentialsExpireAt', 'Roles', 'Name', 'Nickname', 'GlobalOptIn', 'SiteOptIn', 'Birthday', 'Address', 'ZipCode', 'City', 'Country', 'CreatedAt', 'UpdatedAt', ),
        self::TYPE_STUDLYPHPNAME => array ('id', 'username', 'usernameCanonical', 'email', 'emailCanonical', 'enabled', 'salt', 'password', 'lastLogin', 'locked', 'expired', 'expiresAt', 'confirmationToken', 'passwordRequestedAt', 'credentialsExpired', 'credentialsExpireAt', 'roles', 'Name', 'nickname', 'globalOptIn', 'siteOptIn', 'birthday', 'address', 'zipCode', 'city', 'country', 'createdAt', 'updatedAt', ),
        self::TYPE_COLNAME => array (self::ID, self::USERNAME, self::USERNAME_CANONICAL, self::EMAIL, self::EMAIL_CANONICAL, self::ENABLED, self::SALT, self::PASSWORD, self::LAST_LOGIN, self::LOCKED, self::EXPIRED, self::EXPIRES_AT, self::CONFIRMATION_TOKEN, self::PASSWORD_REQUESTED_AT, self::CREDENTIALS_EXPIRED, self::CREDENTIALS_EXPIRE_AT, self::ROLES, self::NAME, self::NICKNAME, self::GLOBAL_OPT_IN, self::SITE_OPT_IN, self::BIRTHDAY, self::ADDRESS, self::ZIP_CODE, self::CITY, self::COUNTRY, self::CREATED_AT, self::UPDATED_AT, ),
        self::TYPE_RAW_COLNAME => array ('ID', 'USERNAME', 'USERNAME_CANONICAL', 'EMAIL', 'EMAIL_CANONICAL', 'ENABLED', 'SALT', 'PASSWORD', 'LAST_LOGIN', 'LOCKED', 'EXPIRED', 'EXPIRES_AT', 'CONFIRMATION_TOKEN', 'PASSWORD_REQUESTED_AT', 'CREDENTIALS_EXPIRED', 'CREDENTIALS_EXPIRE_AT', 'ROLES', 'NAME', 'NICKNAME', 'GLOBAL_OPT_IN', 'SITE_OPT_IN', 'BIRTHDAY', 'ADDRESS', 'ZIP_CODE', 'CITY', 'COUNTRY', 'CREATED_AT', 'UPDATED_AT', ),
        self::TYPE_FIELDNAME => array ('id', 'username', 'username_canonical', 'email', 'email_canonical', 'enabled', 'salt', 'password', 'last_login', 'locked', 'expired', 'expires_at', 'confirmation_token', 'password_requested_at', 'credentials_expired', 'credentials_expire_at', 'roles', 'NAME', 'nickname', 'global_opt_in', 'site_opt_in', 'birthday', 'address', 'zip_code', 'city', 'country', 'created_at', 'updated_at', ),
        self::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, )
    );

    /**
     * @ORM\Column(name="name", type="string", length=50, nullable = true)
     * @Assert\Length(min = 2, max = 50, minMessage = "core.field.min_length", maxMessage="core.field.max_length", groups={"registration"})
     * @Assert\NotBlank(message="core.field.required", groups={"registration"})
     * @Assert\Regex(pattern="/^[[:alpha:]\s'\x22\-_&@!?()\[\]-]*$/u", message="core.field.regex", groups={"registration"})
     */
    protected $name;

    /**
     * The salt to use for hashing
     *
     * @ORM\Column(name="salt", type="string")
     */
    protected $salt;

    /**
     * Encrypted password. Must be persisted.
     *
     * @ORM\Column(name="password", type="string")
     */
    protected $password;

    /**
     * Plain password. Used for model validation. Must not be persisted.
     *
     * @var string
     */
    protected $plainPassword;

    /**
     * @ORM\Column(name="last_login", type="datetime", nullable = true)
     */
    protected $lastLogin;

    /**
     * Random string sent to the user email address in order to verify it
     *
     * @ORM\Column(name="confirmation_token", type="string", nullable = true)
     */
    protected $confirmationToken;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="password_requested_at", type="datetime", nullable = true)
     */
    protected $passwordRequestedAt;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->enabled = false;
        $this->locked = false;
        $this->expired = false;
        $this->credentialsExpired = false;
    }

    /**
     * @return ValueObjectInterface
     */
    public function transform(): ValueObjectInterface
    {
        $this->address = self::setAddress($this->address);
        $this->country = self::setCountry($this->country);
        $this->city = self::setCity($this->city);
        $this->zip_code = self::setZipCode($this->zip_code);
        $this->global_opt_in = self::setGlobalOptIn($this->global_opt_in);
        $this->site_opt_in = self::setSiteOptIn($this->site_opt_in);
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * @return mixed
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * @param string $confirmationToken
     * @return User
     */
    public static function setConfirmationToken()
    {
        return self::generateToken();
    }

    /**
     * Gets the last login time.
     *
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Gets the encrypted password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Removes sensitive data from the user
     * @return User
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
        return $this;
    }

    /**
     * Gets the timestamp that the user requested a password reset.
     *
     * @return null|DateTime
     */
    public function getPasswordRequestedAt(): DateTime
    {
        return $this->passwordRequestedAt;
    }

    /**
     *
     */
    public function isAccountNonConfirmed()
    {
        return (null === $this->getConfirmationToken());
    }

    /**
     * @param  int  $expired
     * @return bool
     */
    public function isConnected($expired = 1800)
    {
        if ($this->lastLogin) {
            $dateLastLogin = $this->lastLogin;
            $dateTime = time() - $expired;
            if ($dateLastLogin->getTimestamp() > $dateTime) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param int $ttl
     * @return bool
     */
    public function isPasswordRequestNonExpired($ttl)
    {
        return $this->getPasswordRequestedAt() instanceof \DateTime &&
            $this->getPasswordRequestedAt()->getTimestamp() + $ttl > time();
    }

    /**
     * @return bool
     */
    public function isSuperAdmin()
    {
        return $this->hasRole(static::$ROLE_SUPER_ADMIN);
    }

    /**
     * {@inheritdoc}
     */
    public function isUser(UserInterface $user = null)
    {
        return null !== $user && $this->getId() === $user->getId();
    }

    /**
     * Populates the object using an array.
     *
     * @param array  $arr     An array to populate the object from.
     * @param string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = self::TYPE_PHPNAME)
    {
        $keys = self::getFieldNames($keyType);

        foreach ($keys as $k => $v) {
            if (array_key_exists($v, $arr)) {
                $this->$v = $arr[$v];
            }
        }
    }

    /**
     * Returns an array of field names.
     *
     * @param      string $type The type of fieldnames to return:
     *                      One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                      BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
     * @return array           A list of field names
     * @throws \Exception - if the type is not valid.
     */
    public static function getFieldNames($type)
    {
        if (!array_key_exists($type, self::$fieldNames)) {
            throw new \Exception('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
        }
        return static::$fieldNames[$type];
    }

    /**
     * @return string
     */
    public static function generateToken()
    {
        return rtrim(strtr(base64_encode(self::getRandomNumber()), '+/', '-_'), '=');
    }

    /**
     * @return string
     */
    public static function getRandomNumber()
    {
        return hash('sha256', uniqid(mt_rand(), true), true);
    }
}
