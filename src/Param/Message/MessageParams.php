<?php

namespace Karaden\Param\Message;


abstract class MessageParams
{
    const CONTEXT_PATH = '/messages';

    function validate(): MessageParams { return $this; }
}
