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

use Sfynx\ToolBundle\Builder\RouteTranslatorFactoryInterface;
use Sfynx\CoreBundle\Layers\Domain\Service\Request\Generalisation\RequestInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 *
 * @subpackage Auth
 * @package    Controller
 */
class SecurityController
{
    /** @var RouteTranslatorFactoryInterface  */
    protected $router;
    /** @var RequestInterface */
    protected $request;
    /** @var ContainerInterface */
    protected $container;
    /** @var \Symfony\Component\HttpFoundation\Session\Session */
    protected $session;

    /**
     * FrontendController constructor.
     *
     * @param RouteTranslatorFactoryInterface $router
     * @param RequestInterface $request
     * @param ContainerInterface $container
     */
    public function __construct(
        RouteTranslatorFactoryInterface $router,
        RequestInterface $request,
        ContainerInterface $container
    ) {
        $this->router = $router;
        $this->request = $request;
        $this->session = $request->getSession();
        $this->container = $container;
    }

    public function loginAction(Request $request)
    {
        /** @var $this->session \Symfony\Component\HttpFoundation\Session\Session */
        $this->session = $request->getSession();
        $error = '';
        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } elseif (null !== $this->session && $this->session->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $this->session->get(SecurityContext::AUTHENTICATION_ERROR);
            $this->session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }
        if ($error) {
            // TODO: this is a potential security risk (see http://trac.symfony-project.org/ticket/9523)
            $error = $error->getMessage();
        }
        // last username entered by the user
        $lastUsername = (null === $this->session) ? '' : $this->session->get(SecurityContext::LAST_USERNAME);
        // we register the username in session used in dispatcherLoginFailureResponse
        $this->session->set('login-username', $lastUsername);
        // we test if the number of attempts allowed connections number with the username have been exceeded.
        if (!empty($lastUsername)) {
            $key = $this->container->get('sfynx.auth.dispatcher.login_failure.change_response')->getKeyValue();
            if ($key == "stop-client") {
                $this->session->set('login-username', '');
                $this->session->remove(SecurityContext::LAST_USERNAME);
                if ($request->isXmlHttpRequest()) {
                    $response = new Response(json_encode('error'));
                    $response->headers->set('Content-Type', 'application/json');

                    return $response;
                }
                $new_url = $this->router->generate('sfynx_auth_security_login');
                $this->session->getFlashBag()->add('errorform', "you exceeded the number of attempts allowed connections!");

                return new RedirectResponse($new_url);
            }
        }

        $csrfToken = $this->container->has('security.csrf.token_manager')
        ? $this->container->get('security.csrf.token_manager')->getToken('authenticate')
        : null;

        if ($request->isXmlHttpRequest()) {
            $statut = "ok";
        	if ($error) {
        		$statut = "error";
        	}
        	$response = new Response(json_encode($statut));
        	$response->headers->set('Content-Type', 'application/json');

        	return $response;
        }

        return $this->renderLogin([
                'last_username' => $lastUsername,
                'error'         => $error,
                'csrf_token' => $csrfToken,
                'NoLayout'    => $request->query->get('NoLayout')
        ]);
    }

    /**
     * Renders the login template with the given parameters. Overwrite this function in
     * an extended controller to provide additional data for the login template.
     *
     * @param array $data
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderLogin(array $data)
    {
        $template = str_replace('::', ':', $this->container->getParameter('sfynx.template.theme.login'))."Security:login.html.twig";

        return $this->container->get('templating')->renderResponse($template, $data);
    }

    public function checkAction()
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.');
    }

    public function logoutAction()
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }
}
