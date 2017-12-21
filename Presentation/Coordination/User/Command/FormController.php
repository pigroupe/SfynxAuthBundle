<?php
namespace Sfynx\AuthBundle\Presentation\Coordination\User\Command;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface as LegacyValidatorInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\HttpFoundation\Response;

/// TEST DDD
use Sfynx\CoreBundle\Layers\Presentation\Coordination\Generalisation\AbstractFormController;
use Sfynx\CoreBundle\Layers\Presentation\Adapter\Command\CommandAdapter;
use Sfynx\CoreBundle\Layers\Application\Response\Generalisation\Interfaces\ResponseHandlerInterface;
use Sfynx\CoreBundle\Layers\Application\Response\Handler\ResponseHandler;
use Sfynx\CoreBundle\Layers\Application\Common\Generalisation\Interfaces\HandlerInterface;
use Sfynx\CoreBundle\Layers\Application\Command\Handler\FormCommandHandler;
use Sfynx\CoreBundle\Layers\Application\Command\Handler\Decorator\CommandHandlerDecorator;
use Sfynx\CoreBundle\Layers\Application\Validation\Generalisation\ValidationHandler\SymfonyValidatorStrategy;
use Sfynx\CoreBundle\Layers\Application\Common\Handler\WorkflowHandler;
use Sfynx\CoreBundle\Layers\Application\Command\WorkflowCommand;
use Sfynx\CoreBundle\Layers\Domain\Service\Manager\Generalisation\Interfaces\ManagerInterface;
use Sfynx\CoreBundle\Layers\Domain\Service\Request\Generalisation\RequestInterface;
use Sfynx\CoreBundle\Layers\Domain\Workflow\Observer\Response\OBCreateFormView;
use Sfynx\CoreBundle\Layers\Domain\Workflow\Observer\Response\OBInjectFormErrors;
use Sfynx\CoreBundle\Layers\Domain\Workflow\Observer\Response\OBCreateFormBody;
use Sfynx\CoreBundle\Layers\Domain\Workflow\Observer\Response\OBCreateResponseHtml;
use Sfynx\CoreBundle\Layers\Infrastructure\Exception\WorkflowException;
use Sfynx\CoreBundle\Layers\Infrastructure\Exception\PresentationException;

use Sfynx\AuthBundle\Presentation\Request\User\Command\FormRequest as UserFormRequest;
use Sfynx\AuthBundle\Application\Cqrs\User\Command\FormCommand as UserFormCommand;
use Sfynx\AuthBundle\Application\Cqrs\User\Command\Validation\SpecHandler\FormCommandSpecHandler as UserFormCommandSpecHandler;
use Sfynx\AuthBundle\Application\Cqrs\User\Command\Validation\ValidationHandler\FormCommandValidationHandler as UserFormCommandValidationHandler;
use Sfynx\AuthBundle\Application\Validation\Type\UsersFormType;
use Sfynx\AuthBundle\Domain\Workflow\Observer\User\Command\OBEntityEdit as OBUserEntityEdit;
use Sfynx\AuthBundle\Domain\Workflow\Observer\User\Command\OBEntityCreate as OBUserEntityCreate;
use Sfynx\AuthBundle\Domain\Workflow\Observer\User\Response\OBCreateFormData as OBUserCreateFormData;

/**
 * class  FormController.
 *
 * @category   Sfynx\AuthBundle
 * @package    Presentation
 * @subpackage Coordination\User\Command
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
class FormController extends AbstractFormController
{
    /** @var ManagerInterface */
    protected $managerGroup;
    /** @var ManagerInterface */
    protected $managerLangue;

    /**
     * UsersController constructor.
     *
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param ManagerInterface $manager
     * @param ManagerInterface $managerGroup
     * @param ManagerInterface $managerLangue
     * @param RequestInterface $request
     * @param FormFactoryInterface $formFactory
     * @param EngineInterface $templating
     * @param LegacyValidatorInterface $validator
     * @param TranslatorInterface $translator
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        ManagerInterface $manager,
        ManagerInterface $managerGroup,
        ManagerInterface $managerLangue,
        RequestInterface $request,
        EngineInterface $templating,
        FormFactoryInterface $formFactory,
        LegacyValidatorInterface $validator,
        TranslatorInterface $translator
    ) {
        parent::__construct($authorizationChecker, $manager, $request, $templating, $formFactory, $validator, $translator);
        $this->managerGroup = $managerGroup;
        $this->managerLangue = $managerLangue;
    }

    /**
     * Displays a form to edit an existing user entity.
     *
     * @param integer $id
     * @return Response
     * @access public
     */
    public function coordinate()
    {
        if (false === $this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        // 1. Transform Request to Command.
        $adapter = new CommandAdapter(new UserFormCommand());
        $command = $adapter->createCommandFromRequest(
            new UserFormRequest($this->request)
        );

        // 2. Implement the command workflow
        $Observer1 = new OBUserEntityEdit($this->manager, $this->request);
        $Observer2 = new OBUserEntityCreate($this->manager, $this->request);
        $workflowCommand = (new WorkflowCommand())
            ->attach($Observer1)
            ->attach($Observer2);

        // 3. Implement decorator to apply the command workflow from the command
        $this->commandHandler = new FormCommandHandler($workflowCommand);
        $this->commandHandler = new UserFormCommandValidationHandler(
            $this->commandHandler,
            new SymfonyValidatorStrategy($this->validator),
            false
        );
        $this->commandHandler = (new UserFormCommandSpecHandler($this->commandHandler))->setObject(null);
        $commandHandlerResult = $this->commandHandler->process($command);
        if (!($commandHandlerResult instanceof HandlerInterface)) {
            throw PresentationException::invalidCommandHandlerResponse();
        }

        // 4. Implement the Response workflow
        $this->param->templating = str_replace('::', ':', $this->getParamOrThrow('sfynx_auth_theme_login')) . 'Users:edit.html.twig';
        $Observer1 = new OBUserCreateFormData($this->request, $this->managerGroup, $this->managerLangue);
        $Observer2 = new OBCreateFormView($this->request, $this->formFactory, new UsersFormType($this->manager));
        $Observer3 = new OBInjectFormErrors($this->request, $this->translator);
        $Observer4 = new OBCreateFormBody($this->request, $this->templating, $this->param);
        $Observer5 = new OBCreateResponseHtml($this->request);
        $workflowHandler = (new WorkflowHandler())
            ->attach($Observer1)
            ->attach($Observer2)
            ->attach($Observer3)
            ->attach($Observer4)
            ->attach($Observer5);

        // 5. Implement the responseHandler from the workflow
        $this->responseHandler = new ResponseHandler($workflowHandler);
        $responseHandlerResult = $this->responseHandler->process($commandHandlerResult);

        return $responseHandlerResult->response;
    }
}