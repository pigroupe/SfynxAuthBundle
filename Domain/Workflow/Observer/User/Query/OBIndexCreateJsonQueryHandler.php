<?php
namespace Sfynx\AuthBundle\Domain\Workflow\Observer\User\Query;

use stdClass;
use Exception;
use Sfynx\CoreBundle\Layers\Domain\Workflow\Observer\Generalisation\Query\AbstractIndexCreateJsonQueryHandler;
use Sfynx\CoreBundle\Layers\Infrastructure\Exception\EntityException;

/**
 * Abstract Class OBIndexCreateJsonQueryHandler
 *
 * @category Sfynx\CoreBundle\Layers
 * @package Domain
 * @subpackage Workflow\Observer\Query
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2016 PI-GROUPE
 */
class OBIndexCreateJsonQueryHandler extends AbstractIndexCreateJsonQueryHandler
{
    /**
     * This method implements the init process evenif the request and the form state
     * @return void
     * @throws EntityException
     */
    protected function process(): void
    {
        try {
            $aColumns = [
                'a.id',
                'a.username',
                'a.nickname',
                'a.name',
                'a.email',
                "case when a.roles LIKE '%ROLE_SUPER_ADMIN%' OR gps.roles LIKE '%ROLE_SUPER_ADMIN%' then 'ROLE_SUPER_ADMIN' when a.roles LIKE '%ROLE_ADMIN%' OR gps.roles LIKE '%ROLE_ADMIN%' then 'ROLE_ADMIN' when a.roles LIKE '%ROLE_USER%' OR gps.roles LIKE '%ROLE_USER%' then 'ROLE_USER'  else 'OTHER' end",
                'a.created_at',
                'a.updated_at',
                "case when a.enabled = 1 then 'Actif' when a.archive_at IS NOT NULL and a.archived = 1  then 'Supprime' else 'En attente dactivation' end",
                "a.enabled"
            ];

            $q1 = clone $this->wfLastData->query;
            $q2 = clone $this->wfLastData->query;

            $this->wfLastData->result = $this->createAjaxQuery('select', $aColumns, $q1, 'a', [
                    0 => ['column'=>'a.created_at', 'format'=>'Y-m-d', 'idMin'=>'minc', 'idMax'=>'maxc'],
                    1 => ['column'=>'a.updated_at', 'format'=>'Y-m-d', 'idMin'=>'minu', 'idMax'=>'maxu']
                ]
            );
            $this->wfLastData->total  = $this->createAjaxQuery('count', $aColumns, $q2, 'a', [
                    0 => ['column'=>'a.created_at', 'format'=>'Y-m-d', 'idMin'=>'minc', 'idMax'=>'maxc'],
                    1 => ['column'=>'a.updated_at', 'format'=>'Y-m-d', 'idMin'=>'minu', 'idMax'=>'maxu']
                ]
            );
        } catch (Exception $e) {
            throw EntityException::NotFoundEntities($this->entityName);
        }
    }
}
