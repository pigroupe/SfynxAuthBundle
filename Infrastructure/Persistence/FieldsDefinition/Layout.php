<?php
declare(strict_types = 1);

namespace SfynxAuthContext\Infrastructure\Persistence\FieldsDefinition;

use Sfynx\DddBundle\Layer\Infrastructure\Persistence\FieldsDefinition\FieldsDefinitionAbstract;

/**
 * Class Layout
 *
 * @category SfynxAuthContext
 * @package Infrastructure
 * @subpackage Persistence\FieldsDefinition
 *
 * @copyright Copyright (c) 2016-2017, Aareon Group
 * @license http://www.pigroupe.com under a proprietary license
 * @version 1.1.1
 */
class Layout extends FieldsDefinitionAbstract
{
    /**
     * @var string[] Associative array where keys are parameters names from the request and values are db fields names.
     */
    protected $fields = [
        'id' => 'id',
        'name' => 'layout.name',
        'filePc' => 'layout.filePc',
        'fileMobile' => 'layout.fileMobile',
        'configxml' => 'layout.configxml',
        'createdAt' => 'layout.createdAt',
        'updatedAt' => 'layout.updatedAt',
        'publishedAt' => 'layout.publishedAt',
        'archiveAt' => 'layout.archiveAt',
        'archived' => 'layout.archived',
        'enabled' => 'layout.enabled',
    ];
}
