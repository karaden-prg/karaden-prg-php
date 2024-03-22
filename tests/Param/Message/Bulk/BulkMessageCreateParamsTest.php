<?php

namespace Karaden\Param\Message\Bulk;

use Karaden\Exception\InvalidParamsException;
use PHPUnit\Framework\TestCase;

class BulkMessageCreateParamsTest extends TestCase
{
    protected function setUp(): void
    {
    }

    /**
     * @test
     */
    public function 正しいパスを生成できる()
    {
        $bulkFileId = '72fe94ec-9c7d-9634-8226-e3136bd6cf7a';
        $params = new BulkMessageCreateParams($bulkFileId);

        $this->assertEquals(BulkMessageCreateParams::CONTEXT_PATH, $params->toPath());
    }

    /**
     * @test
     */
    public function bulkFileIdを送信データにできる()
    {
        $expected = '72fe94ec-9c7d-9634-8226-e3136bd6cf7a';
        $params = new BulkMessageCreateParams($expected);

        $actual = $params->toData();
        $this->assertEquals($expected, $actual['bulk_file_id']);
    }

    public function bulkFileIdProvider()
    {
        return [
            [''],
            [null],
        ];
    }

    /**
     * @test
     * @dataProvider bulkFileIdProvider
     */
    public function bulkFileIdが空文字や未指定はエラー($bulkFileId)
    {
        $this->expectException(InvalidParamsException::class);
        try {
            $builder = BulkMessageCreateParams::newBuilder();
            if ($bulkFileId) {
                $builder->withBulkFileId($bulkFileId);
            }
            $builder->build()
                ->validate();
        } catch(InvalidParamsException $e) {
            $messages = $e->error->getErrors()->getProperty('bulkFileId');
            $this->assertIsArray($messages);
            throw $e;
        }
    }
}
