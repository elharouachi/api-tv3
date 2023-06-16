<?php

namespace App\EventListener;

use ApiPlatform\Core\EventListener\ExceptionListener as ApiPlatformExceptionListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ApiExceptionListener
{
    /**
     * @var ApiPlatformExceptionListener
     */
    private $decorated;

    public function __construct(ApiPlatformExceptionListener $decorated)
    {
        $this->decorated = $decorated;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $request = $event->getRequest();
        $requestFormat = $request->getRequestFormat('');

        if (empty($requestFormat)) {
            $request->attributes->set('_api_respond', 1);
        }

        $this->decorated->onKernelException($event);
    }
}
