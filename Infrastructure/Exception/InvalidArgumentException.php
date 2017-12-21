<?php

namespace Sfynx\AuthBundle\Infrastructure\Exception;

use Exception;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class InvalidArgumentException
 */
class InvalidArgumentException extends Exception
{
    /**
     * InvalidArgumentException constructor.
     * @param string $message
     * @param Exception|null $previous
     */
    public function __construct($message = '', Exception $previous = null)
    {
        parent::__construct($message, Response::HTTP_BAD_REQUEST, $previous);
    }

    /**
     * @param mixed $value
     * @param string $expectedType
     * @return ValidationException
     */
    public static function invalidType($value, $expectedType)
    {
        $valueType = is_object($value) ? get_class($value) : gettype($value);

        return new static(sprintf('Expected argument of type "%s", "%s" given', $expectedType, $valueType));
    }

    /**
     * @return InvalidArgumentException
     */
    public static function invalidArgument()
    {
        return new static('Invalid argument given');
    }
}
