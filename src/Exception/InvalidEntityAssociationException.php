<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class InvalidEntityAssociationException extends \Exception
{
    public function __construct($message, $code = Response::HTTP_BAD_REQUEST, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
