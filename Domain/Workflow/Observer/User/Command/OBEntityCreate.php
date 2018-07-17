<?php
namespace Sfynx\AuthBundle\Domain\Workflow\Observer\User\Command;

use Exception;
use Symfony\Component\Form\Extension\Core\DataTransformer\ValueToDuplicatesTransformer;

use Sfynx\ToolBundle\Builder\RouteTranslatorFactoryInterface;
use Sfynx\CoreBundle\Layers\Domain\Service\Manager\Generalisation\Interfaces\ManagerInterface;
use Sfynx\CoreBundle\Layers\Domain\Service\Request\Generalisation\RequestInterface;
use Sfynx\CoreBundle\Layers\Domain\Workflow\Observer\Generalisation\Command\AbstractEntityCreateHandler;
use Sfynx\CoreBundle\Layers\Infrastructure\Exception\EntityException;
use Sfynx\AuthBundle\Domain\Generalisation\Interfaces\UserInterface;

/**
 * Class OBEntityCreate
 *
 * @category Sfynx\AuthBundle
 * @package Domain
 * @subpackage Workflow\Observer\User\Command
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2016 PI-GROUPE
 */
class OBEntityCreate extends AbstractEntityCreateHandler
{
    /** @var RouteTranslatorFactoryInterface */
    protected $router;

    /**
     * AbstractEntityCreateHandler constructor.
     * @param ManagerInterface $manager
     * @param RequestInterface $request
     * @param RouteTranslatorFactoryInterface $router
     */
    public function __construct(ManagerInterface $manager, RequestInterface $request, RouteTranslatorFactoryInterface $router, bool $updateCommand = false)
    {
        parent::__construct($manager, $request, $updateCommand);
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    protected function onSuccess(): void
    {
        $errors = false;
        if(empty($this->wfCommand->getPlainPassword()['first'])
            && empty($this->wfCommand->getPlainPassword()['second'])
        ) {
            $errors = true;
        }
        try {
            (new ValueToDuplicatesTransformer(['first', 'second']))->reverseTransform($this->wfCommand->getPlainPassword());
        } catch (Exception $e) {
            $errors = true;
        }
        if ($errors) {
            $this->wfCommand->errors['plainPassword']['first'] = 'Oops! This is a error message for first field of RepeatedField ';
            $this->wfCommand->errors['plainPassword']['second'] = 'Oops! This is error message for confirm field';
        }

        $entity = $this->wfLastData->entity;
        try {
            if ($entity instanceof UserInterface
                && count($this->wfCommand->errors) == 0
            ) {
                $entity = $this->manager->buildFromCommand($entity, $this->wfCommand);
                $this->manager->getCommandRepository()->save($entity);
            }
        } catch (Exception $e) {
            $this->wfCommand->errors['success'] = 'errors.user.save';
        }
        // we add the last entity version
        $this->wfLastData->entity = $entity;

        $this->wfLastData->url = $this->router->generate('users_edit', [
            'id' => $this->wfLastData->entity->getId(),
        ]);
    }
}
