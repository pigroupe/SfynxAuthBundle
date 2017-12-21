<?php
namespace Sfynx\AuthBundle\Domain\Workflow\Observer\User\Query;

use stdClass;
use Exception;
use Sfynx\CoreBundle\Layers\Domain\Workflow\Observer\Generalisation\Query\AbstractIndexCreateQueryHandler;
use Sfynx\CoreBundle\Layers\Infrastructure\Exception\EntityException;

/**
 * Abstract Class OBIndexCreateQueryHandler
 *
 * @category Sfynx\CoreBundle\Layers
 * @package Domain
 * @subpackage Workflow\Observer\Query
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2016 PI-GROUPE
 */
class OBIndexCreateQueryHandler extends AbstractIndexCreateQueryHandler
{
    /**
     * This method implements the init process evenif the request and the form state
     * @return void
     * @throws EntityException
     */
    protected function process(): void
    {
        try {
            $this->wfLastData->query = $this->manager->getQueryRepository()->getAllEditorUsersWithLazy();
        } catch (Exception $e) {
            throw EntityException::NotFoundEntity($this->entityName);
        }
    }
}
