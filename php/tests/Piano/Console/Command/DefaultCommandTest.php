<?php

namespace Piano\Tests\Console\Command;

use Piano\Console\Command\DefaultCommand;
use Piano\Datasets;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class DefaultCommandTest extends TestCase
{
    public function testExecute()
    {
        // Arrange
        $command = $this->getMockBuilder('Piano\Console\Command\DefaultCommand')
            ->setMethods(['getDatasets'])
            ->getMock();

        $datasets = $this->createMock(Datasets::class);
        $datasets->method('merge')
             ->will($this->returnValue(['header' => ['user_id', 'email', 'first_name'], 'records' => [['asdasdas', 'a@b.c', 'Test']]]));

        $command
            ->method('getDatasets')
            ->will($this->returnValue($datasets));

        $commandTester = new CommandTester($command);

        // Act
        $commandTester->execute(['files' => ['file_a.csv', 'file_b.csv']]);

        $output = $commandTester->getDisplay();

        // Assert
        $this->assertSame("user_id,email,first_name\nasdasdas,a@b.c,Test\n", $output);
    }
}
