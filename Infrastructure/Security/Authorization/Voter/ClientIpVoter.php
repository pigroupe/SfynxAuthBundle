<?php
/**
 * This file is part of the <Auth> project.
 * Creating a Custom Voter from the blacklist defined in the services.yml
 *
 * (c) <etienne de Longeaux> <etienne.delongeaux@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\AuthBundle\Infrastructure\Security\Authorization\Voter;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ClientIpVoter implements VoterInterface
{
    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     * @param array              $blacklistedIp
     */
    public function __construct(ContainerInterface $container, $blacklistedIp = array())
    {
        $this->container     = $container;
        $this->blacklistedIp = $blacklistedIp;
    }

    public function supportsAttribute($attribute)
    {
        // we won't check against a user attribute, so we return true
        return true;
    }

    public function supportsClass($class)
    {
        // our voter supports all type of token classes, so we return true
        return true;
    }

    public function vote(TokenInterface $token, $object, array $attributes)
    {
        if (!(null === $this->blacklistedIp)) {
            $getClientIp    = $this->container->get('request_stack')->getCurrentRequest()->getClientIp();
            if (!in_array($getClientIp, $this->blacklistedIp)
                    && !in_array('IS_AUTHENTICATED_ANONYMOUSLY', $attributes)
            ) {
                return VoterInterface::ACCESS_DENIED;
            }
            return VoterInterface::ACCESS_ABSTAIN;
        }
        return VoterInterface::ACCESS_ABSTAIN;
    }
}
