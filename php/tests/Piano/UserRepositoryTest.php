<?php

namespace Piano\Tests;

use Piano\UserRepository;

use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{
    public function testUpdateUid()
    {
        // Arrange
        $expected_uid = '123';

        $user = ['user_id' => 'asdasdas', 'email' => 'a@b.c', 'first_name' => 'Test'];

        $user_repository = $this->getMockBuilder('Piano\UserRepository')
            ->disableOriginalConstructor()
            ->setMethods(['findByEmail'])
            ->getMock();
        $user_repository
            ->expects($this->once())
            ->method('findByEmail')
            ->will($this->returnValue(['uid' => $expected_uid]));

        // Act
        $user_repository->updateUid($user);

        // Assert
        $this->assertEquals($expected_uid, $user['user_id']);
    }

    public function testFindByEmail()
    {
        // Arrange
        $expected_user = ['uid' => 'abcde123'];
        $service = $this->getMockBuilder('Piano\Service\PianoApi\Publisher\PianoApiPublisherUserService')
            ->setMethods(['search'])
            ->getMock();
        $service
            ->expects($this->once())
            ->method('search')
            ->will($this->returnValue(['code' => 0, 'total' => 1, 'users' => [$expected_user]]));

        $user_repository = new UserRepository($service);

        // Act
        $user = $user_repository->findByEmail('test@example.com');

        // Assert
        $this->assertEquals($expected_user, $user);
    }
}
