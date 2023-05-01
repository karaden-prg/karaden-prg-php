<?php

namespace Karaden;

use Karaden\Exception\InvalidParamsException;
use Karaden\Param\Message\MessageCancelParams;
use PHPUnit\Framework\TestCase;

class MessageCancelParamsTest extends TestCase
{
    protected function setUp(): void
    {
    }

    /**
     * @test
     */
    public function 正しいパスを生成できる()
    {
        $id = 'id';
        $params = new MessageCancelParams($id);

        $this->assertEquals(MessageCancelParams::CONTEXT_PATH . "/{$id}/cancel", $params->toPath());
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
            $builder = MessageCancelParams::newBuilder();
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
