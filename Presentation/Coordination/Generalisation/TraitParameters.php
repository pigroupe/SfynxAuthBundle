<?php

namespace Sfynx\AuthBundle\Presentation\Coordination\Generalisation;

/**
 * Trait Parameters
 *
 * @subpackage Sfynx\AuthBundle
 * @package    Presentation
 * @subpackage Coordination\Generalisation
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com
 */
trait TraitParameters
{
    /** @var Array */
    protected $parameters = array();

    /**
     * Get parameter
     *
     * @param string $name
     *
     * @return string
     */
    public function getParameter($name)
    {
        if (array_key_exists($name, $this->parameters)) {
            return $this->parameters[$name];
        }
        throw new \InvalidArgumentException(sprintf('The %s parameter does not exist', $name));
    }

    /**
     * set parameter
     *
     * @param array $param
     *
     * @return void
     */
    public function setParameter(array $param)
    {
        $this->parameters = array_unique(array_merge($this->parameters, $param));
    }
}
