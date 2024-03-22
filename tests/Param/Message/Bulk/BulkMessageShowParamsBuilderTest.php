<?php

namespace Karaden\Param\Message\Bulk;

use PHPUnit\Framework\TestCase;

class BulkMessageShowParamsBuilderTest extends TestCase
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
        $params = (new BulkMessageShowParamsBuilder())
            ->withId($expected)
            ->build();

        $this->assertEquals($expected, $params->id);
    }
}
