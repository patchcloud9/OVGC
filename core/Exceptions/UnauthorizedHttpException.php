<?php

namespace Core\Exceptions;

/**
 * 401 Unauthorized Exception
 * 
 * Throw this when authentication is required but not provided.
 * 
 * Usage:
 *   throw new UnauthorizedHttpException('Please log in to continue');
 */
class UnauthorizedHttpException extends HttpException
{
    protected int $statusCode = 401;
    
    public function __construct(string $message = 'Authentication is required', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
