<?php

namespace Karaden\Net;

interface ResponseInterface
{
    function getError();
    function getObject();
    function isError(): bool;
}
