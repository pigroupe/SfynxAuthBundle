<?php
declare(strict_types = 1);

namespace SfynxAuthContext\Infrastructure\Persistence\FieldsDefinition;

use Sfynx\DddBundle\Layer\Infrastructure\Persistence\FieldsDefinition\FieldsDefinitionAbstract;

/**
 * Class User
 *
 * @category SfynxAuthContext
 * @package Infrastructure
 * @subpackage Persistence\FieldsDefinition
 *
 * @copyright Copyright (c) 2016-2017, Aareon Group
 * @license http://www.pigroupe.com under a proprietary license
 * @version 1.1.1
 */
class User extends FieldsDefinitionAbstract
{
    /**
     * @var string[] Associative array where keys are parameters names from the request and values are db fields names.
     */
    protected $fields = [
        'id' => 'id',
        'lang_code' => 'lang_code',
        'salt' => 'user.salt',
        'password' => 'user.password',
        'lastLogin' => 'user.lastLogin',
        'confirmationToken' => 'user.confirmationToken',
        'passwordRequestedAt' => 'user.passwordRequestedAt',
        'username' => 'user.username',
        'usernameCanonical' => 'user.usernameCanonical',
        'name' => 'user.name',
        'nickname' => 'user.nickname',
        'email' => 'user.email',
        'emailCanonical' => 'user.emailCanonical',
        'birthday' => 'user.birthday',
        'address' => 'user.address',
        'country' => 'user.country',
        'city' => 'user.city',
        'zipCode' => 'user.zipCode',
        'createdAt' => 'user.createdAt',
        'updatedAt' => 'user.updatedAt',
        'publishedAt' => 'user.publishedAt',
        'archiveAt' => 'user.archiveAt',
        'archived' => 'user.archived',
        'expired' => 'user.expired',
        'expiresAt' => 'user.expiresAt',
        'locked' => 'user.locked',
        'credentialsExpired' => 'user.credentialsExpired',
        'credentialsExpireAt' => 'user.credentialsExpireAt',
        'globalOptIn' => 'user.globalOptIn',
        'siteOptIn' => 'user.siteOptIn',
        'enabled' => 'user.enabled',
        'roles' => 'user.roles',
        'permissions' => 'user.permissions',
        'applicationTokens' => 'user.applicationTokens',
    ];
}
