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
            ->setMethods(['getDatasets', 'updateUids'])
            ->getMock();

        $datasets = $this->createMock(Datasets::class);
        $datasets
            ->expects($this->once())
            ->method('merge')
            ->will($this->returnValue(['header' => ['user_id', 'email', 'first_name'], 'records' => [['someid', 'a@b.c', 'Test']]]));

        $command
            ->expects($this->once())
            ->method('getDatasets')
            ->will($this->returnValue($datasets));

        $command
            ->expects($this->once())
            ->method('updateUids');

        $commandTester = new CommandTester($command);

        // Act
        $commandTester->execute(['files' => ['file_a.csv', 'file_b.csv']]);

        $output = $commandTester->getDisplay();

        // Assert
        $this->assertSame("user_id,email,first_name\nsomeid,a@b.c,Test\n", $output);
    }
}
