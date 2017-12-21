<?php
namespace Sfynx\AuthBundle\Infrastructure\Security;

use Sfynx\AuthBundle\Infrastructure\Role\Generalisation\RoleFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;

class AuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler
{
    private $roleFactory;
    private $dispatcher;

    /**
     * Constructs a new instance of SecurityListener.
     *
     * @param RoleFactoryInterface     $roleFactory
     * @param LoggerInterface          $logger
     * @param EventDispatcherInterface $dispatcher
     * @param AuthorizationChecker     $AuthorizationChecker
     * @param HttpUtils                $httpUtils
     * @param array                    $options
     */
    public function __construct(
        RoleFactoryInterface $roleFactory,
        LoggerInterface $logger,
        EventDispatcherInterface $dispatcher,
        AuthorizationChecker $AuthorizationChecker,
        HttpUtils $httpUtils,
        array $options
    ) {
        parent::__construct($httpUtils, $options);

        $this->roleFactory = $roleFactory;
        $this->logger = $logger;
        $this->authorizationChecker = $AuthorizationChecker;
        $this->dispatcher = $dispatcher;
    }

    /**
     * We deal with the case where the connection is limited to a set of roles (ajax or not ajax connection).
     *
     * @param Request        $request The request service
     * @param TokenInterface $token   The token class
     *
     * @access public
     * @return Response
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     * @throws \InvalidArgumentException When the HTTP status code is not valid
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $user = $token->getUser();
        // Log handler
        $this->logger->info("User ".$user->getId()." has been saved", array('user' => $user));

        if (isset($_POST['roles']) && !empty($_POST['roles'])) {
            $all_authorization_roles = json_decode($_POST['roles'], true);
            $best_roles_name = $this->roleFactory->getBestRoleUser();
            if (is_array($all_authorization_roles)
                && !in_array($best_roles_name, $all_authorization_roles)
            ) {
                // Set a flash message
                $request->getSession()->getFlashBag()->add('notice', "Vous n'êtes pas autorisé à vous connecté !");
                // we disconnect user
                $request->getSession()->invalidate();
            }
        }
        $response = new Response(json_encode('ok'));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
