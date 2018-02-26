<?php
namespace Sfynx\AuthBundle\Domain\Service\User\Manager;

use Sfynx\CoreBundle\Layers\Infrastructure\Persistence\Factory\Generalisation\AdapterFactoryInterface;
use Sfynx\AuthBundle\Domain\Service\User\Generalisation\Interfaces\UserManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Sfynx\CoreBundle\Layers\Domain\Service\Request\Generalisation\RequestInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sfynx\AuthBundle\Domain\Generalisation\Interfaces\UserInterface;
use Sfynx\CoreBundle\Layers\Application\Command\Generalisation\Interfaces\CommandInterface;
use Sfynx\CoreBundle\Layers\Domain\Model\Interfaces\EntityInterface;
use Sfynx\CoreBundle\Layers\Domain\Service\Manager\Generalisation\AbstractManager;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\HttpFoundation\Response;

use Sfynx\AuthBundle\Infrastructure\Event\ViewObject\ResponseEvent;
use Sfynx\AuthBundle\Infrastructure\Event\SfynxAuthEvents;
use Sfynx\AuthBundle\Domain\Service\Mailer\PiMailerManager;
use Sfynx\AuthBundle\Domain\Service\User\UserStorage;
use Sfynx\ToolBundle\Util\PiStringManager;

/**
 * User manager working with entities (Orm, Odm, Couchdb)
 *
 * @category   Sfynx\AuthBundle
 * @package    Domain
 * @subpackage Service\User\Manager
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
class EntityManager extends AbstractManager implements UserManagerInterface
{
    /** @var UserStorage */
    protected $tokenStorage;
    /** @var EncoderFactoryInterface */
    protected $encoderFactory;
    /** @var RequestInterface */
    protected $request;
    /** @var ContainerInterface */
    protected $container;

    /**
     * Constructor.
     *
     * @param AdapterFactoryInterface $factory
     * @param EncoderFactoryInterface $encoderFactory
     * @param RequestInterface $request
     * @param TokenStorageInterface $security
     * @param ContainerInterface $container
     */
    public function __construct(
        AdapterFactoryInterface $factory,
        EncoderFactoryInterface $encoderFactory,
        RequestInterface $request,
        TokenStorageInterface $TokenStorage,
        ContainerInterface $container
    ) {
        parent::__construct($factory);

        $this->encoderFactory = $encoderFactory;
        $this->request = $request;
        $this->tokenStorage = new UserStorage($TokenStorage);
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function authenticateUser(UserInterface $user = null, &$response = null, $deleteToken = false)
    {
        $locale    = $this->request->getLocale();

        if (null === $user) {
            $token = $this->request->query->get('token');
            $user  = $this->findUserByConfirmationToken($token);
        }

        $token     = new UsernamePasswordToken($user, null, $this->param->providerKey, $user->getRoles());
        $this->tokenStorage->setToken($token); //now the user is logged in
        $request->getSession()->set('_security_'.$this->param->providerKey, serialize($token));
        $request->getSession()->set('_security_secured_area', serialize($token));
	    // we delete token user
        if ($deleteToken) {
            $user->setConfirmationToken(null);
            $this->update($user, true);
        }
        //now dispatch the login event
        //$request = $this->container->get("request");
        //$event = new \Symfony\Component\Security\Http\Event\InteractiveLoginEvent($request, $token);
        //$this->get("event_dispatcher")->dispatch("security.interactive_login", $event);
        //
        if ($response instanceof Response) {
            // Record the layout variable in cookies.
            if ($this->param->dateExpire && !empty($this->param->date_interval)) {
                if (is_numeric($this->param->date_interval)) {
                    $this->param->dateExpire = time() + intVal($this->param->date_interval);
                } else {
                    $this->param->dateExpire = new \DateTime("NOW");
                    $this->param->dateExpire->add(new \DateInterval($this->param->date_interval));
                }
            }
            // we apply all events allowed to change the redirection response
            $event_response = new ResponseEvent($response, (int) $this->param->dateExpire, $this->getRequest(), $user, $locale);
            $this->container->get('event_dispatcher')->dispatch(SfynxAuthEvents::HANDLER_LOGIN_CHANGERESPONSE, $event_response);
            $response = $event_response->getResponse();
        }

        return $response;
    }

    /**
     * {@inheritDoc}
     */
    public function disconnectUser()
    {
        $this->request->getSession()->invalidate();
    }

    /**
     * {@inheritDoc}
     */
    public function tokenUser(UserInterface $user)
    {
        $user->setConfirmationToken();
        $this->getCommandRepository()->persist($user, true);

        $this->request
                ->getSession()
                ->set(PiMailerManager::SESSION_EMAIL, $this->container->get('sfynx.auth.mailer')->getObfuscatedEmail($user));

        return $user->getConfirmationToken();
    }

    /**
     * {@inheritDoc}
     */
    public function isUserdIdExisted($userId)
    {
        $entity = $this->find($userId);
        if ($entity instanceof UserInterface) {
            return true;
        }
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getEncoder(UserInterface $user)
    {
        return $this->encoderFactory->getEncoder($user);
    }

    /**
     * {@inheritDoc}
     */
    public function getTokenByUserIdAndApplication($userId, $application)
    {
        $entity = $userId;
    	if (!($userId instanceof UserInterface)) {
            $entity = $this->find($userId);
    	}
        if ($entity instanceof UserInterface) {
            return $entity->getTokenByApplicationName($application);
        }
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function setAssociationUserIdWithApplicationToken($userId, $token, $application)
    {
        $entity = $userId;
        if (!($userId instanceof UserInterface)) {
            $entity = $this->find($userId);
        }
        if ($entity instanceof UserInterface) {
            $entity->addTokenByApplicationName($application, $token);
            $this->getCommandRepository()->persist($entity, true);

            return true;
        }
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function findUserBy(array $criteria)
    {
        return $this->getQueryRepository()->findOneBy($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function findUserByUsername($username)
    {
        return $this->findUserBy(array('usernameCanonical' => PiStringManager::canonicalize($username)));
    }

    /**
     * {@inheritDoc}
     */
    public function findUserByEmail($email)
    {
        return $this->findUserBy(array('emailCanonical' => PiStringManager::canonicalize($email)));
    }

    /**
     * {@inheritDoc}
     */
    public function findUserByUsernameOrEmail($usernameOrEmail)
    {
        if (filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL)) {
            return $this->findUserByEmail($usernameOrEmail);
        }

        return $this->findUserByUsername($usernameOrEmail);
    }

    /**
     * {@inheritDoc}
     */
    public function findUserByConfirmationToken($token)
    {
        return $this->findUserBy(array('confirmationToken' => $token));
    }

    /**
     * {@inheritDoc}
     */
    public function update(EntityInterface $entity, $andFlush = true): void
    {
        $this->updateCanonicalFields($entity);
        $this->updatePassword($entity);

        $this->getCommandRepository()->persist($entity, $andFlush);
    }

    /**
     * {@inheritDoc}
     */
    public function updateCanonicalFields(UserInterface $user)
    {
        $user->setUsernameCanonical(PiStringManager::canonicalize($user->getUsername()));
        $user->setEmailCanonical(PiStringManager::canonicalize($user->getEmail()));
    }

    /**
     * {@inheritDoc}
     */
    public function updatePassword(UserInterface $user)
    {
        if (0 !== strlen($password = $user->getPlainPassword())) {
            $user->setPasswordRequestedAt(new \DateTime());
            $encoder = $this->getEncoder($user);
            $user->setPassword($encoder->encodePassword($password, $user->getSalt()));
            $user->eraseCredentials();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function newFromCommand(CommandInterface $command): EntityInterface
    {
        $class = $this->getClass();
        /** @var  UserInterface */
        $entity = $class::newFromCommand($command);
        $this->transformEntity($entity, $command);

        return $entity;
    }

    /**
     * {@inheritDoc}
     */
    public function buildFromCommand(EntityInterface $entity, CommandInterface $command): EntityInterface
    {
        $class = $this->getClass();
        $entity = $class::buildFromCommand($entity, $command);
        $this->transformEntity($entity, $command);

        return $entity;
    }

    /**
     * {@inheritDoc}
     */
    public function buildFromEntity(CommandInterface $command, EntityInterface $entity): CommandInterface
    {
        $class = $this->getClass();
        $command =  $class::buildFromEntity($command, $entity);

        return $command;
    }

    /**
     * @param EntityInterface $entity
     * @param CommandInterface $command
     * @return EntityManager
     */
    protected function transformEntity(EntityInterface &$entity, CommandInterface $command): EntityManager
    {
        if (null !== $command->getPermissions()) {
            $entity->setPermissions($command->getPermissions());
        }
        $entity->setEnabled($command->getEnabled());
        $entity->setUsername($command->getUsername());
        $entity->setName($command->getName());
        $entity->setNickname($command->getNickname());
        $entity->setEmail($command->getEmail());
        if ('' !== $command->getLangCode() && null !== $command->getLangCode()) {
            $entity->setLangCode(
                $this->getQueryRepository()->getEntityManager()->getReference(
                    '\Sfynx\AuthBundle\Domain\Entity\Langue',
                    $command->getLangCode())
            );
        }
        $entity->initGroups();
        if (null !== $command->getGroups()) {
            foreach ($command->getGroups() as $key => $groupId) {
                $entity->addGroup(
                    $this->getQueryRepository()->getEntityManager()->getReference(
                        '\Sfynx\AuthBundle\Domain\Entity\Group',
                        $groupId)
                );
            }
        }

        if(!empty($command->getPlainPassword()['first'])
            && !empty($command->getPlainPassword()['second']))
        {
            $entity->setPassword($this->getEncoder($entity)->encodePassword(
                $command->getPlainPassword()['first'],
                $entity->getSalt()));
        }
        $entity->eraseCredentials()
            ->setConfirmationToken()
            ->setUsernameCanonical($command->getUsername())
            ->setEmailCanonical($command->getEmail());

        return $this;
    }
}
