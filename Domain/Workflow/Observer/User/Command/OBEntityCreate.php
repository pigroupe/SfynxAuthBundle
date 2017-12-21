<?php
namespace Sfynx\AuthBundle\Domain\Workflow\Observer\User\Command;

use Exception;
use Symfony\Component\Form\Extension\Core\DataTransformer\ValueToDuplicatesTransformer;

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
    /**
     * {@inheritdoc}
     */
    protected function onSuccess(): void
    {
        $errors = false;
        if(empty($this->wfCommand->plainPassword['first'])
            && empty($this->wfCommand->plainPassword['second']))
        {
            $errors = true;
        }
        try {
            (new ValueToDuplicatesTransformer(['first', 'second']))->reverseTransform($this->wfCommand->plainPassword);
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
    }
}
