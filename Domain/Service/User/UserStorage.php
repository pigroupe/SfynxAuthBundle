<?php
namespace Sfynx\AuthBundle\Domain\Service\User;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * UserStorage
 *
 * @category   Auth
 * @package    User
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
class UserStorage
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * Constructor.
     *
     * @param TokenInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Returns the current security token.
     *
     * @return TokenInterface|null A TokenInterface instance or null if no authentication information is available
     */
    public function getToken()
    {
        return  $this->tokenStorage->getToken();
    }

    /**
     * Sets the authentication token.
     *
     * @param TokenInterface $token A TokenInterface token, or null if no further authentication information should be stored
     */
    public function setToken(TokenInterface $token = null)
    {
        return  $this->tokenStorage->getToken();
    }

    /**
     * Return the connected user entity.
     *
     * @return UserInterface
     * @access public
     */
    public function getUser()
    {
        return $this->getToken()->getUser();
    }

    /**
     * Return the connected user name.
     *
     * @return string User name
     * @access public
     */
    public function getUserName()
    {
        return $this->getToken()->getUser()->getUsername();
    }

    /**
     * Return the user permissions.
     *
     * @return array User permissions
     * @access public
     */
    public function getUserPermissions()
    {
        return $this->getToken()->getUser()->getPermissions();
    }

    /**
     * Return the user roles.
     *
     * @return array User roles
     * @access public
     */
    public function getUserRoles()
    {
        return $this->getToken()->getUser()->getRoles();
    }

    /**
     * Return if yes or no the user is anonymous token.
     *
     * @return boolean
     * @access public
     */
    public function isAnonymousToken()
    {
        if (($this->getToken() instanceof AnonymousToken)
            || ($this->getToken() === null)
        ) {
            return true;
        }
        return false;
    }

    /**
     * Return if yes or no the user is UsernamePassword token.
     *
     * @return boolean
     * @access public
     */
    public function isUsernamePasswordToken()
    {
        if ($this->getToken() instanceof UsernamePasswordToken) {
            return true;
        }
        return false;
    }
}
