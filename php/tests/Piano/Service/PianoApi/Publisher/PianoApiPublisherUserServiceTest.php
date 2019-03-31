<?php

namespace Piano\Tests\Service\PianoApi\Publisher;

use Piano\Service\PianoApi\Publisher\PianoApiPublisherUserService;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\Response;

class PianoApiPublisherUserServiceTest extends TestCase
{
    public function testSearch()
    {
        // Arrange
        $expected_body = ['code' => 0, 'total' => 1, 'users' => [['uid' => 'abcde123']]];

        $client = $this->getMockBuilder('Piano\Service\PianoApi\PianoApiClient')
            ->disableOriginalConstructor()
            ->setMethods(['request'])
            ->getMock();
        $client
            ->expects($this->once())
            ->method('request')
            ->will($this->returnValue(new Response(200, [], json_encode($expected_body))));

        $service = $this->getMockBuilder('Piano\Service\PianoApi\Publisher\PianoApiPublisherUserService')
            ->setMethods(['getClient'])
            ->getMock();
        $service
            ->expects($this->once())
            ->method('getClient')
            ->will($this->returnValue($client));

        // Act
        $result = $service->search(['email' => 'test@example.com']);

        // Assert
        $this->assertEquals($expected_body, $result);
    }
}
