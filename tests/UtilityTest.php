<?php

namespace Karaden;

use Karaden\Model\KaradenObject;
use Karaden\Model\Message;
use Karaden\RequestOptions;
use PHPUnit\Framework\TestCase;

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
}
 