<?php

namespace Karaden\Param\Message\Bulk;

use PHPUnit\Framework\TestCase;

class BulkMessageDownloadParamsBuilderTest extends TestCase
{
    protected function setUp(): void
    {
    }

    /**
     * @test
     */
    public function idを入力できる()
    {
        $expected = '72fe94ec-9c7d-9634-8226-e3136bd6cf7a';
        $params = (new BulkMessageDownloadParamsBuilder())
            ->withId($expected)
            ->build();

        $this->assertEquals($expected, $params->id);
    }

    /**
     * @test
     */
    public function directoryPathを入力できる()
    {
        $expected = 'path';
        $params = (new BulkMessageDownloadParamsBuilder())
            ->withDirectoryPath($expected)
            ->build();

        $this->assertEquals($expected, $params->directoryPath);
    }

    /**
     * @test
     */
    public function maxRetriesを入力できる()
    {
        $expected = 1;
        $params = (new BulkMessageDownloadParamsBuilder())
            ->withMaxRetries($expected)
            ->build();

        $this->assertEquals($expected, $params->maxRetries);
    }

    /**
     * @test
     */
    public function retryIntervalを入力できる()
    {
        $expected = 10;
        $params = (new BulkMessageDownloadParamsBuilder())
            ->withRetryInterval($expected)
            ->build();

        $this->assertEquals($expected, $params->retryInterval);
    }
}
