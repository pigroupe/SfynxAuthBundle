<?php
declare(strict_types = 1);

namespace SfynxAuthContext\Infrastructure\Persistence\FieldsDefinition;

use Sfynx\CoreBundle\Layers\Infrastructure\Persistence\FieldsDefinition\Generalisation\FieldsDefinitionAbstract;

/**
 * Class Permission
 *
 * @category SfynxAuthContext
 * @package Infrastructure
 * @subpackage Persistence\FieldsDefinition
 *
 * @copyright Copyright (c) 2016-2017, Aareon Group
 * @license http://www.pigroupe.com under a proprietary license
 * @version 1.1.1
 */
class Permission extends FieldsDefinitionAbstract
{
    /**
     * @var string[] Associative array where keys are parameters names from the request and values are db fields names.
     */
    protected $fields = [
        'id' => 'id',
        'name' => 'permission.name',
        'comment' => 'permission.comment',
        'enabled' => 'permission.enabled',
    ];
}
