<?php
namespace Sfynx\AuthBundle\Presentation\Coordination;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormFactoryInterface;
//use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface as LegacyValidatorInterface;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Sfynx\ToolBundle\Twig\Extension\PiFormExtension;
use Sfynx\CoreBundle\Layers\Presentation\Coordination\Generalisation\AbstractQueryController;
use Sfynx\CoreBundle\Layers\Domain\Service\Request\Generalisation\RequestInterface;
use Sfynx\CoreBundle\Layers\Infrastructure\Exception\ControllerException;
use Sfynx\AuthBundle\Domain\Entity\Layout;
use Sfynx\AuthBundle\Application\Validation\Type\LayoutType;

/**
 * Layout controller.
 *
 * @subpackage Auth
 * @package    Controller
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @since 2012-01-03
 */
class LayoutController extends AbstractQueryController
{
    protected $_entityName = "SfynxAuthBundle:Layout";

    /** @var LegacyValidatorInterface */
    protected $validator;
    /** @var RequestInterface */
    protected $request;
    /** @var RegistryInterface $registry */
    protected $registry;
    /** @var EngineInterface $templating */
    protected $templating;
    /** @var FormFactoryInterface $formFactory */
    protected $formFactory;

    /**
     * FrontendController constructor.
     *
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param LegacyValidatorInterface $validator
     * @param CsrfTokenManagerInterface $securityManager
     * @param RequestInterface $request
     * @param RequestInterface $registry
     * @param RequestInterface $templating
     * @param RequestInterface $formFactory
     * @param PiFormExtension $form
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        LegacyValidatorInterface $validator,
        RequestInterface $request,
        RegistryInterface $registry,
        EngineInterface $templating,
        FormFactoryInterface $formFactory,
        PiFormExtension $formExtension
    ) {
        parent::__construct($authorizationChecker, $manager, $request, $templating, $formExtension);

        $this->registry = $registry;
        $this->formFactory = $formFactory;
        $this->validator = $validator;
    }

    /**
     * Lists all Layout entities.
     *
     * @Secure(roles="ROLE_EDITOR")
     * @return Response
     * @access public
     */
    public function indexAction()
    {
        $em = $this->registry->getManager();
        $entities = $em->getRepository($this->_entityName)->findAll();

        return $this->templating->renderResponse('SfynxAuthBundle:Layout:index.html.twig', [
            'entities' => $entities
        ]);
    }

    /**
     * Enabled Layout entities.
     *
     * @Secure(roles="ROLE_EDITOR")
     * @return Response
     * @access public
     */
    public function enabledajaxAction()
    {
        return parent::enabledajaxAction();
    }

    /**
     * Disable Layout entities.
     *
     * @Secure(roles="ROLE_EDITOR")
     * @return Response
     * @access public
     */
    public function disableajaxAction()
    {
        return parent::disableajaxAction();
    }

    /**
     * Delete Layout entities.
     *
     * @Secure(roles="ROLE_EDITOR")
     * @return Response
     * @access public
     */
    public function deleteajaxAction()
    {
        return parent::deletajaxAction();
    }

    /**
     * Finds and displays a Layout entity.
     *
     * @Secure(roles="ROLE_EDITOR")
     * @return Response
     * @access public
     */
    public function showAction($id)
    {
        $em = $this->registry->getManager();
        $entity = $em->getRepository($this->_entityName)->find($id);
        if (!$entity) {
            throw ControllerException::NotFoundEntity('Layout');
        }
        $deleteForm = $this->createDeleteForm($id);

        return $this->templating->renderResponse('SfynxAuthBundle:Layout:show.html.twig', [
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to create a new Layout entity.
     *
     * @Secure(roles="ROLE_EDITOR")
     * @return Response
     * @access public
     */
    public function newAction()
    {
        $entity = new Layout();
        $form   = $this->formFactory->create(new LayoutType(), $entity, ['show_legend' => false]);

        return $this->templating->renderResponse('SfynxAuthBundle:Layout:new.html.twig', [
            'entity' => $entity,
            'form'   => $form->createView()
        ]);
    }

    /**
     * Creates a new Layout entity.
     *
     * @Secure(roles="ROLE_EDITOR")
     * @return Response
     * @access public
     */
    public function createAction()
    {
        $entity  = new Layout();
        $form    = $this->formFactory->create(new LayoutType(), $entity, ['show_legend' => false]);
        $form->bind($this->request);
        if ($form->isValid()) {
            $em = $this->registry->getManager();
            $em->persist($entity);
            $em->flush();

            return new RedirectResponse(
                $this->generateUrl('sfynx_layout_show', ['id' => $entity->getId()])
            );
        }

        return $this->templating->renderResponse('SfynxAuthBundle:Layout:new.html.twig', [
            'entity' => $entity,
            'form'   => $form->createView()
        ]);
    }

    /**
     * Displays a form to edit an existing Layout entity.
     *
     * @Secure(roles="ROLE_EDITOR")
     * @return Response
     * @access public
     */
    public function editAction($id)
    {
        $em = $this->registry->getManager();
        $entity = $em->getRepository($this->_entityName)->find($id);
        if (!$entity) {
            throw ControllerException::NotFoundEntity('Layout');
        }
        $editForm = $this->formFactory->create(new LayoutType(), $entity, ['show_legend' => false]);
        $deleteForm = $this->createDeleteForm($id);

        return $this->templating->renderResponse('SfynxAuthBundle:Layout:edit.html.twig', [
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Edits an existing Layout entity.
     *
     * @Secure(roles="ROLE_EDITOR")
     * @return Response
     * @access public
     */
    public function updateAction($id)
    {
        $em = $this->registry->getManager();
        $entity = $em->getRepository($this->_entityName)->find($id);
        if (!$entity) {
            throw ControllerException::NotFoundEntity('Layout');
        }
        $editForm   = $this->formFactory->create(new LayoutType(), $entity);
        $deleteForm = $this->createDeleteForm($id);
        $editForm->bind($this->request);
        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return new RedirectResponse(
                $this->generateUrl('sfynx_layout_edit', ['id' => $id])
            );
        }

        return $this->templating->renderResponse('SfynxAuthBundle:Layout:edit.html.twig', [
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a Layout entity.
     *
     * @Secure(roles="ROLE_SUPER_ADMIN")
     * @return RedirectResponse
     * @access public
     * @throws \Exception, \LogicException
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($this->request);
        if ($form->isValid()) {
            $em = $this->registry->getManager();
            $entity = $em->getRepository($this->_entityName)->find($id);
            if (!$entity) {
                throw ControllerException::NotFoundEntity('Layout');
            }
            $em->remove($entity);
            $em->flush();
        }

        return new RedirectResponse($this->generateUrl('sfynx_layout'));
    }

    /**
     * Create a Deletes form builder.
     *
     * @param integer $id
     *
     * @return
     * @access protected
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    protected function createDeleteForm($id)
    {
        return $this->formFactory->createBuilder('form', ['id' => $id])
            ->add('id', 'hidden')
            ->getForm()
            ;
    }
}
