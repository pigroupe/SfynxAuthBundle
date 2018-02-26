<?php
namespace Sfynx\AuthBundle\Domain\Workflow\Observer\Response;

use Exception;
use stdClass;
use Sfynx\CoreBundle\Layers\Domain\Workflow\Observer\Generalisation\Response\AbstractCreateIndexBodyJson;
use Sfynx\CoreBundle\Layers\Infrastructure\Exception\WorkflowException;

/**
 * Class OBCreateIndexBodyJson
 *
 * @category Sfynx\CoreBundle\Layers
 * @package Domain
 * @subpackage Workflow\Observer\Response
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2016 PI-GROUPE
 */
class OBCreateIndexBodyJson extends AbstractCreateIndexBodyJson
{
    const roles =  [
        'ROLE_SUPER_ADMIN',
        'ROLE_ADMIN',
        'ROLE_USER',
        'ROLE_SUBSCRIBER',
        'ROLE_MEMBER',
        'ROLE_CUSTOMER',
        'ROLE_PROVIDER'
    ];

    /**
     * {@inheritdoc}
     */
    protected function process(): bool
    {
        $this->wfLastData->rows = [];
        try {
            foreach ($this->wfHandler->result as $entity) {
                $row = [];
                $row[] = $entity->getId() . '_row_' . $entity->getId();
                $row[] = $entity->getId();
                $row[] = (string) $entity->getUsername();
                $row[] = (string) $entity->getNickname();
                $row[] = (string) $entity->getName();
                $row[] = (string) $entity->getEmail();

                if (is_array($entity->getRoles())) {
                    $best_roles = $this->roleFactory->getBestRoles($entity->getRoles());
                    if (is_string($best_roles) && !in_array($best_roles, self::roles)) {
                        $best_roles = 'Autres';
                    }
                    $row[] = (string) implode(",", $best_roles);
                } else {
                    $row[] = "";
                }

                if (is_object($entity->getCreatedAt())) {
                    $row[] = (string) $entity->getCreatedAt()->format('Y-m-d');
                } else {
                    $row[] = "";
                }

                if (is_object($entity->getUpdatedAt())) {
                    $row[] = (string) $entity->getUpdatedAt()->format('Y-m-d');
                } else {
                    $row[] = "";
                }

                $row[] = (string) $this->toolExtension->statusFilter($entity);

                // create action links
                $route_path_edit = $this->generateUrl('users_edit', [
                    'id' => $entity->getId(),
                    'NoLayout' => $this->wfHandler->query->getNoLayout(),
                    'category' => $this->wfHandler->query->getCategory()
                ]);
                $actions  = '<a href="'.$route_path_edit.'" title="'.$this->translator->trans('pi.grid.action.edit').'" data-ui-icon="ui-icon-edit-user" class="button-ui-icon-edit-user info-tooltip" >'.$this->translator->trans('pi.grid.action.edit').'</a>'; //actions
//                $actions .= '<a href="'.$route_path_edit.'" title="'.$this->translator->trans('pi.grid.action.delete').'" data-ui-icon="ui-icon-delete-user" class="button-ui-icon-delete-user info-tooltip" >'.$this->translator->trans('pi.grid.action.delete').'</a>'; //actions
                $row[]    = (string) $actions;

                $this->wfLastData->rows[] = $row ;
            }
        } catch (Exception $e) {
            throw WorkflowException::noCreatedViewForm();
        }
        return true;
    }
}
