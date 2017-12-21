<?php
namespace Sfynx\AuthBundle\Infrastructure\EventListener\HandlerException;

use Sfynx\AuthBundle\Infrastructure\Exception\InvalidArgumentException;
use Sfynx\AuthBundle\Infrastructure\EventListener\HandlerException\Generalisation\ResponseExceptionInterface;
use Sfynx\AuthBundle\Infrastructure\EventListener\HandlerException\Handler\JsonResponseException;
use Sfynx\AuthBundle\Infrastructure\EventListener\HandlerException\Handler\HtmlResponseException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class HandlerProfilerBuild
 *
 * @category   Sfynx\AuthBundle
 * @package    Infrastructure
 * @subpackage EventListener\HandlerException
 */
class HandlerExceptionBuild
{
    /** @var string JSON format code from the Symfony\Component\HttpFoundation\Response object. */
    const FORMAT_JSON = 'json';

    const FORMAT_HTML = 'html';

    /**
     * List of concrete $responseException that can be built using this factory.
     * @var string[]
     */
    protected static $responseExceptionList = [
        self::FORMAT_JSON => JsonResponseException::class,
        self::FORMAT_HTML => HtmlResponseException::class,
    ];

    /**
     * Return the right instance of an ResponseProfilerInterface object or null if not in the available list.
     *
     * @param string $requestFormat
     * @return ResponseExceptionInterface
     * @throws InvalidArgumentException
     */
    public static function build($requestFormat, HandlerExceptionFactory $factory)
    {
        if (array_key_exists($requestFormat, self::$responseExceptionList)) {
            return new self::$responseExceptionList["$requestFormat"]($factory);
        }
        throw InvalidArgumentException::invalidArgument();
    }
}
