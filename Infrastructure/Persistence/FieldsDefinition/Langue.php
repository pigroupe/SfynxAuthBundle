<?php
declare(strict_types = 1);

namespace SfynxAuthContext\Infrastructure\Persistence\FieldsDefinition;

use Sfynx\DddBundle\Layer\Infrastructure\Persistence\FieldsDefinition\FieldsDefinitionAbstract;

/**
 * Class Langue
 *
 * @category SfynxAuthContext
 * @package Infrastructure
 * @subpackage Persistence\FieldsDefinition
 *
 * @copyright Copyright (c) 2016-2017, Aareon Group
 * @license http://www.pigroupe.com under a proprietary license
 * @version 1.1.1
 */
class Langue extends FieldsDefinitionAbstract
{
    /**
     * @var string[] Associative array where keys are parameters names from the request and values are db fields names.
     */
    protected $fields = [
        'id' => 'id',
        'label' => 'langue.label',
        'archived' => 'langue.archived',
        'createdAt' => 'langue.createdAt',
        'updatedAt' => 'langue.updatedAt',
        'publishedAt' => 'langue.publishedAt',
        'archiveAt' => 'langue.archiveAt',
        'enabled' => 'langue.enabled',
    ];
}
