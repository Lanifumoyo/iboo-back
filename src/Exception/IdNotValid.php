<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class IdNotValid extends HttpException
{
    public function __construct(string $message = null)
    {
        parent::__construct(400,$message ?? 'Not valid id');
    }
}