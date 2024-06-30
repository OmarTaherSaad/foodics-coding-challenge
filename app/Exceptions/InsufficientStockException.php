<?php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    public function __construct($ingredientName)
    {
        parent::__construct("Insufficient stock for ingredient: $ingredientName");
    }
}
