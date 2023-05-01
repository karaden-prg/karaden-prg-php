<?php

namespace Karaden;

use Karaden\Param\Message\MessageCancelParamsBuilder;
use PHPUnit\Framework\TestCase;

class MessageCancelParamsBuilderTest extends TestCase
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
        $params = (new MessageCancelParamsBuilder())
            ->withId($expected)
            ->build();

        $this->assertEquals($expected, $params->id);
    }
}
