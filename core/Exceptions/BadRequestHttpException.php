<?php

namespace Core\Exceptions;

/**
 * 400 Bad Request Exception
 * 
 * Throw this when the request is malformed or invalid.
 * 
 * Usage:
 *   throw new BadRequestHttpException('Invalid request data');
 */
class BadRequestHttpException extends HttpException
{
    protected int $statusCode = 400;
    
    public function __construct(string $message = 'The request was invalid', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
