<?php

namespace Core\Exceptions;

/**
 * 405 Method Not Allowed Exception
 * 
 * Throw this when the HTTP method is not supported for the route.
 * 
 * Usage:
 *   throw new MethodNotAllowedHttpException('POST method not allowed on this route');
 */
class MethodNotAllowedHttpException extends HttpException
{
    protected int $statusCode = 405;
    
    public function __construct(string $message = 'The HTTP method is not allowed for this route', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
