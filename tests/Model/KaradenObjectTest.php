<?php

namespace Karaden\Model;

use PHPUnit\Framework\TestCase;

class KaradenObjectTest extends TestCase
{
    protected function setUp(): void
    {
    }

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

    public function idValueProvider()
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

    /**
     * @test
     * @dataProvider primitiveValueProvider
     */
    public function プロパティに入出力できる($expected)
    {
        $key = 'test';
        $object = new KaradenObject();
        $object->setProperty($key, $expected);

        $this->assertEquals($expected, $object->getProperty($key));
    }

    /**
     * @test
     */
    public function プロパティのキーを列挙できる()
    {
        $expected = ['test1', 'test2'];
        $object = new KaradenObject();
        foreach($expected as $value) {
            $object->setProperty($value, $value);
        }

        $keys = $object->getPropertyKeys();
        $this->assertIsArray($keys);
        foreach($expected as $value) {
            $this->assertTrue(in_array($value, $keys));
        }
    }

    /**
     * @test
     * @dataProvider idValueProvider
     */
    public function idを出力できる($expected)
    {
        $object = new KaradenObject();
        $object->setProperty('id', $expected);

        $this->assertEquals($expected, $object->getId());
    }

    /**
     * @test
     */
    public function objectを出力できる()
    {
        $expected = 'test';
        $object = new KaradenObject();
        $object->setProperty('object', $expected);

        $this->assertEquals($expected, $object->getObject());
    }
}
 