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
                ['file' => 'file_a.csv', 'header' => ['user_id', 'email'], 'records' => [['user_id' => 'asdasdas', 'email' => 'a@b.c']]],
                ['file' => 'file_b.csv', 'header' => ['user_id', 'first_name'], 'records' => [['user_id' => 'asdasdas', 'first_name' => 'Test']]],
            ]
        );

        // Act
        $result = $datasets->merge('user_id');

        // Assert
        $this->assertEquals(['user_id', 'email', 'first_name'], $result['header']);
        $this->assertEquals([['user_id' => 'asdasdas', 'email' => 'a@b.c', 'first_name' => 'Test']], $result['records']);
    }
}
