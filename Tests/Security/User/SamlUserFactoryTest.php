<?php

namespace Angle\OneLoginAzureSamlBundle\Tests\Security\User;

use Angle\OneLoginAzureSamlBundle\Security\Authentication\Token\SamlToken;
use Angle\OneLoginAzureSamlBundle\Security\User\SamlUserFactory;
use Angle\OneLoginAzureSamlBundle\Tests\TestUser;
use PHPUnit\Framework\TestCase;

class SamlUserFactoryTest extends TestCase
{
    public function testUserMapping(): void
    {
        $map = [
            'password' => 'notused',
            'email' => '$mail',
            'name' => '$cn',
            'lastname' => '$sn',
            'roles' => ['ROLE_USER']
        ];

        $token = $this->createMock(SamlToken::class);
        $token->method('getUserIdentifier')->willReturn('admin');
        $token->method('getAttributes')->willReturn([
            'mail' => ['email@mail.com'],
            'cn' => ['testname'],
            'sn' => ['testlastname']
        ]);

        $factory = new SamlUserFactory(TestUser::class, $map);
        $user = $factory->createUser($token);

        self::assertEquals('admin', $user->getUserIdentifier());
        self::assertEquals('email@mail.com', $user->getEmail());
        self::assertEquals('testname', $user->getName());
        self::assertEquals('testlastname', $user->getLastname());
        self::assertEquals('notused', $user->getPassword());
        self::assertEquals(['ROLE_USER'], $user->getRoles());
    }

}
