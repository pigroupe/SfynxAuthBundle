<?php
namespace Sfynx\AuthBundle\Domain\EntityVO;

use Doctrine\ODM\CouchDB\Mapping\Annotations as CouchDB;
use Doctrine\ORM\Mapping as ORM;

use Sfynx\CoreBundle\Layers\Domain\Model\Interfaces\EntityInterface;
use Sfynx\AuthBundle\Domain\ValueObject\UserVO;
use Sfynx\AuthBundle\Domain\Generalisation as TraitsAuth;

/**
 * Class User
 *
 * @category Sfynx\AuthBundle
 * @package Domain
 * @subpackage EntityVO
 *
 * @ORM\MappedSuperclass
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
 * ODM\Document(collection="fos_user")
 * ODM\HasLifecycleCallbacks
 *
 * CouchDB\Document
 */
class User implements EntityInterface
{
    use TraitsAuth\TraitLangue;
    use TraitsAuth\TraitGroups;
    use TraitsAuth\TraitRoles;
    use TraitsAuth\TraitPermissions;
    use TraitsAuth\TraitApplicationTokens;

    /**
     * @var integer|string Unique identifier of the User.
     *
     * @JMS\Serializer\Annotation\Since("1")
     * @ORM\Column(type="integer", name="ID")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * ODM\Id(strategy="AUTO", type="string", name="ID")
     * CouchDB\Id(strategy="UUID")
     */
    protected $id;

    /**
     * @var UserVO
     *
     * @JMS\Serializer\Annotation\Since("1")
     * @ORM\Embedded(class="Sfynx\AuthBundle\Domain\ValueObject\UserVO", columnPrefix=false)
     * ODM\EmbedOne(targetDocument="Sfynx\AuthBundle\Domain\ValueObject\UserVO")
     * CouchDB\EmbedOne(targetDocument="Sfynx\AuthBundle\Domain\ValueObject\UserVO")
     */
    protected $user;

    /**
     * Builds the entity based on the value object associated.
     *
     * @param Langue $lang_code
     * @param UserVO $user
     * @return User
     */
    public static function build(Langue $lang_code, UserVO $user): User
    {
        return new self($lang_code, $user);
    }

    /**
     * User constructor.
     *
     * @param Langue $lang_code
     * @param UserVO $user
     */
    protected function __construct(Langue $lang_code, UserVO $user)
    {
        $this->setLangCode($lang_code);
        $this->setUser($user);
    }

    /**
     * Returns the id.
     *
     * @return int|string|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the id.
     *
     * @param int|string $id
     * @return User
     */
    public function setId($id): User
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Returns the user.
     *
     * @return UserVO
     */
    public function getUser(): UserVO
    {
        return $this->user;
    }

    /**
     * Sets the user.
     *
     * @param UserVO $user
     * @return User
     */
    public function setUser(UserVO $user): User
    {
        $this->user = $user;
        return $this;
    }

    /**
     *
     * This method is used when you want to convert to string an object of
     * this Entity
     * ex: $value = (string)$entity;
     *
     */
    public function __toString()
    {
        $content  = $this->getId();
        $username = $this->getUser()->getUsername();
        $mail     = $this->getUser()->getEmail();
        $name     = $this->getUser()->getName();
        $nickame  = $this->getUser()->getNickname();
        if ($username) {
            $content .=  "- " . $username;
        }
        if ($mail) {
            $content .=  "- " . $mail;
        }
        if ($name && $nickame) {
            $content .=  " (" . $name . " ". $nickame . ")";
        }
        return (string) $content;
    }

    /**
     * Serializes the user.
     *
     * The serialized data have to contain the fields used during check for
     * changes and the id.
     *
     * @return string
     */
    public function serialize()
    {
        return serialize([
            $this->getUser()->getPassword(),
            $this->getUser()->getSalt(),
            $this->getUser()->getUsernameCanonical(),
            $this->getUser()->getUsername(),
            $this->getUser()->getExpired(),
            $this->getUser()->getLocked(),
            $this->getUser()->getCredentialsExpired(),
            $this->getUser()->getEnabled(),
            $this->getId(),
            $this->getUser()->getExpiresAt(),
            $this->getUser()->getCredentialsExpireAt(),
            $this->getUser()->getEmail(),
            $this->getUser()->getEmailCanonical(),
        ]);
    }

    /**
     * Unserializes the user.
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        // add a few extra elements in the array to ensure that we have enough keys when unserializing
        // older data which does not include all properties.
        $data = array_merge($data, array_fill(0, 2, null));

        list(
            $this->getUser()->getPassword(),
            $this->getUser()->getSalt(),
            $this->getUser()->getUsernameCanonical(),
            $this->getUser()->getUsername(),
            $this->getUser()->getExpired(),
            $this->getUser()->getLocked(),
            $this->getUser()->getCredentialsExpired(),
            $this->getUser()->getEnabled(),
            $this->getUser()->getId(),
            $this->getUser()->getExpiresAt(),
            $this->getUser()->getCredentialsExpireAt(),
            $this->getUser()->getEmail(),
            $this->getUser()->getEmailCanonical()
        ) = $data;
    }
}
