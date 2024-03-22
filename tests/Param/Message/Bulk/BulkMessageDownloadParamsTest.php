<?php

namespace Karaden\Param\Message\Bulk;

use Karaden\Exception\InvalidParamsException;
use PHPUnit\Framework\TestCase;

class BulkMessageDownloadParamsTest extends TestCase
{
    protected function setUp(): void
    {
    }

    public function idProvider()
    {
        return [
            [''],
            [null],
        ];
    }

    /**
     * @test
     * @dataProvider idProvider
     */
    public function idが空文字や未指定はエラー($id)
    {
        $this->expectException(InvalidParamsException::class);
        try {
            $builder = BulkMessageDownloadParams::newBuilder();
            if ($id) {
                $builder->withId($id);
            }
            $builder->build()
                ->validate();
        } catch(InvalidParamsException $e) {
            $messages = $e->error->getErrors()->getProperty('id');
            $this->assertIsArray($messages);
            throw $e;
        }
    }

    /**
     * @test
     */
    public function directoryPathが存在しない値の場合はエラー()
    {
        $this->expectException(InvalidParamsException::class);
        try {
            BulkMessageDownloadParams::newBuilder()
                ->withDirectoryPath('invalid')
                ->build()
                ->validate();
        } catch(InvalidParamsException $e) {
            $messages = $e->error->getErrors()->getProperty('directoryPath');
            $this->assertIsArray($messages);
            throw $e;
        }
    }

    /**
     * @test
     */
    public function directoryPathがファイルを指定している場合はエラー()
    {
        $tmpFileName = tempnam(sys_get_temp_dir(), "test_");
        $this->expectException(InvalidParamsException::class);
        try {
            BulkMessageDownloadParams::newBuilder()
                ->withDirectoryPath($tmpFileName)
                ->build()
                ->validate();
        } catch(InvalidParamsException $e) {
            $messages = $e->error->getErrors()->getProperty('directoryPath');
            $this->assertIsArray($messages);
            throw $e;
        } finally {
            unlink($tmpFileName);
        }
    }

    /**
     * @test
     */
    public function 指定されたdirectoryPathに読み取り権限がない場合はエラー()
    {
        $tmpDir = sys_get_temp_dir() . '/test';
        if(!file_exists($tmpDir)) {
            mkdir($tmpDir);
        }
        chmod($tmpDir, 0377);
        $this->expectException(InvalidParamsException::class);
        try {
            BulkMessageDownloadParams::newBuilder()
                ->withDirectoryPath($tmpDir)
                ->build()
                ->validate();
        } catch(InvalidParamsException $e) {
            $messages = $e->error->getErrors()->getProperty('directoryPath');
            $this->assertIsArray($messages);
            throw $e;
        } finally {
            chmod($tmpDir, 0777);
            rmdir($tmpDir);
        }
    }

    /**
     * @test
     */
    public function 指定されたdirectoryPathに書き込み権限がない場合はエラー()
    {
        $tmpDir = sys_get_temp_dir() . '/test';
        if(!file_exists($tmpDir)) {
            mkdir($tmpDir);
        }
        chmod($tmpDir, 0577);
        $this->expectException(InvalidParamsException::class);
        try {
            BulkMessageDownloadParams::newBuilder()
                ->withDirectoryPath($tmpDir)
                ->build()
                ->validate();
        } catch(InvalidParamsException $e) {
            $messages = $e->error->getErrors()->getProperty('directoryPath');
            $this->assertIsArray($messages);
            throw $e;
        } finally {
            chmod($tmpDir, 0777);
            rmdir($tmpDir);
        }
    }

    public function maxRetriesProvider()
    {
        return [
            [0],
            [6],
            [-1],
        ];
    }

    /**
     * @test
     * @dataProvider maxRetriesProvider
     */
    public function maxRetriesが0以下または6以上はエラー($maxRetries)
    {
        $this->expectException(InvalidParamsException::class);
        try {
            BulkMessageDownloadParams::newBuilder()
                ->withMaxRetries($maxRetries)
                ->build()
                ->validate();
        } catch(InvalidParamsException $e) {
            $messages = $e->error->getErrors()->getProperty('maxRetries');
            $this->assertIsArray($messages);
            throw $e;
        }
    }

    public function retryIntervalProvider()
    {
        return [
            [9],
            [61],
            [-1],
        ];
    }

    /**
     * @test
     * @dataProvider retryIntervalProvider
     */
    public function retryIntervalが9以下または61以上はエラー($retryInterval)
    {
        $this->expectException(InvalidParamsException::class);
        try {
            BulkMessageDownloadParams::newBuilder()
                ->withRetryInterval($retryInterval)
                ->build()
                ->validate();
        } catch(InvalidParamsException $e) {
            $messages = $e->error->getErrors()->getProperty('retryInterval');
            $this->assertIsArray($messages);
            throw $e;
        }
    }
}
