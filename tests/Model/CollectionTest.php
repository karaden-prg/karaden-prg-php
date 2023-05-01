<?php

namespace Karaden\Model;

use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    protected function setUp(): void
    {
    }

    /**
     * @test
     */
    public function dataを出力できる()
    {
        $value = [];
        $collection = new Collection();
        $collection->setProperty('data', $value);

        $this->assertIsArray($collection->getData());
    }

    /**
     * @test
     */
    public function hasMoreを出力できる()
    {
        $value = true;
        $collection = new Collection();
        $collection->setProperty('has_more', $value);

        $this->assertTrue($collection->hasMore());
    }
}
 