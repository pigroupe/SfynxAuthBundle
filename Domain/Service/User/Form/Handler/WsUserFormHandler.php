<?php
namespace Sfynx\AuthBundle\Domain\Service\User\Form\Handler;

use Sfynx\AuthBundle\Domain\Generalisation\Interfaces\UserInterface;
use Sfynx\AuthBundle\Domain\Service\User\Generalisation\Interfaces\UserManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use Sfynx\AuthBundle\Domain\Entity\User;
use Sfynx\AuthBundle\Infrastructure\Validator\SubmitUserValidator;
use Sfynx\AuthBundle\Domain\Service\User\UserWS;

/**
 * This class is used to process json data transmit in post from the new api Webservice
 * If data are valide, a new User is created
 * If data are not valide, the handler return errors in json
 *
 * @category   Sfynx\AuthBundle
 * @package    Domain
 * @subpackage Service\User\Form\Handler
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2015 PI-GROUPE
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    2.3
 * @link       http://opensource.org/licenses/gpl-license.php
 * @since      2015-02-16
 */
class WsUserFormHandler
{
    /** @var SubmitUserValidator */
    protected $validator;
    /** @var Validator */
    protected $sfValidator;
    /** @var UserManagerInterface */
    protected $userManager;
    /** @var MailerInterface */
    protected $mailer;
    /** @var User */
    protected $newUser = null;
    /** @var array */
    protected $submitDatas = null;
    /** @var Request */
    protected $request;
    /** @var EventDispatcherInterface */
    protected $dispatcher;

    /**
     * WsUserFormHandler constructor.
     * @param ContainerInterface $container
     * @param SubmitUserValidator $validator
     * @param UserManagerInterface $userManager
     * @param ValidatorInterface $sfValidator
     * @param null $mailer
     */
    public function __construct(
        ContainerInterface $container,
        SubmitUserValidator $validator,
        UserManagerInterface $userManager,
        ValidatorInterface $sfValidator,
        $mailer = null
    ) {
        $this->validator   = $validator;
        $this->sfValidator = $sfValidator;
        $this->userManager = $userManager;
        $this->mailer      = $mailer;
        $this->container   = $container;
        $this->request     = $container->get('request_stack')->getCurrentRequest();
        $this->dispatcher  = $container->get('event_dispatcher');
    }

    /**
     * @param Request $request
     */
    public function bindDatas(Request $request)
    {
        $this->request     = $request;
        $this->submitDatas = $this->validator->setSubmitedUserDatas($request);
    }

    /**
     * Process of the user form validation
     * @return array
     * @throws \Exception
     */
    public function process()
    {
        if ($this->submitDatas === null) {
             throw new \Exception("You must bind datas before process");
        }
        if (!$this->validator->isValide()) {
            throw new \Exception(
                $this->validator->getErrors(),
                $this->validator->getValidationCode()
            );
        }
        $this->createUser();
        if (!$this->newUser || !$this->newUser->getId()) {
            throw new \Exception(
                json_encode(array("error"  => "Erreur serveur, veuillez réessayer ultérieurement.")),
                500
            );
        }
        return $this->getUserInJson();
    }

    /**
     * Create a new user
     * @throws \Exception
     */
    protected function createUser()
    {
        $this->testIfEmailAlreadyInUse();
        $this->testIfUsernameAlreadyInUse();
        $this->populateUserWithSubmitedDatas();
        $modelErrors = $this->sfValidator->validate($this->newUser);
        if (count($modelErrors)) {
            throw new \Exception(
                json_encode($modelErrors),
                400
            );
        }

        $this->userManager->update($this->newUser);
        if ($this->mailer) {
            $this->mailer->sendConfirmationEmailMessage($this->newUser);
        }
    }

    protected function testIfEmailAlreadyInUse()
    {
        $email = $this->userManager->findUserByEmail($this->submitDatas['email']);
        if ($email) {
            throw new \Exception(
                json_encode(array("email" => "Cet email est déjà utilisé.")),
                403
            );
        }
    }

    protected function testIfUsernameAlreadyInUse()
    {
        $email = $this->userManager->findUserByUsername($this->submitDatas['email']);
        if ($email) {
            throw new \Exception(
                json_encode(array("email" => "Cet email est déjà utilisé.")),
                403
            );
        }
    }

    protected function populateUserWithSubmitedDatas()
    {
        $this->newUser = new User();
        if (isset($this->submitDatas['enabled'])) {
            $this->newUser->setEnabled($this->submitDatas['enabled']);
        }
        $this->newUser->setNickname($this->submitDatas['first_name']);
        $this->newUser->setName($this->submitDatas['last_name']);
        $this->newUser->setEmail($this->submitDatas['email']);
        //
        $this->newUser->addRole($this->submitDatas['connexion']['role']);
        $this->newUser->setUsername($this->submitDatas['connexion']['username']);
        $this->newUser->setPlainPassword($this->submitDatas['connexion']['password']);
        if (isset($this->submitDatas['location'])) {
            $this->newUser->setAddress($this->submitDatas['location']['address']);
            $this->newUser->setZipCode($this->submitDatas['location']['cp']);
            $this->newUser->setCity($this->submitDatas['location']['city']);
            if (isset($this->submitDatas['location']['country'])) {
                $this->newUser->setCountry($this->submitDatas['location']['country']);
            }
        }
        if (isset($this->submitDatas['birthday'])) {
            $this->newUser->setBirthday($this->submitDatas['birthday']);
        }
        if (isset($this->submitDatas['global_optin'])) {
            $this->newUser->setGlobalOptIn($this->submitDatas['global_optin']);
        }
        if (isset($this->submitDatas['site_optin'])) {
            $this->newUser->setSiteOptIn($this->submitDatas['site_optin']);
        }
    }

    protected function getUserInJson()
    {
        $userWs = new UserWS($this->newUser);

        return $userWs->jsonSerialize();
    }
}
