<?php
namespace Sfynx\AuthBundle\Infrastructure\EventListener\HandlerException\Generalisation;

use Symfony\Component\HttpFoundation\Response;
use Sfynx\AuthBundle\Infrastructure\EventListener\HandlerException\HandlerExceptionFactory;

/**
 * Interface ResponseExceptionInterface
 * @category   Sfynx\AuthBundle
 * @package    Infrastructure
 * @subpackage EventListener\HandlerException\Handler
 */
interface ResponseExceptionInterface
{
    /**
     * ResponseExceptionInterface constructor.
     * @param HandlerExceptionFactory $factory
     */
    public function __construct(HandlerExceptionFactory $factory);

    /**
     * @return Response
     */
    public function getResponse();
}
