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
namespace Sfynx\AuthBundle\Infrastructure\Security\Encoder;

use Symfony\Component\Security\Core\Encoder\BasePasswordEncoder;

class MyPasswordEncoder extends BasePasswordEncoder
{
    public $algorithm = 'sha512'; // bcrypt sha512

    /**
     * @param string $raw
     * @param string $salt
     * @return string
     */
    public function encodePassword($raw, $salt)
    {
        $this->encodeHashAsBase64 = true;
        $this->iterations = 5000;

        if (!in_array($this->algorithm, hash_algos(), true)) {
        	throw new \LogicException(sprintf('The algorithm "%s" is not supported.', $this->algorithm));
        }

        $salted = $this->mergePasswordAndSalt($raw, $salt);
        $digest = hash($this->algorithm, $salted, true);

        // "stretch" hash
        for ($i = 1; $i < $this->iterations; $i++) {
        	$digest = hash($this->algorithm, $digest.$salted, true);
        }

        return $this->encodeHashAsBase64 ? base64_encode($digest) : bin2hex($digest);

        // ex : return md5($salt.$raw.$salt);
    }

    /**
     * @param string $encoded
     * @param string $raw
     * @param string $salt
     * @return bool
     */
    public function isPasswordValid($encoded, $raw, $salt)
    {
        return $this->comparePasswords($encoded, $this->encodePassword($raw, $salt));
    }
}
