<?php

namespace App\EventListener;

use App\Exception\InvalidEntityAssociationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof InvalidEntityAssociationException) {
            return;
        }

        $responseData = \array_merge(
            ['title' => 'Validation Failed'],
            ['detail' => $exception->getMessage()]
        );

        $headers = ['Content-Type' => 'application/problem+json; charset=utf-8'];

        $event->setResponse(new JsonResponse($responseData, $exception->getCode(), $headers));
    }
}
