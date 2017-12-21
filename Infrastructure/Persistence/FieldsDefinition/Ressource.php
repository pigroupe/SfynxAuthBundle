<?php
declare(strict_types = 1);

namespace SfynxAuthContext\Infrastructure\Persistence\FieldsDefinition;

use Sfynx\DddBundle\Layer\Infrastructure\Persistence\FieldsDefinition\FieldsDefinitionAbstract;

/**
 * Class Ressource
 *
 * @category SfynxAuthContext
 * @package Infrastructure
 * @subpackage Persistence\FieldsDefinition
 *
 * @copyright Copyright (c) 2016-2017, Aareon Group
 * @license http://www.pigroupe.com under a proprietary license
 * @version 1.1.1
 */
class Ressource extends FieldsDefinitionAbstract
{
    /**
     * @var string[] Associative array where keys are parameters names from the request and values are db fields names.
     */
    protected $fields = [
        'id' => 'id',
        'routeName' => 'ressource.routeName',
        'slug' => 'ressource.slug',
        'url' => 'ressource.url',
        'createdAt' => 'ressource.createdAt',
        'updatedAt' => 'ressource.updatedAt',
        'publishedAt' => 'ressource.publishedAt',
        'archiveAt' => 'ressource.archiveAt',
        'archived' => 'ressource.archived',
        'enabled' => 'ressource.enabled',
    ];
}
