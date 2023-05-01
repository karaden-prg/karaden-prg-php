<?php

namespace Karaden;

use Karaden\Param\Message\MessageDetailParamsBuilder;
use PHPUnit\Framework\TestCase;

class MessageDetailParamsBuilderTest extends TestCase
{
    protected function setUp(): void
    {
    }

    /**
     * @test
     */
    public function idを入力できる()
    {
        $expected = 'id';
        $params = (new MessageDetailParamsBuilder())
            ->withId($expected)
            ->build();

        $this->assertEquals($expected, $params->id);
    }
}
