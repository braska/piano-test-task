<?php

namespace Piano\Tests\Console\Command;

use Piano\Console\Command\RunCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class RunCommandTest extends TestCase
{
    public function testExecute()
    {
        // Arrange
        $application = new Application();
        $application->add(new RunCommand());

        $command = $application->find('run');
        $commandTester = new CommandTester($command);

        // Act
        $commandTester->execute([
            'command'  => $command->getName(),
        ]);

        $output = $commandTester->getDisplay();

        // Assert
        $this->assertStringContainsString('Hello World', $output);
    }
}
