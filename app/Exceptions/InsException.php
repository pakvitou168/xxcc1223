<?php

namespace App\Exceptions;

use Exception;

class InsException extends Exception
{
    public function __construct($message = "Something went wrong", $code = 500)
    {
        parent::__construct($message, $code);
    }

    public function render($request)
    {
        return response()->json([
            'error' => $this->getMessage(),
        ], $this->getCode());
    }
}
