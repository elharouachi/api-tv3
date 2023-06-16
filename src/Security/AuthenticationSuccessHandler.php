<?php

namespace App\Security;

use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationSuccessResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as ContractsEventDispatcherInterface;

class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private $jwtManager;
    private $dispatcher;
    private $em;

    public function __construct(
        JWTTokenManagerInterface $jwtManager,
        EventDispatcherInterface $dispatcher,
        EntityManagerInterface $em
    ) {
        $this->jwtManager = $jwtManager;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        return $this->handleAuthenticationSuccess($token->getUser());
    }

    public function handleAuthenticationSuccess(UserInterface $user, $jwt = null)
    {
        if (null === $jwt) {
            $jwt = $this->jwtManager->create($user);
        }

        $response = new JWTAuthenticationSuccessResponse($jwt);
        $event = new AuthenticationSuccessEvent(['token' => $jwt], $user, $response);

        if ($this->dispatcher instanceof ContractsEventDispatcherInterface) {
            $this->dispatcher->dispatch($event, Events::AUTHENTICATION_SUCCESS);
        } else {
            $this->dispatcher->dispatch($event, Events::AUTHENTICATION_SUCCESS);
        }

        /* @phpstan-ignore-next-line */
        $user->setLastConnection(new \DateTime());

        $this->em->flush();

        $response->setData($event->getData());

        return $response;
    }
}
