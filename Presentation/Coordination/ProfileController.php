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

use Sfynx\ToolBundle\Builder\RouteTranslatorFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Sfynx\AuthBundle\Domain\Generalisation\Interfaces\UserInterface;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sfynx\CoreBundle\Controller\abstractController;

use Sfynx\AuthBundle\Domain\Service\User\UserStorage;

/**
 * Controller managing the user profile
 *
 * @subpackage Profile
 * @package    Controller
 */
class ProfileController extends abstractController
{
    /** @var RouteTranslatorFactoryInterface $routeFactory */
    protected $routeFactory;
    /** @var EngineInterface $templating */
    protected $templating;
    /** @var UserStorage $TokenStorage */
    protected $TokenStorage;
    /** @var array */
    protected $param = [];

    /**
     * FrontendController constructor.
     *
     * @param RouteTranslatorFactoryInterface $routeFactory
     * @param EngineInterface $templating
     * @param TokenStorageInterface $TokenStorage
     * @param string $sfynx_auth_theme_login
     */
    public function __construct(
        RouteTranslatorFactoryInterface $routeFactory,
        EngineInterface $templating,
        TokenStorageInterface $TokenStorage,
        $sfynx_auth_theme_login
    ) {
        $this->routeFactory = $routeFactory;
        $this->templating = $templating;
        $this->tokenStorage = new UserStorage($TokenStorage);
        $this->param["sfynx_auth_theme_login"] = $sfynx_auth_theme_login;
    }

    /**
     * Show the user profile
     */
    public function showAction()
    {
        $user = $this->tokenStorage->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        return $this->templating->renderResponse(
            str_replace('::', ':', $this->param["sfynx_auth_theme_login"]) . 'Profile:show.html.twig',
            array('user' => $user)
        );
    }
}
