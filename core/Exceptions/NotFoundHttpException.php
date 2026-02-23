<?php

namespace Core\Exceptions;

/**
 * 404 Not Found Exception
 * 
 * Throw this when a resource cannot be found.
 * 
 * Usage:
 *   throw new NotFoundHttpException('User not found');
 *   throw new NotFoundHttpException(); // Uses default message
 */
class NotFoundHttpException extends HttpException
{
    protected int $statusCode = 404;
    
    public function __construct(string $message = 'The requested resource was not found', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
