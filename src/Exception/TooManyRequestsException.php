<?php

namespace Karaden\Exception;

class TooManyRequestsException extends KaradenException
{
    const STATUS_CODE = 429;
}
