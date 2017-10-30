<?php

namespace Rxnet\JsonRpc\Exceptions;

class UnexpectedServerAdressException extends RxnetJsonRpcException
{
    public function __construct($message = "Unexpected server address", $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
