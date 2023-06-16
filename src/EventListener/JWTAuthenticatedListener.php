<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Namshi\JOSE\JWS;
use Symfony\Component\Security\Core\User\UserInterface;

class JWTAuthenticatedListener
{
    private $tokenTTL;

    public function __construct($tokenTTL)
    {
        $this->tokenTTL = $tokenTTL;
    }

    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        $token = JWS::load($data['token']);

        $data['tokenDuration'] = (int) $this->tokenTTL;
        $data['tokenExpirationDate'] = date(\DateTimeInterface::RFC3339, $token->getPayload()['exp']);

        $event->setData($data);
    }
}
