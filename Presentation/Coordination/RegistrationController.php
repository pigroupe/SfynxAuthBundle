<?php
/**
 * This file is part of the <Auth> project.
 *
 * @subpackage   Auth
 * @package    Controller
 * @since 2012-01-03
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\AuthBundle\Presentation\Coordination;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\RegistrationController as BaseController;

/**
 * Controller managing the registration
 *
 * @subpackage Auth
 * @package    Controller
 */
class RegistrationController extends BaseController
{
    /** @var RequestInterface */
    protected $request;

    /**
     * FrontendController constructor.
     *
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;
    }

//    public function registerAction(Request $request)
//    {
//        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
//        $formFactory = $this->container->get('fos_user.registration.form.factory');
//        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
//        $userManager = $this->container->get('fos_user.user_manager');
//        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
//        $dispatcher = $this->container->get('event_dispatcher');
//
//        $user = $userManager->create();
//        $user->setEnabled(true);
//
//        $form = $formFactory->createForm();
//        //$form->setData($user);
//
//        if ('POST' === $request->getMethod()) {
//            $form->bind($request);
//            if ($form->isValid()) {
//                $userManager->update($user);
//                if (null === $response = $event->getResponse()) {
//                    $url = $this->container->get('router')->generate('fos_user_registration_confirmed');
//                    $response = new RedirectResponse($url);
//                }
//                return $response;
//            }
//        }
//
//        return $this->container->get('templating')->renderResponse(str_replace('::', ':', $this->container->getParameter('sfynx.template.theme.login')).'Registration:register.html.twig', array(
//            'form' => $form->createView(),
//        ));
//    }
}
