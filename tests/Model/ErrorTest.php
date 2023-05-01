<?php

namespace Karaden\Model;

use PHPUnit\Framework\TestCase;

class ErrorTest extends TestCase
{
    protected function setUp(): void
    {
    }

    /**
     * @test
     */
    public function codeを出力できる()
    {
        $value = 'code';
        $error = new Error();
        $error->setProperty('code', $value);

        $this->assertEquals($value, $error->getCode());
    }

    /**
     * @test
     */
    public function messageを出力できる()
    {
        $value = 'message';
        $error = new Error();
        $error->setProperty('message', $value);

        $this->assertEquals($value, $error->getMessage());
    }

    /**
     * @test
     */
    public function errorsを出力できる()
    {
        $value = new KaradenObject();
        $error = new Error();
        $error->setProperty('errors', $value);

        $this->assertInstanceOf(KaradenObject::class, $error->getErrors());
    }
}
 