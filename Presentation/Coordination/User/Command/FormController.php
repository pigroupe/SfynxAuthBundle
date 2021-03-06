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
use Sfynx\ToolBundle\Builder\RouteTranslatorFactoryInterface;
use Sfynx\ToolBundle\Twig\Extension\PiToolExtension;

use Sfynx\CoreBundle\Layers\Presentation\Coordination\Generalisation\AbstractFormController;
use Sfynx\CoreBundle\Layers\Presentation\Adapter\Command\CommandAdapter;
use Sfynx\CoreBundle\Layers\Application\Response\Handler\Generalisation\Interfaces\ResponseHandlerInterface;
use Sfynx\CoreBundle\Layers\Application\Response\Handler\ResponseHandler;
use Sfynx\CoreBundle\Layers\Application\Common\Generalisation\Interfaces\HandlerInterface;
use Sfynx\CoreBundle\Layers\Application\Command\Handler\CommandHandler;
use Sfynx\CoreBundle\Layers\Application\Command\Handler\Decorator\CommandHandlerDecorator;
use Sfynx\CoreBundle\Layers\Application\Validation\Validator\SymfonyValidatorStrategy;
use Sfynx\CoreBundle\Layers\Application\Common\Handler\WorkflowHandler;
use Sfynx\CoreBundle\Layers\Application\Command\Workflow\CommandWorkflow;
use Sfynx\CoreBundle\Layers\Domain\Service\Manager\Generalisation\Interfaces\ManagerInterface;
use Sfynx\CoreBundle\Layers\Domain\Service\Request\Generalisation\RequestInterface;
use Sfynx\CoreBundle\Layers\Domain\Workflow\Observer\Response\OBCreateEntityFormView;
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
    /** @var RouteTranslatorFactoryInterface */
    protected $routeFactory;
    /** @var ManagerInterface */
    protected $managerGroup;
    /** @var ManagerInterface */
    protected $managerLangue;
    /** @var PiToolExtension */
    protected $tool;

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
     * @param RouteTranslatorFactoryInterface $routeFactory
     * @param TranslatorInterface $translator
     * @param PiToolExtension $tool
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
        RouteTranslatorFactoryInterface $routeFactory,
        TranslatorInterface $translator,
        PiToolExtension $tool
    ) {
        parent::__construct($authorizationChecker, $manager, $request, $templating, $formFactory, $validator, $translator);
        $this->managerGroup = $managerGroup;
        $this->managerLangue = $managerLangue;
        $this->routeFactory = $routeFactory;
        $this->tool = $tool;
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
        $locale = $this->request->getLocale();

        try {
            // 1. Transform Request to Command.
            $adapter = new CommandAdapter(new UserFormCommand());
            $command = $adapter->createCommandFromRequest(new UserFormRequest($this->request), false);
    
            // 2. Implement the command workflow
            $workflowCommand = (new CommandWorkflow())
                ->attach(new OBUserEntityEdit($this->manager, $this->request))
                ->attach(new OBUserEntityCreate($this->manager, $this->request, $this->routeFactory));
    
            // 3. Implement decorator to apply the command workflow from the command
            $this->commandHandler = new CommandHandler($workflowCommand, $this->manager);
            $this->commandHandler = new UserFormCommandValidationHandler($this->commandHandler,
                new SymfonyValidatorStrategy($this->validator),
                false
            );
            $this->commandHandler = (new UserFormCommandSpecHandler($this->commandHandler))->setObject(null);
            $commandHandlerResult = $this->commandHandler->process($command);

            // 4. Implement the Response workflow
            $this->setParam('templating', 'SfynxAuthBundle:Users:edit.html.twig');
            $workflowHandler = (new WorkflowHandler())
                ->attach(new OBUserCreateFormData($this->request, $this->managerGroup, $this->managerLangue))
                ->attach(new OBCreateEntityFormView($this->request, $this->formFactory, new UsersFormType($this->manager, $this->tool, $locale)))
                ->attach(new OBInjectFormErrors($this->request, $this->translator))
                ->attach(new OBCreateFormBody($this->request, $this->templating, $this->param))
                ->attach(new OBCreateResponseHtml($this->request));
    
            // 5. Implement the responseHandler from the workflow
            $this->responseHandler = new ResponseHandler($workflowHandler);
            $responseHandlerResult = $this->responseHandler->process($commandHandlerResult);

            $response = $responseHandlerResult->getResponse();
        }  catch (NotFoundEntityException $e) {
            $response = new Response();
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            $response->setContent($e->getMessage());
        }  catch (ViolationEntityException $e) {
            $response = new Response();
            $response->setStatusCode(Response::HTTP_CONFLICT);
            $response->setContent($e->getMessage());
        }  catch (Exception $e) {
            $response = new Response();
            $response->setStatusCode(Response::HTTP_SERVICE_UNAVAILABLE);
            $response->setContent($e->getMessage());
        }

        return $response;
    }
}