<?php

namespace Denason\Wikipedia\Exceptions;


use Exception;
use Throwable;
/**
 * Class WikipediaException
 *
 * This class is used for handling errors related to the Wikipedia API interactions.
 * It is thrown when the API response encounters an error or if processing the received data fails.
 * This exception allows the application to handle the error appropriately.
 *
 * @package Denason\Wikipedia\Exceptions
 *
 * @extends Exception
 */

class WikipediaException extends Exception
{
    /**
     * WikipediaException constructor.
     *
     * @param string         $message  The error message
     * @param int            $code     The error code (default is 0)
     * @param Throwable|null $previous The previous exception for chaining (optional)
     */
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
