<?php

namespace Karaden;

use Karaden\Exception\InvalidParamsException;
use Karaden\Param\Message\MessageDetailParams;
use PHPUnit\Framework\TestCase;

class MessageDetailParamsTest extends TestCase
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
        $params = new MessageDetailParams($id);

        $this->assertEquals(MessageDetailParams::CONTEXT_PATH . "/{$id}", $params->toPath());
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
            $builder = MessageDetailParams::newBuilder();
            if ($id) {
                $builder->withId($id);
            }
            $builder->build()
                ->validate();
        } catch (InvalidParamsException $e) {
            $messages = $e->error->getErrors()->getProperty('id');
            $this->assertIsArray($messages);
            throw $e;
        }
    }
}
