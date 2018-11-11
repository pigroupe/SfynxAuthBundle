<?php
/**
 * This file is part of the <Auth> project.
 *
 * @subpackage   User
 * @package    Manager
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @since 2012-02-03
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\AuthBundle\Domain\Service\Role;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Sfynx\AuthBundle\Domain\Service\Role\Generalisation\RoleFactoryInterface;
use Sfynx\CoreBundle\Layers\Domain\Service\Role\RoleManager;

/**
 * role factory.
 *
 * @category   Sfynx\AuthBundle
 * @package    Domain
 * @subpackage Service\Role
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
class RoleFactory  implements RoleFactoryInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $_container;
    /** @var TokenStorageInterface */
    protected $tokenStorage;
    /** @var string */
    protected $kernelRoot;
    /** @var array */
    protected $hierarchy;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param TokenStorageInterface $tokenStorage
     * @param string $kernelRoot
     * @param array $hierarchy
     */
    public function __construct(ContainerInterface $container, TokenStorageInterface $tokenStorage, string $kernelRoot, array $hierarchy = [])
    {
        $this->_container = $container;
        $this->tokenStorage = $tokenStorage;
        $this->kernelRoot = $kernelRoot;
        $this->hierarchy = $hierarchy;
    }

    /**
     * {@inheritdoc}
     */
    public function getJsonFile()
    {
        return $this->kernelRoot . "/cachesfynx/heritage.json";
    }

    /**
     * {@inheritdoc}
     */
    public function getNoAuthorizeRoles(array $heritage)
    {
        return RoleManager::getNoAuthorizeRolesFromUser($heritage, $this->hierarchy);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllUserRoles()
    {
        if ($this->isUsernamePasswordToken()) {
            return \array_unique(\array_merge(
                RoleManager::getAllHeritageByRoles($this->hierarchy, RoleManager::getBestRoles($this->hierarchy, $this->getUserRoles())),
                $this->getUserRoles()
            ));
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getBestRoleUser()
    {
        if ($this->isUsernamePasswordToken()) {
            return RoleManager::getUserBestRoles($this->hierarchy, $this->getUserRoles());
        }
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getBestRoles(?array $roles)
    {
        return RoleManager::getBestRoles($this->hierarchy, $roles);
    }

    /**
     * {@inheritdoc}
     */
    public function isJsonFileExisted()
    {
        return \realpath($this->getJsonFile());
    }

    /**
     * {@inheritdoc}
     */
    public function setJsonFileRoles(bool $isForce = true)
    {
        if ($isForce
            || !$this->isJsonFileExisted()
        ) {
            // we register the hierarchy roles in the heritage.jon file in the cache
            $em = $this->_container->get('doctrine')->getManager();
            $roles = $em->getRepository('SfynxAuthBundle:Role')->getAllHeritageRoles();

//            // we delete cache files
//            $path_files[] = realpath($this->getContainer()->getParameter("kernel.cache_dir") . "/appDevDebugProjectContainer.php");
//            $path_files[] = realpath($this->getContainer()->getParameter("kernel.cache_dir") . "/appDevDebugProjectContainer.php.meta");
//            $path_files[] = realpath($this->getContainer()->getParameter("kernel.cache_dir") . "/appDevDebugProjectContainer.xml");
//            $path_files[] = realpath($this->getContainer()->getParameter("kernel.cache_dir") . "/appDevDebugProjectContainerCompiler.log");
//            $path_files[] = realpath($this->getContainer()->getParameter("kernel.cache_dir") . "/appProdProjectContainer.php");
//            $path_files = array_unique($path_files);
//
//            foreach ($path_files as $key=>$file) {
//                if (!empty($file)) {
//                    unlink($file);
//                }
//            }

            return \file_put_contents(
                $this->getJsonFile(),
                \json_encode(['HERITAGE_ROLES'=>$roles], JSON_UNESCAPED_UNICODE)
            );
        }
    }

    /**
     * Return if yes or no the user is UsernamePassword token.
     *
     * @return boolean
     * @access protected
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    protected function isUsernamePasswordToken()
    {
        if ($this->getToken() instanceof \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken) {
            return true;
        }
        return false;
    }

    /**
     * Return the user roles.
     *
     * @return array user roles
     * @access protected
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    protected function getUserRoles()
    {
        return $this->getToken()->getUser()->getRoles();
    }

    /**
     * Return the token object.
     *
     * @return \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken
     * @access protected
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    protected function getToken()
    {
        return $this->tokenStorage->getToken();
    }
}
