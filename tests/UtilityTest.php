<?php

namespace Karaden;

use Exception;
use GuzzleHttp\Psr7\Response;
use Http\Message\RequestMatcher;
use Http\Mock\Client;
use Karaden\Exception\FileUploadFailedException;
use Karaden\Model\KaradenObject;
use Karaden\Model\Message;
use Karaden\RequestOptions;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class UtilityTest extends TestCase
{
    public function primitiveValueProvider()
    {
        return [
            ['string'],
            [''],
            [123],
            [0],
            [true],
            [false],
            [null],
        ];
    }

    public function arrayObjectItemProvider()
    {
        return [
            [[], KaradenObject::class,],
            [['object' => null,], KaradenObject::class, ],
            [['object' => 'test',], KaradenObject::class, ],
            [['object' => 'message',], Message::class, ],
        ];
    }

    protected function setUp(): void
    {
    }

    protected function tearDown(): void
    {
        Config::$httpClient = null;
    }

    /**
     * @test
     */
    public function objectのフィールドが存在しない場合はKaradenObjectが返る()
    {
        $contents = json_decode('{"test": "test"}');
        $requestOptions = new RequestOptions();

        $object = Utility::convertToKaradenObject($contents, $requestOptions);

        $this->assertInstanceOf(KaradenObject::class, $object);
    }

    /**
     * @test
     */
    public function objectのフィールドが存在してObjectTypesのマッピングが存在する場合はオブジェクトが返る()
    {
        $contents = json_decode('{"object": "message"}');
        $requestOptions = new RequestOptions();

        $object = Utility::convertToKaradenObject($contents, $requestOptions);

        $this->assertInstanceOf(Message::class, $object);
    }

    /**
     * @test
     */
    public function objectのフィールドが存在してObjectTypesのマッピングに存在しない場合はKaradenObjectが返る()
    {
        $contents = json_decode('{"object": "test"}');
        $requestOptions = new RequestOptions();

        $object = Utility::convertToKaradenObject($contents, $requestOptions);

        $this->assertInstanceOf(KaradenObject::class, $object);
    }

    /**
     * @test
     * @dataProvider primitiveValueProvider
     */
    public function プリミティブな値はデシリアライズしても変わらない($value)
    {
        $key = 'test';
        $contents = json_decode(json_encode([
            $key => $value,
        ]));
        $requestOptions = new RequestOptions();

        $object = Utility::convertToKaradenObject($contents, $requestOptions);

        $this->assertInstanceOf(KaradenObject::class, $object);
        $this->assertEquals($value, $object->getProperty($key));
    }

    /**
     * @test
     * @dataProvider primitiveValueProvider
     */
    public function プリミティブな値の配列の要素はデシリアライズしても変わらない($value)
    {
        $contents = json_decode(json_encode([
            'test' => [$value,],
        ]));
        $requestOptions = new RequestOptions();

        $object = Utility::convertToKaradenObject($contents, $requestOptions);

        $this->assertInstanceOf(KaradenObject::class, $object);
        $this->assertIsArray($object->getProperty('test'));
        $this->assertEquals($value, $object->getProperty('test')[0]);
    }

    /**
     * @test
     * @dataProvider arrayObjectItemProvider
     */
    public function 配列の配列もサポートする()
    {
        $value = 'test';
        $contents = json_decode(sprintf('{"test": [["%s"]]}', $value));
        $requestOptions = new RequestOptions();

        $object = Utility::convertToKaradenObject($contents, $requestOptions);

        $this->assertInstanceOf(KaradenObject::class, $object);
        $this->assertIsArray($object->getProperty('test'));
        $this->assertCount(1, $object->getProperty('test'));
        $this->assertIsArray($object->getProperty('test')[0]);
        $this->assertEquals($value, $object->getProperty('test')[0][0]);
    }

    /**
     * @test
     * @dataProvider arrayObjectItemProvider
     */
    public function オブジェクトの配列の要素はデシリアライズするとKaradenObjectに変換される($item, $class)
    {
        $item['test'] = 'test';
        $contents = json_decode(json_encode([
            'test' => [
                $item,
            ],
        ]));
        $requestOptions = new RequestOptions();

        $object = Utility::convertToKaradenObject($contents, $requestOptions);

        $this->assertInstanceOf(KaradenObject::class, $object);
        $this->assertIsArray($object->getProperty('test'));
        $this->assertInstanceOf($class, $object->getProperty('test')[0]);
        $this->assertEquals($item['test'], $object->getProperty('test')[0]->getProperty('test'));
    }

    /**
     * @test
     */
    public function 指定のURLにfileパスのファイルをPUTメソッドでリクエストする()
    {
        $client = new Client();
        $requestMatcher = new class implements RequestMatcher
        {
            public function matches(RequestInterface $request): bool
            {
                return true;
            }
        };

        $file = tmpfile();
        $filename = stream_get_meta_data($file)['uri'];
        $signedUrl = 'https://example.com/';

        $client->on($requestMatcher, function (RequestInterface $request) use ($signedUrl, $filename){
            $this->assertEquals('PUT', $request->getMethod());
            $this->assertEquals($signedUrl, $request->getUri());
            $this->assertEquals($filename, $request->getBody()->getMetadata()['uri']);
            $this->assertEquals('application/octet-stream', $request->getHeader('Content-Type')[0]);

            return new Response(200, [], '');
        });

        Config::$httpClient = $client;
        Utility::putSignedUrl($signedUrl, $filename);
    }

    /**
     * @test
     */
    public function レスポンスコードが200以外だとFileUploadFailedExceptionが返る()
    {
        $client = new Client();
        $requestMatcher = new class implements RequestMatcher
        {
            public function matches(RequestInterface $request): bool
            {
                return true;
            }
        };

        $file = tmpfile();
        $filename = stream_get_meta_data($file)['uri'];
        $signedUrl = 'https://example.com/';

        $client->on($requestMatcher, fn () => new Response(403, [], ''));

        Config::$httpClient = $client;
        $this->expectException(FileUploadFailedException::class);
        Utility::putSignedUrl($signedUrl, $filename);
    }

    /**
     * @test
     */
    public function 例外が発生するとFileUploadFailedExceptionをリスローする()
    {
        $client = new Client();
        $client->addException(new Exception());

        $file = tmpfile();
        $filename = stream_get_meta_data($file)['uri'];
        $signedUrl = 'https://example.com/';

        Config::$httpClient = $client;
        $this->expectException(FileUploadFailedException::class);
        Utility::putSignedUrl($signedUrl, $filename);
    }

    /**
     * @test
     */
    public function ContentTypeを指定できる()
    {
        $client = new Client();
        $requestMatcher = new class implements RequestMatcher
        {
            public function matches(RequestInterface $request): bool
            {
                return true;
            }
        };

        $file = tmpfile();
        $filename = stream_get_meta_data($file)['uri'];
        $signedUrl = 'https://example.com/';
        $contentType = 'text/csv';

        $client->on($requestMatcher, function (RequestInterface $request) use ($signedUrl, $filename, $contentType){
            $this->assertEquals('PUT', $request->getMethod());
            $this->assertEquals($signedUrl, $request->getUri());
            $this->assertEquals($filename, $request->getBody()->getMetadata()['uri']);
            $this->assertEquals($contentType, $request->getHeader('Content-Type')[0]);

            return new Response(200, [], '');
        });

        Config::$httpClient = $client;
        Utility::putSignedUrl($signedUrl, $filename, $contentType);
    }
}
