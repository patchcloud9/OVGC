<?php

namespace Core\Exceptions;

/**
 * Base HTTP Exception
 * 
 * All HTTP-specific exceptions extend this class.
 */
abstract class HttpException extends \Exception
{
    protected int $statusCode = 500;
    
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
    
    /**
     * Get the HTTP status code
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
