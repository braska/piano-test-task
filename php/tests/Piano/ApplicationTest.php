<?php
use PHPUnit\Framework\TestCase;
use Piano\Application;

class ApplicationTest extends TestCase
{
    public function testRun()
    {
        // Arrange
        $expectedOutput = 'Hello World';

        // Act
        ob_start();
        $application = new Application();
        $application->run();
        $actualOutput = ob_get_clean();

        // Assert
        $this->assertSame($actualOutput, $expectedOutput);
    }
}
