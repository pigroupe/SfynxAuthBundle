<?php
namespace Sfynx\AuthBundle\Application\EventListener\HandlerProfiler;

use Sfynx\AuthBundle\Infrastructure\Exception\InvalidArgumentException;
use Sfynx\AuthBundle\Application\EventListener\Profiler\Generalisation\ResponseProfilerInterface;
use Sfynx\AuthBundle\Application\EventListener\HandlerProfiler\Handler\JsonResponseProfiler;
use Sfynx\AuthBundle\Application\EventListener\HandlerProfiler\Handler\XmlResponseProfiler;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class HandlerProfilerBuild
 *
 * @category   Sfynx\AuthBundle
 * @package    Infrastructure
 * @subpackage EventListener\HandlerProfiler
 */
class HandlerProfilerBuild
{
    /** @var string JSON format code from the Symfony\Component\HttpFoundation\Response object. */
    const FORMAT_JSON = 'json';

    const FORMAT_XML = 'xml';

    /**
     * List of concrete ResponseProfiler that can be built using this factory.
     * @var string[]
     */
    protected static $responseProfilerList = [
        self::FORMAT_JSON => JsonResponseProfiler::class,
        self::FORMAT_XML => XmlResponseProfiler::class,
    ];

    /**
     * Return the right instance of an ResponseProfilerInterface object or null if not in the available list.
     *
     * @param string $requestFormat
     * @return ResponseProfilerInterface
     * @throws InvalidArgumentException
     */
    public static function build($requestFormat, Response $response)
    {
        if (array_key_exists($requestFormat, self::$responseProfilerList)) {
            return new self::$responseProfilerList["$requestFormat"]($response);
        }
        throw InvalidArgumentException::invalidArgument();
    }
}
