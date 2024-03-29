<?php

namespace Angle\OneLoginAzureSamlBundle\Security\Authentication\Provider;

use Angle\OneLoginAzureSamlBundle\Event\UserCreatedEvent;
use Angle\OneLoginAzureSamlBundle\Event\UserModifiedEvent;
use Angle\OneLoginAzureSamlBundle\Security\Authentication\Token\SamlTokenFactoryInterface;
use Angle\OneLoginAzureSamlBundle\Security\Authentication\Token\SamlTokenInterface;
use Angle\OneLoginAzureSamlBundle\Security\User\SamlUserFactoryInterface;
use Angle\OneLoginAzureSamlBundle\Security\User\SamlUserInterface;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @deprecated since 2.1
 */
class SamlProvider implements AuthenticationProviderInterface
{
    protected $userProvider;
    protected $userFactory;
    protected $tokenFactory;
    protected $eventDispatcher;

    public function __construct(UserProviderInterface $userProvider, ?EventDispatcherInterface $eventDispatcher)
    {
        $this->userProvider = $userProvider;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function setUserFactory(SamlUserFactoryInterface $userFactory)
    {
        $this->userFactory = $userFactory;
    }

    public function setTokenFactory(SamlTokenFactoryInterface $tokenFactory)
    {
        $this->tokenFactory = $tokenFactory;
    }

    public function authenticate(TokenInterface $token)
    {
        $user = $this->retrieveUser($token);

        if ($user) {
            if ($user instanceof SamlUserInterface) {
                $user->setSamlAttributes($token->getAttributes());
                if ($this->eventDispatcher) {
                    $this->eventDispatcher->dispatch(new UserModifiedEvent($user));
                }
            }

            $authenticatedToken = $this->tokenFactory->createToken($user, $token->getAttributes(), $user->getRoles());
            $authenticatedToken->setAuthenticated(true);

            return $authenticatedToken;
        }

        throw new AuthenticationException('The authentication failed.');
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof SamlTokenInterface;
    }

    protected function retrieveUser($token)
    {
        try {
            return $this->userProvider->loadUserByIdentifier($token->getUserIdentifier());
        } catch (UserNotFoundException $e) {
            if ($this->userFactory instanceof SamlUserFactoryInterface) {
                return $this->generateUser($token);
            }

            throw $e;
        }
    }

    protected function generateUser($token)
    {
        $user = $this->userFactory->createUser($token);
        if ($this->eventDispatcher) {
            $this->eventDispatcher->dispatch(new UserCreatedEvent($user));
        }

        return $user;
    }
}
