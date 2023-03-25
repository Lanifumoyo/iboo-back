<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductNotFoundException extends NotFoundHttpException
{
    public function __construct(string $message = null)
    {
        parent::__construct($message ?? 'Entity not found',null,404);
    }
}