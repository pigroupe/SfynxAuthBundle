<?php
namespace Sfynx\AuthBundle\Application\Cqrs\User\Command;

use Sfynx\CoreBundle\Layers\Application\Command\Generalisation\AbstractCommand;

/**
 * Class FormCommand.
 *
 * @category   Sfynx\AuthBundle
 * @package    Application
 * @subpackage Cqrs\User\Command
 */
class FormCommand extends AbstractCommand
{
    /** @var int */
    public $entityId;
    /** @var string */
    public $password;
    /** @var string */
    public $plainPassword;
    /** @var string */
    public $lastLogin;
    /** @var string */
    public $confirmationToken;
    /** @var string */
    public $passwordRequestedAt;
    /** @var string */
    public $username;
    /** @var string */
    public $usernameCanonical;
    /** @var string */
    public $name;
    /** @var string */
    public $nickname;
    /** @var string */
    public $email;
    /** @var string */
    public $emailCanonical;
    /** @var string */
    public $birthday;
    /** @var string */
    public $address;
    /** @var string */
    public $country;
    /** @var string */
    public $city;
    /** @var string */
    public $zipCode;
    /** @var string */
    public $createdAt;
    /** @var string */
    public $updatedAt;
    /** @var string */
    public $publishedAt;
    /** @var string */
    public $archiveAt;
    /** @var bool */
    public $archived;
    /** @var bool */
    public $expired;
    /** @var string */
    public $expiresAt;
    /** @var bool */
    public $locked;
    /** @var bool */
    public $credentialsExpired;
    /** @var string */
    public $credentialsExpireAt;
    /** @var bool */
    public $globalOptIn;
    /** @var bool */
    public $siteOptIn;
    /** @var bool */
    public $enabled;
    /** @var array */
    public $groups;
    /** @var array */
    public $roles;
    /** @var array */
    public $permissions;
    /** @var array */
    public $applicationTokens;
    /** @var int */
    public $langCode;
}
