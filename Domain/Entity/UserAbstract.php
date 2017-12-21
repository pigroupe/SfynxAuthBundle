<?php

namespace Sfynx\AuthBundle\Domain\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Sfynx\AuthBundle\Domain\Generalisation\Interfaces\UserInterface;

abstract class UserAbstract implements UserInterface
{
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

    public static function generateToken()
    {
        return rtrim(strtr(base64_encode(self::getRandomNumber()), '+/', '-_'), '=');
    }

    public static function getRandomNumber()
    {
        return hash('sha256', uniqid(mt_rand(), true), true);
    }

    /**
     *
     * This method is used when you want to convert to string an object of
     * this Entity
     * ex: $value = (string)$entity;
     *
     */
    public function __toString() {
        $content  = $this->getId();
        $username = $this->getUsername();
        $mail     = $this->getEmail();
        $name     = $this->getName();
        $nickame  = $this->getNickname();
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
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->expired,
            $this->locked,
            $this->credentialsExpired,
            $this->enabled,
            $this->id,
            $this->expiresAt,
            $this->credentialsExpireAt,
            $this->email,
            $this->emailCanonical
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
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->expired,
            $this->locked,
            $this->credentialsExpired,
            $this->enabled,
            $this->id,
            $this->expiresAt,
            $this->credentialsExpireAt,
            $this->email,
            $this->emailCanonical
            ) = $data;
    }
}
