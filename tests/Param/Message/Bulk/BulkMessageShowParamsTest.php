<?php

namespace Karaden\Param\Message\Bulk;

use Karaden\Exception\InvalidParamsException;
use PHPUnit\Framework\TestCase;

class BulkMessageShowParamsTest extends TestCase
{
    protected function setUp(): void
    {
    }

    /**
     * @test
     */
    public function 正しいパスを生成できる()
    {
        $id = '72fe94ec-9c7d-9634-8226-e3136bd6cf7a';
        $params = new BulkMessageShowParams($id);

        $this->assertEquals(BulkMessageShowParams::CONTEXT_PATH . "/{$id}", $params->toPath());
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
            $builder = BulkMessageShowParams::newBuilder();
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
}
