<?php

namespace Karaden\Service\BulkMessage;

use GuzzleHttp\Psr7\Response;
use Http\Mock\Client;
use Karaden\Config;
use Karaden\Exception\BulkMessageCreateFailedException;
use Karaden\Exception\BulkMessageListMessageRetryLimitExceedException;
use Karaden\Exception\BulkMessageShowRetryLimitExceedException;
use Karaden\Exception\FileNotFoundException;
use Karaden\Exception\FileDownloadFailedException;
use Karaden\Param\Message\Bulk\BulkMessageDownloadParams;
use Karaden\TestHelper;
use PHPUnit\Framework\TestCase;

class BulkMessageServiceTest extends TestCase
{
    protected function setUp(): void
    {
        $client = new Client();
        $bulkFileResponse = [
            'id' => '741121d7-3f7e-ed85-9fac-28d87835528e',
            'object' => 'bulk_file',
            'url' => 'https://example.com',
            'created_at'=> '2023-12-01T15:00:00.0Z',
            'expires_at'=> '2023-12-01T15:00:00.0Z',
        ];
        $bulkMessageResponse = [
            'id' => 'ef931182-80ff-611c-c878-871a08bb5a6a',
            'object' => 'bulk_message',
            'status' => 'processing',
            'created_at'=> '2023-12-01T15:00:00.0Z',
            'updated_at'=> '2023-12-01T15:00:00.0Z',
        ];
        $client->addResponse(new Response(200, [], json_encode($bulkFileResponse)));
        $client->addResponse(new Response(200, [], ''));
        $client->addResponse(new Response(200, [], json_encode($bulkMessageResponse)));
        Config::$httpClient = $client;
    }

    protected function tearDown(): void
    {
        Config::$httpClient = null;
    }

    /**
     * @test
     */
    public function bulkMessageオブジェクトが返る()
    {
        $file = tmpfile();
        $filename = stream_get_meta_data($file)['uri'];
        $requestOptions = TestHelper::getDefaultRequestOptionsBuilder()->build();
        $bulkMessage = BulkMessageService::create($filename, $requestOptions);
        $this->assertEquals('bulk_message', $bulkMessage->getObject());
    }

    /**
     * @test
     */
    public function ファイルが存在しない場合はエラー()
    {
        $filename = 'test.csv';
        $requestOptions = TestHelper::getDefaultRequestOptionsBuilder()->build();

        $this->expectException(FileNotFoundException::class);
        BulkMessageService::create($filename, $requestOptions);
    }

    /**
     * @test
     */
    public function ファイルがダウンロードできる()
    {
        $tmpDir = sys_get_temp_dir() . '/test';
        if(!file_exists($tmpDir)) {
            mkdir($tmpDir);
        }
        $bulkMessageResponse = [
            'id' => 'ef931182-80ff-611c-c878-871a08bb5a6a',
            'object' => 'bulk_message',
            'status' => 'done',
            'created_at'=> '2023-12-01T15:00:00.0Z',
            'updated_at'=> '2023-12-01T15:00:00.0Z',
        ];
        $filename = 'file.csv';
        $fileContent = 'file content';
        $contentDisposition ="attachment;filename=\"" . $filename . "\";filename*=UTF-8''" . $filename;
        $client = new Client();
        $client->addResponse(new Response(200, [], json_encode($bulkMessageResponse)));
        $client->addResponse(new Response(302, ['Location' => 'http://example.com/' . uniqid()]));
        $client->addResponse(new Response(200, ['content-disposition' => $contentDisposition], $fileContent));
        Config::$httpClient = $client;

        $params = BulkMessageDownloadParams::newBuilder()
            ->withId('c439f89c-1ea3-7073-7021-1f127a850437')
            ->withDirectoryPath($tmpDir)
            ->withMaxRetries(1)
            ->withRetryInterval(10)
            ->build();
        $requestOptions = TestHelper::getDefaultRequestOptionsBuilder()
            ->build();

        BulkMessageService::download($params, $requestOptions);
        $this->assertTrue(file_exists($tmpDir . '/' . $filename));
        $this->assertEquals(file_get_contents($tmpDir . '/' . $filename), $fileContent);
        unlink($tmpDir . '/' . $filename);
        rmdir($tmpDir);
    }

    /**
     * @test
     */
    public function bulkMessageのstatusがdone以外でリトライ回数を超過した場合はエラー()
    {
        $tmpDir = sys_get_temp_dir() . '/test';
        if(!file_exists($tmpDir)) {
            mkdir($tmpDir);
        }

        $client = new Client();
        $bulkMessageResponse = [
            'id' => 'ef931182-80ff-611c-c878-871a08bb5a6a',
            'object' => 'bulk_message',
            'status' => 'processing',
            'created_at'=> '2023-12-01T15:00:00.0Z',
            'updated_at'=> '2023-12-01T15:00:00.0Z',
        ];
        $client->addResponse(new Response(200, [], json_encode($bulkMessageResponse)));
        $client->addResponse(new Response(200, [], json_encode($bulkMessageResponse)));
        Config::$httpClient = $client;

        $params = BulkMessageDownloadParams::newBuilder()
            ->withId('c439f89c-1ea3-7073-7021-1f127a850437')
            ->withDirectoryPath($tmpDir)
            ->withMaxRetries(1)
            ->withRetryInterval(10)
            ->build();
        $requestOptions = TestHelper::getDefaultRequestOptionsBuilder()
            ->build();

        $this->expectException(BulkMessageShowRetryLimitExceedException::class);
        BulkMessageService::download($params, $requestOptions);
        rmdir($tmpDir);
    }

    /**
     * @test
     */
    public function 結果取得APIが202を返しリトライ回数を超過した場合はエラー()
    {
        $tmpDir = sys_get_temp_dir() . '/test';
        if(!file_exists($tmpDir)) {
            mkdir($tmpDir);
        }

        $client = new Client();
        $bulkMessageResponse = [
            'id' => 'ef931182-80ff-611c-c878-871a08bb5a6a',
            'object' => 'bulk_message',
            'status' => 'done',
            'created_at'=> '2023-12-01T15:00:00.0Z',
            'updated_at'=> '2023-12-01T15:00:00.0Z',
        ];
        $client->addResponse(new Response(200, [], json_encode($bulkMessageResponse)));
        $client->addResponse(new Response(202, []));
        $client->addResponse(new Response(202, []));
        Config::$httpClient = $client;

        $params = BulkMessageDownloadParams::newBuilder()
            ->withId('c439f89c-1ea3-7073-7021-1f127a850437')
            ->withDirectoryPath($tmpDir)
            ->withMaxRetries(1)
            ->withRetryInterval(10)
            ->build();
        $requestOptions = TestHelper::getDefaultRequestOptionsBuilder()
            ->build();

        $this->expectException(BulkMessageListMessageRetryLimitExceedException::class);
        BulkMessageService::download($params, $requestOptions);
        rmdir($tmpDir);
    }

    /**
     * @test
     */
    public function bulkMessageのstatusがerrorはエラー()
    {
        $tmpDir = sys_get_temp_dir() . '/test';
        if(!file_exists($tmpDir)) {
            mkdir($tmpDir);
        }

        $client = new Client();
        $bulkMessageResponse = [
            'id' => 'ef931182-80ff-611c-c878-871a08bb5a6a',
            'object' => 'bulk_message',
            'status' => 'error',
            'created_at'=> '2023-12-01T15:00:00.0Z',
            'updated_at'=> '2023-12-01T15:00:00.0Z',
        ];
        $client->addResponse(new Response(200, [], json_encode($bulkMessageResponse)));
        Config::$httpClient = $client;

        $params = BulkMessageDownloadParams::newBuilder()
            ->withId('c439f89c-1ea3-7073-7021-1f127a850437')
            ->withDirectoryPath($tmpDir)
            ->withMaxRetries(1)
            ->withRetryInterval(10)
            ->build();
        $requestOptions = TestHelper::getDefaultRequestOptionsBuilder()
            ->build();

        $this->expectException(BulkMessageCreateFailedException::class);
        BulkMessageService::download($params, $requestOptions);
        rmdir($tmpDir);
    }

    /**
     * @test
     */
    public function ファイルダウンロード処理にエラーが発生した場合は例外が飛ぶ()
    {
        $tmpDir = sys_get_temp_dir() . '/test';
        if(!file_exists($tmpDir)) {
            mkdir($tmpDir);
        }
        $bulkMessageResponse = [
            'id' => 'ef931182-80ff-611c-c878-871a08bb5a6a',
            'object' => 'bulk_message',
            'status' => 'done',
            'created_at'=> '2023-12-01T15:00:00.0Z',
            'updated_at'=> '2023-12-01T15:00:00.0Z',
        ];
        $fileContent = 'file content';
        $client = new Client();
        $client->addResponse(new Response(200, [], json_encode($bulkMessageResponse)));
        $client->addResponse(new Response(302, ['Location' => ''])); // Locationが空
        $client->addResponse(new Response(200, [], $fileContent));
        Config::$httpClient = $client;

        $params = BulkMessageDownloadParams::newBuilder()
            ->withId('c439f89c-1ea3-7073-7021-1f127a850437')
            ->withDirectoryPath($tmpDir)
            ->withMaxRetries(1)
            ->withRetryInterval(10)
            ->build();
        $requestOptions = TestHelper::getDefaultRequestOptionsBuilder()
            ->build();

        $this->expectException(FileDownloadFailedException::class);
        BulkMessageService::download($params, $requestOptions);

        $this->assertTrue(file_exists($tmpDir . '/file.csv'));
        $this->assertEquals(file_get_contents($tmpDir . '/file.csv'), $fileContent);
    }
}
