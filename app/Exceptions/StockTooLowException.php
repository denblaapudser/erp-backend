<?php

namespace App\Exceptions;

use Exception;


class StockTooLowException extends Exception
{
    public function __construct($message = "Der er ikke nok på lager", $code = 422, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}