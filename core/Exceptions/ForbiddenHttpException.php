<?php

namespace Core\Exceptions;

/**
 * 403 Forbidden Exception
 * 
 * Throw this when the user is authenticated but doesn't have permission.
 * 
 * Usage:
 *   throw new ForbiddenHttpException('You do not have permission to access this resource');
 */
class ForbiddenHttpException extends HttpException
{
    protected int $statusCode = 403;
    
    public function __construct(string $message = 'You do not have permission to access this resource', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
