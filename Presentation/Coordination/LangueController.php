<?php
/**
 * This file is part of the <Auth> project.
 *
 * @subpackage   Auth
 * @package    Controller
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @since 2012-01-03
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\AuthBundle\Presentation\Coordination;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Form\FormFactoryInterface;

use Sfynx\CoreBundle\Layers\Domain\Service\Request\Generalisation\RequestInterface;
use Sfynx\AuthBundle\Domain\Entity\Translation\LangueTranslation;
use Sfynx\CoreBundle\Controller\abstractController;
use Sfynx\CoreBundle\Layers\Infrastructure\Exception\ControllerException;
use Sfynx\AuthBundle\Domain\Entity\Langue;
use Sfynx\AuthBundle\Application\Validation\Type\LangueType;

/**
 * Langue controller.
 *
 * @subpackage Auth
 * @package    Controller
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
class LangueController extends abstractController
{
    protected $_entityName = "SfynxAuthBundle:Langue";

    /** @var RequestInterface */
    protected $request;
    /** @var RegistryInterface $registry */
    protected $registry;
    /** @var EngineInterface $templating */
    protected $templating;
    /** @var SessionInterface $session */
    protected $session;
    /** @var FormFactoryInterface $formFactory */
    protected $formFactory;

    /**
     * FrontendController constructor.
     *
     * @param RequestInterface     $request
     * @param RegistryInterface    $registry
     * @param EngineInterface      $templating
     * @param SessionInterface     $session
     * @param FormFactoryInterface $session
     */
    public function __construct(
        RequestInterface $request,
        RegistryInterface $registry,
        EngineInterface $templating,
        SessionInterface $session,
        FormFactoryInterface $formFactory
    ) {
        $this->request = $request;
        $this->registry = $registry;
        $this->templating = $templating;
        $this->session = $session;
        $this->formFactory = $formFactory;
    }

    /**
     * Lists all Langue entities.
     *
     * @Secure(roles="ROLE_EDITOR")
     * @return Response
     * @access public
     */
    public function indexAction()
    {
        $em         = $this->registry->getManager();
        $locale     = $this->request->getLocale();
        $entities   = $em->getRepository("SfynxAuthBundle:Langue")->findAllByEntity($locale, 'object', false);

        return $this->templating->renderResponse('SfynxAuthBundle:Langue:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Enabled Langue entities.
     *
     * @Secure(roles="ROLE_EDITOR")
     * @return Response
     * @access  public
     *
     * @throws \LogicException
     * @throws ControllerException
     */
    public function enabledajaxAction()
    {
        $em = $this->registry->getManager();
        if ($this->request->isXmlHttpRequest()){
            $data        = $this->request->get('data', null);
            foreach ($data as $key => $id) {
                $entity = $em->getRepository($this->_entityName)->find($id);
                $entity->setEnabled(true);
                $em->persist($entity);
                $em->flush();
            }
            $em->clear();

            // we disable all flash message
            $this->session->getFlashBag()->clear();

            $tab= [];
            $tab['id'] = '-1';
            $tab['error'] = '';
            $tab['fieldErrors'] = '';
            $tab['data'] = '';

            $response = new Response(json_encode($tab));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        throw ControllerException::callAjaxOnlySupported('enabledajax');
    }

    /**
     * Disable Langue  entities.
     *
     * @Secure(roles="ROLE_EDITOR")
     * @return Response
     * @access  public
     *
     * @throws \LogicException
     * @throws ControllerException
     */
    public function disableajaxAction()
    {
        $em = $this->registry->getManager();
        if ($this->request->isXmlHttpRequest()){
            $data  = $this->request->get('data', null);
            foreach ($data as $key => $id) {
                $entity = $em->getRepository($this->_entityName)->find($id);
                $entity->setEnabled(false);
                $em->persist($entity);
                $em->flush();
            }
            $em->clear();
            // we disable all flash message
            $this->session->getFlashBag()->clear();

            $tab= [];
            $tab['id'] = '-1';
            $tab['error'] = '';
            $tab['fieldErrors'] = '';
            $tab['data'] = '';

            $response = new Response(json_encode($tab));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        throw ControllerException::callAjaxOnlySupported('disableajax');
    }

    /**
     * Finds and displays a Langue entity.
     *
     * @param integer $id
     *
     * @Secure(roles="ROLE_EDITOR")
     * @return Response
     * @access public
     *
     * @throws ControllerException
     */
    public function showAction($id)
    {
        $em     = $this->registry->getManager();
        $locale = $this->request->getLocale();
        $entity = $em->getRepository($this->_entityName)->findOneByEntity($locale, $id, 'object');
        if (!$entity) {
            throw ControllerException::NotFoundEntity('Langue');
        }
        $deleteForm = $this->createDeleteForm($id);
        $locale_id = explode('_', strtolower($entity->getId()));
        if (count($locale_id) == 2) {
            $locale_id = $locale_id[1];
        }

        return $this->templating->renderResponse('SfynxAuthBundle:Langue:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'locale_id'   => $locale_id,
        ));
    }

    /**
     * Displays a form to create a new Langue entity.
     *
     * @Secure(roles="ROLE_EDITOR")
     * @return Response
     * @access public
     */
    public function newAction()
    {
        $entity = new Langue();
        $locale = $this->request->getLocale();
        $form   = $this->formFactory->create(new LangueType($locale), $entity, ['show_legend' => false]);

        return $this->templating->renderResponse('SfynxAuthBundle:Langue:new.html.twig', [
            'entity' => $entity,
            'form'   => $form->createView()
        ]);
    }

    /**
     * Creates a new Langue entity.
     *
     * @Secure(roles="ROLE_EDITOR")
     * @return Response
     * @access public
     */
    public function createAction()
    {
        $em     = $this->registry->getManager();
        $locale = $this->request->getLocale();
        $entity = new Langue();

        $form    = $this->formFactory->create(new LangueType($locale), $entity, array('show_legend' => false));
        $form->bind($this->request->getInstance());

        if ($form->isValid()) {
            $entity->setTranslatableLocale($locale);
            $em->persist($entity);
            $em->flush();

            return new RedirectResponse($this->generateUrl('sfynx_langue_show', array('id' => $entity->getId())));
        }

        return $this->templating->renderResponse('SfynxAuthBundle:Langue:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing Langue entity.
     *
     * @param integer $id
     *
     * @Secure(roles="ROLE_EDITOR")
     * @return Response
     * @access public
     */
    public function editAction($id)
    {
        $em     = $this->registry->getManager();
        $locale = $this->request->getLocale();
        $entity = $em->getRepository($this->_entityName)->findOneByEntity($locale, $id, 'object');

        if (!$entity) {
            $entity = $em->getRepository("SfynxAuthBundle:Langue")->find($id);
            $entity->addTranslation(new LangueTranslation($locale));
        }

        $editForm = $this->formFactory->create(new LangueType($locale, true), $entity, array('show_legend' => false));
        $deleteForm = $this->createDeleteForm($id);

        return $this->templating->renderResponse('SfynxAuthBundle:Langue:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Langue entity.
     *
     * @param integer $id
     *
     * @Secure(roles="ROLE_EDITOR")
     * @return Response
     * @access public
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function updateAction($id)
    {
        $em     = $this->registry->getManager();
        $locale = $this->request->getLocale();
        $entity = $em->getRepository($this->_entityName)->findOneByEntity($locale, $id, 'object');

        if (!$entity) {
            $entity = $em->getRepository($this->_entityName)->find($id);
        }

        $editForm   = $this->formFactory->create(new LangueType($locale, true), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $editForm->bind($this->request->getInstance(), $entity);
        if ($editForm->isValid()) {
            $entity->setTranslatableLocale($locale);
            $em->persist($entity);
            $em->flush();

            return new RedirectResponse($this->generateUrl('sfynx_langue_edit', array('id' => $id)));
        }

        return $this->templating->renderResponse('SfynxAuthBundle:Langue:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Langue entity.
     *
     * @param integer $id
     *
     * @Secure(roles="ROLE_ADMIN")
     * @return RedirectResponse
     * @access public
     *
     * @throws \LogicException
     * @throws \Exception
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($this->request->getInstance());
        if ($form->isValid()) {
            $em = $this->registry->getManager();
            $entity = $em->getRepository($this->_entityName)->find($id);
            if (!$entity) {
                throw ControllerException::NotFoundEntity('Langue');
            }
            try {
                $em->remove($entity);
                $em->flush();
            } catch (\Exception $e) {
                $this->session->getFlashBag()->clear();
                $this->session->getFlashBag()->add('notice', 'pi.session.flash.wrong.undelete');
            }
        }

        return new RedirectResponse($this->generateUrl('admin_langue'));
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
