<?php
namespace Sfynx\AuthBundle\Domain\Generalisation;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * trait class for default attributs.
 *
 * @category   Generalisation
 * @package    Trait
 * @subpackage Entity
 * @abstract
 */
trait TraitApplicationTokens
{
    /**
     * @var array
     * @ORM\Column(name="application_tokens", type="array", nullable=true)
     */
    protected $applicationTokens;

    /**
     * Get application tokens
     *
     * @return array
     */
    public function getApplicationTokens()
    {
        return $this->applicationTokens;
    }

    /**
     * Set application tokens
     *
     * @param array $all
     */
    public function setApplicationTokens(array $all)
    {
        foreach ($all as $one) {
            $one = strtoupper($one);
            if (null === $this->applicationTokens) {
                $this->applicationTokens[] = $one;
            } else {
                $info = explode("::", $one);
                $name = $info[0];
                $is_in = false;
                foreach ($this->applicationTokens as $key => $appl) {
                    $appl = strtoupper($appl);
                    $info_ = explode("::", $appl);
                    $name_ = $info_[0];
                    if ($name == $name_) {
                        $this->applicationTokens[ $key ] = $one;
                        $is_in = true;
                    }
                }
                if (!$is_in) {
                    $this->applicationTokens[] = $one;
                }
            }
        }
    }

    /**
     * we add a token associate to an application
     *
     * @param string $application
     * @param string $token
     *
     * @return integer
     * @access public
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function addTokenByApplicationName($application, $token)
    {
        $this->setApplicationTokens(array(strtoupper($application.'::'.$token)));
    }

    /**
     * we return the token associated to the name given in param.
     *
     * @param string $name
     *
     * @return integer
     * @access public
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function getTokenByApplicationName($name)
    {
        $all_appl =  $this->applicationTokens;
        if (!(null === $all_appl)) {
            foreach ($all_appl as $appl) {
                $string = strtoupper($appl);
                $replace = strtoupper($name.'::');
                $token = str_replace($replace, '', $string, $count);
                if ($count === 1) {
                    return strtoupper($token);
                }
            }
        }
        return '';
    }
}
