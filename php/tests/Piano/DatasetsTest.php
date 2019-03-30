<?php

namespace Piano\Tests;

use Piano\Datasets;

use PHPUnit\Framework\TestCase;

class DatasetsTest extends TestCase
{
    public function testMerge()
    {
        // Arrange
        $datasets = new Datasets(
            [
                ['file' => 'file_a.csv', 'header' => ['user_id', 'email'], 'records' => ['asdasdas', 'a@b.c']],
                ['file' => 'file_b.csv', 'header' => ['user_id', 'first_name'], 'records' => ['asdasdas', 'Test']],
            ]
        );

        // Act
        $result = $datasets->merge('user_id');

        // Assert
        $this->assertEquals(['user_id', 'email', 'first_name'], $result['header']);
    }
}
