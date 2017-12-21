<?php
namespace Sfynx\AuthBundle\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Sfynx\AuthBundle\Domain\Generalisation as TraitsAuth;
use Sfynx\AuthBundle\Domain\Generalisation\Interfaces\UserInterface;
use Sfynx\CoreBundle\Layers\Domain\Model\Interfaces\EntityInterface;
use Sfynx\CoreBundle\Layers\Domain\Model\Traits;
use Sfynx\ToolBundle\Util\PiDateManager;

/**
 * Storage agnostic overloding fos user object
 *
 * @ORM\MappedSuperclass
 * @ORM\Entity(repositoryClass="Sfynx\AuthBundle\Infrastructure\Persistence\Adapter\Command\Orm\UserRepository")
 * @ORM\Table(name="fos_user", indexes={
 *      @ORM\Index(name="emailCanonical_idx", columns={"email_canonical"}),
 *      @ORM\Index(name="email_idx", columns={"email"})
 * })
 *
 * @UniqueEntity(
 *     fields={"email"},
 *     message="Your E-Mail adress has already been registered",
 *     groups={"registration"}
 * )
 * @UniqueEntity(
 *     fields={"emailCanonical"},
 *     message="Your E-Mail adress has already been registered",
 *     groups={"registration"}
 * )
 *
 * @ORM\HasLifecycleCallbacks
 *
 * @category   Sfynx\AuthBundle
 * @package    Domain
 * @subpackage Entity
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2015 PI-GROUPE
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    2.3
 * @link       http://opensource.org/licenses/gpl-license.php
 * @since      2015-02-16
 */
class User extends UserAbstract implements EntityInterface
{
    use Traits\TraitBuild;

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
    use TraitsAuth\TraitLangue;
    use TraitsAuth\TraitGroups;
    use TraitsAuth\TraitRoles;
    use TraitsAuth\TraitPermissions;
    use TraitsAuth\TraitApplicationTokens;

    /**
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

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
        $this->applicationTokens = [];
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->enabled = false;
        $this->locked = false;
        $this->expired = false;
        $this->credentialsExpired = false;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @return integer
     */
    public function setId($id)
    {
    	$this->id = (int) $id;
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
    public function setConfirmationToken($confirmationToken = '')
    {
        if (empty($confirmationToken)) {
            $this->confirmationToken = self::generateToken();
        }
        return $this;
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
     * @param \DateTime $time
     * @return User
     */
    public function setLastLogin(\DateTime $time)
    {
        $this->lastLogin = $time;
        return $this;
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
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
        return $this;
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
     * @param \DateTime|null $date
     * @return User
     */
    public function setPasswordRequestedAt(\DateTime $date = null)
    {
        $this->passwordRequestedAt = $date;
        return $this;
    }

    /**
     * Gets the timestamp that the user requested a password reset.
     *
     * @return null|\DateTime
     */
    public function getPasswordRequestedAt()
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
     * @param UserInterface|null $user
     * @return bool
     */
    public function isUser(UserInterface $user = null)
    {
        return null !== $user && $this->getId() === $user->getId();
    }
}
