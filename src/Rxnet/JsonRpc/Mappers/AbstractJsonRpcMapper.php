<?php
namespace Rxnet\JsonRpc\Mappers;

use Rxnet\JsonRpc\JsonRpcResponse;

abstract class AbstractJsonRpcMapper
{
    /**
     * @param JsonRpcResponse $response
     * @return mixed
     */
    abstract public function map(JsonRpcResponse $response);
}
