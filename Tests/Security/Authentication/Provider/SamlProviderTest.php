<?php

namespace Angle\OneLoginAzureSamlBundle\Tests\Security\Authentication\Provider;

use Doctrine\ORM\EntityManagerInterface;
use Angle\OneLoginAzureSamlBundle\Event\UserCreatedEvent;
use Angle\OneLoginAzureSamlBundle\Event\UserModifiedEvent;
use Angle\OneLoginAzureSamlBundle\EventListener\User\UserCreatedListener;
use Angle\OneLoginAzureSamlBundle\EventListener\User\UserModifiedListener;
use Angle\OneLoginAzureSamlBundle\Security\Authentication\Provider\SamlProvider;
use Angle\OneLoginAzureSamlBundle\Security\Authentication\Token\SamlToken;
use Angle\OneLoginAzureSamlBundle\Security\Authentication\Token\SamlTokenFactory;
use Angle\OneLoginAzureSamlBundle\Security\User\SamlUserFactoryInterface;
use Angle\OneLoginAzureSamlBundle\Security\User\SamlUserInterface;
use Angle\OneLoginAzureSamlBundle\Security\User\SamlUserProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class SamlProviderTest extends TestCase
{
    public function testSupports(): void
    {
        $provider = $this->getProvider();

        self::assertTrue($provider->supports($this->createMock(SamlToken::class)));
        self::assertFalse($provider->supports($this->createMock(TokenInterface::class)));
    }

    public function testAuthenticate(): void
    {
        $user = $this->createMock(UserInterface::class);
        $user
            ->expects(self::once())
            ->method('getRoles')
            ->willReturn([])
        ;

        $provider = $this->getProvider($user);
        $token = $provider->authenticate($this->getSamlToken());

        self::assertInstanceOf(SamlToken::class, $token);
        self::assertEquals(['foo' => 'bar'], $token->getAttributes());
        self::assertEquals([], $token->getRoleNames());
        self::assertTrue($token->isAuthenticated());
        self::assertSame($user, $token->getUser());
    }

    public function testAuthenticateInvalidUser(): void
    {
        $this->expectException(UserNotFoundException::class);
        $provider = $this->getProvider();
        $provider->authenticate($this->getSamlToken());
    }

    public function testAuthenticateWithUserFactory(): void
    {
        $user = $this->createMock(UserInterface::class);
        $user
            ->expects(self::once())
            ->method('getRoles')
            ->willReturn([])
        ;

        $userFactory = $this->createMock(SamlUserFactoryInterface::class);
        $userFactory
            ->expects(self::once())
            ->method('createUser')
            ->willReturn($user)
        ;

        $provider = $this->getProvider(null, $userFactory);
        $token = $provider->authenticate($this->getSamlToken());

        self::assertInstanceOf(SamlToken::class, $token);
        self::assertEquals(['foo' => 'bar'], $token->getAttributes());
        self::assertEquals([], $token->getRoleNames());
        self::assertTrue($token->isAuthenticated());
        self::assertSame($user, $token->getUser());
    }

    public function testSamlAttributesInjection(): void
    {
        $user = $this->createMock(SamlUserInterface::class);
        $user
            ->expects(self::once())
            ->method('getRoles')
            ->willReturn([])
        ;
        $user
            ->expects(self::once())
            ->method('setSamlAttributes')
            ->with(self::equalTo(['foo' => 'bar']))
        ;

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->expects(self::once())
            ->method('persist')
            ->with(self::equalTo($user))
        ;
        $entityManager
            ->expects(self::once())
            ->method('flush')
        ;

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(UserModifiedEvent::class, new UserModifiedListener($entityManager, true));

        $provider = $this->getProvider($user, null, $eventDispatcher);
        $provider->authenticate($this->getSamlToken());
    }

    public function testPersistUser(): void
    {
        $user = $this->createMock(UserInterface::class);
        $user
            ->expects(self::once())
            ->method('getRoles')
            ->willReturn([])
        ;

        $userFactory = $this->createMock(SamlUserFactoryInterface::class);
        $userFactory
            ->expects(self::once())
            ->method('createUser')
            ->willReturn($user)
        ;

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->expects(self::once())
            ->method('persist')
            ->with(self::equalTo($user))
        ;
        $entityManager
            ->expects(self::once())
            ->method('flush')
        ;

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(UserCreatedEvent::class, new UserCreatedListener($entityManager, true));

        $provider = $this->getProvider(null, $userFactory, $eventDispatcher);
        $provider->authenticate($this->getSamlToken());

    }

    protected function getSamlToken(): SamlToken
    {
        $token = $this->createMock(SamlToken::class);
        $token
            ->expects(self::once())
            ->method('getUserIdentifier')
            ->willReturn('admin')
        ;
        $token
            ->method('getAttributes')
            ->willReturn(['foo' => 'bar'])
        ;

        return $token;
    }

    protected function getProvider($user = null, $userFactory = null, EventDispatcherInterface $eventDispatcher = null): SamlProvider
    {
        $userProvider = $this->createMock(SamlUserProvider::class);
        if ($user) {
            $userProvider
                ->method('loadUserByIdentifier')
                ->willReturn($user)
            ;
        } else {
            $userProvider
                ->method('loadUserByIdentifier')
                ->will(self::throwException(new UserNotFoundException()))
            ;
        }

        $provider = new SamlProvider($userProvider, $eventDispatcher);
        $provider->setTokenFactory(new SamlTokenFactory());

        if ($userFactory) {
            $provider->setUserFactory($userFactory);
        }

        return $provider;
    }
}
