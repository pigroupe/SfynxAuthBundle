<?php
namespace Sfynx\AuthBundle\Domain\Workflow\Observer\User\Response;

use stdClass;
use Sfynx\CoreBundle\Layers\Domain\Service\Manager\Generalisation\Interfaces\ManagerInterface;
use Sfynx\CoreBundle\Layers\Domain\Service\Request\Generalisation\RequestInterface;
use Sfynx\CoreBundle\Layers\Domain\Workflow\Observer\Generalisation\Response\AbstractCreateFormData;
use Sfynx\CoreBundle\Layers\Infrastructure\Exception\WorkflowException;

/**
 * Class OBCreateFormData
 *
 * @category Sfynx\AuthBundle
 * @package Domain
 * @subpackage Workflow\Observer\User\Response
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2016 PI-GROUPE
 */
class OBCreateFormData extends AbstractCreateFormData
{
    /** @var ManagerInterface */
    protected $managerGroup;
    /** @var ManagerInterface */
    protected $managerLangue;

    /**
     * OBCreateFormData constructor.
     *
     * @param RequestInterface $request
     * @param ManagerInterface $managerGroup
     * @param ManagerInterface $managerLangue
     */
    public function __construct(RequestInterface $request, ManagerInterface $managerGroup, ManagerInterface $managerLangue)
    {
        parent::__construct($request);
        $this->managerGroup = $managerGroup;
        $this->managerLangue = $managerLangue;
    }

    /**
     * {@inheritdoc}
     */
    protected function process(): bool
    {
        try {
            $this->wfLastData->formViewData['langCode'] = $this->managerLangue->getQueryRepository()->formAllOrderByField('label', 'ASC', 1, 0);
            $this->wfLastData->formViewData['groups'] = $this->managerGroup->getQueryRepository()->formAllOrderByField('name', 'ASC', 1, 0);
        } catch (Exception $e) {
            throw WorkflowException::noCreatedFormData();
        }
        return true;
    }
}
