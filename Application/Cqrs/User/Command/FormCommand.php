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
    protected $entityId;
    /** @var string */
    protected $password;
    /** @var string */
    protected $plainPassword;
    /** @var string */
    protected $lastLogin;
    /** @var string */
    protected $confirmationToken;
    /** @var string */
    protected $passwordRequestedAt;
    /** @var string */
    protected $username;
    /** @var string */
    protected $usernameCanonical;
    /** @var string */
    protected $name;
    /** @var string */
    protected $nickname;
    /** @var string */
    protected $email;
    /** @var string */
    protected $emailCanonical;
    /** @var string */
    protected $birthday;
    /** @var string */
    protected $address;
    /** @var string */
    protected $country;
    /** @var string */
    protected $city;
    /** @var string */
    protected $zipCode;
    /** @var string */
    protected $createdAt;
    /** @var string */
    protected $updatedAt;
    /** @var string */
    protected $publishedAt;
    /** @var string */
    protected $archiveAt;
    /** @var bool */
    protected $archived;
    /** @var bool */
    protected $expired;
    /** @var string */
    protected $expiresAt;
    /** @var bool */
    protected $locked;
    /** @var bool */
    protected $credentialsExpired;
    /** @var string */
    protected $credentialsExpireAt;
    /** @var bool */
    protected $globalOptIn;
    /** @var bool */
    protected $siteOptIn;
    /** @var bool */
    protected $enabled;
    /** @var array */
    protected $groups;
    /** @var array */
    protected $roles;
    /** @var array */
    protected $permissions;
    /** @var array */
    protected $applicationTokens;
    /** @var int */
    protected $langCode;
}
