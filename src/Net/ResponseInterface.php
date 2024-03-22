<?php

namespace Karaden\Net;

interface ResponseInterface
{
    function getError();
    function getObject();
    function getStatusCode();
    function getHeaders();
    function isError(): bool;
}
