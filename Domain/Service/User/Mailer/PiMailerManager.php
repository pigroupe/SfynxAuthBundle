<?php
/**
 * This file is part of the <Auth> project.
 *
 * @subpackage   Auth
 * @package    Controller
 * @abstract
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @since 2012-10-01
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\AuthBundle\Domain\Service\User\Mailer;

use Sfynx\ToolBundle\Builder\RouteTranslatorFactoryInterface;
use Sfynx\CoreBundle\Layers\Domain\Service\Request\Generalisation\RequestInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\UserBundle\Model\UserInterface;

/**
 * abstract controller.
 *
 * @subpackage Auth
 * @package    Controller
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
class PiMailerManager extends Controller
{
    const SESSION_EMAIL = 'fos_user_send_resetting_email/email';

    /** @var RouteTranslatorFactoryInterface  */
    protected $router;
    /** @var RequestInterface */
    protected $request;
    /** @var RegistryInterface $registry */
    protected $registry;
    /** @var ContainerInterface */
    protected $container;

    /**
     * Constructor.
     *
     * @param RouteTranslatorFactoryInterface $router
     * @param RequestInterface $request
     * @param RegistryInterface $registry
     * @param ContainerInterface $container The service container
     */
    public function __construct(
        RouteTranslatorFactoryInterface $router,
        RequestInterface $request,
        RegistryInterface $registry,
        ContainerInterface $container
    ) {
        $this->router = $router;
        $this->request = $request;
        $this->registry = $registry;
        $this->container = $container;
    }

    /**
     * Send mail to reset user password
     *
     * @param UserInterface $user
     * @param string        $route_reset_connexion
     * @param string        $body_type             ['body_text', 'body_html']
     *
     * @return string
     * @access public
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function sendConfirmationEmailMessage(UserInterface $user, $route_reset_connexion = 'fos_user_registration_confirm', $body_type = "body_html")
    {
        $url      = $this->router->generate($route_reset_connexion, array('token' => $user->getConfirmationToken()));
        $html_url = $this->getUrl($url);
        $html_url = "<a href='$html_url'>" . $html_url . "</a>";
        $templateFile = str_replace('::', ':', $this->container->getParameter('sfynx.template.theme.login')).'Registration:email.txt.twig';
        $from     = $this->container->getParameter('sfynx.template.theme.email.registration.from_email.address');

        $this->sendEmailMessage($templateFile, $from, $user, $html_url, $body_type);
    }

    /**
     * Send mail to reset user password
     *
     * @param UserInterface $user
     * @param string        $route_reset_connexion
     * @param string        $body_type             ['body_text', 'body_html']
     *
     * @return string
     * @access public
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function sendResettingEmailMessage(UserInterface $user, $route_reset_connexion = 'sfynx_auth_resetting_reset', $body_type = "body_html")
    {
        $url      = $this->router->generate($route_reset_connexion, array('token' => $user->getConfirmationToken()));
        $html_url = $this->getUrl($url);
        $html_url = "<a href='$html_url'>" . $html_url . "</a>";
        $templateFile = str_replace('::', ':', $this->container->getParameter('sfynx.template.theme.login')).'Resetting:email.txt.twig';
        $from     = $this->container->getParameter('sfynx.template.theme.email.resetting.from_email.address');

        $this->sendEmailMessage($templateFile, $from, $user, $html_url, $body_type);
    }

    /**
     * @param string        $templateFile
     * @param string        $from
     * @param UserInterface $user
     * @param string        $html_url
     * @param string        $body_type    ['body_text', 'body_html']
     */
    protected function sendEmailMessage($templateFile, $from, $user, $html_url, $body_type = "body_html")
    {
        $templateContent = $this->container->get('twig')->loadTemplate($templateFile);
        $email_subject   = ($templateContent->hasBlock("subject")
                ? $templateContent->renderBlock("subject", array(
                    'user'            => $user,
                    'confirmationUrl' => $html_url,
                ))
                : "Default subject here");
        $email_body      = ($templateContent->hasBlock("body")
                ? $templateContent->renderBlock($body_type, array(
                    'user'            => $user,
                    'confirmationUrl' => $html_url,
                ))
                : "Default body here");
        $this->container->get("sfynx.tool.mailer_manager")->send(
            $from,
            $user->getEmail(),
            $email_subject,
            $email_body
        );
    }

    /**
     * Generate link to reset user password (return link with url)
     *
     * @param UserInterface $user
     * @param string        $route_reset_connexion
     * @param string        $title
     * @param array         $parameters
     *
     * @return string
     * @access public
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function sendResettingEmailMessageLink(UserInterface $user, $route_reset_connexion, $title = '', $parameters = array())
    {
        $user->setConfirmationToken();
        $em = $this->registry->getManager();
        $em->persist($user);
        $em->flush();

        $this->request
            ->getSession()
            ->set(static::SESSION_EMAIL, $this->getObfuscatedEmail($user));

        $parameters = array_merge($parameters, array('token' => $user->getConfirmationToken()));

        $html_url = $this->getUrl($this->router->generate($route_reset_connexion, $parameters));

        if (empty($title)) {
            $title = $html_url;
        }
        $result = "<a href='$html_url'>" . $title . "</a>";

        return $result;
    }

    /**
     * Send mail to reset user password (return URL)
     *
     * @param UserInterface $user
     * @param string        $route_reset_connexion
     * @param string        $title
     * @param array         $parameters
     *
     * @return string
     * @access public
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function sendResettingEmailMessageURL(UserInterface $user, $route_reset_connexion, $parameters = array())
    {
        $user->setConfirmationToken();
        $em = $this->registry->getManager();
        $em->persist($user);
        $em->flush();

        $this->request
            ->getSession()
            ->set(static::SESSION_EMAIL, $this->getObfuscatedEmail($user));

        $parameters = array_merge($parameters, array('token' => $user->getConfirmationToken()));

        return $this->getUrl($this->router->generate($route_reset_connexion, $parameters));
    }

    /**
     * Get the truncated email displayed when requesting the resetting.
     *
     * The default implementation only keeps the part following @ in the address.
     *
     * @param UserInterface $user
     *
     * @return string
     */
    public function getObfuscatedEmail(UserInterface $user)
    {
        $email = $user->getEmail();
        if (false !== $pos = strpos($email, '@')) {
            $email = '...' . substr($email, $pos);
        }

        return $email;
    }

    /**
     * @param $url
     * @return string
     */
    protected function getUrl($url)
    {
        return 'http://' . $this->request->getHttpHost() . $this->request->getBasePath() . $url;
    }
}
