<?php
namespace Rxnet\JsonRpc\Mappers;

use Rxnet\JsonRpc\JsonRpcResponse;

class EthereumMapper extends AbstractJsonRpcMapper
{
    /**
     * @param JsonRpcResponse $response
     * @return mixed
     */
    public function map(JsonRpcResponse $response)
    {
        $methodName = $response->getRequest()->getMethod();
        $methodName = "parse" . ucwords(preg_replace_callback(
            '#_([a-z])#',
            function ($matches) {
                return strtoupper($matches[1]);
            },
            $methodName
        ));

        if (method_exists($this, $methodName)) {
            return call_user_func([$this, $methodName], $response);
        }

        // return result only (go fast !)
        return $response->getResult();
    }

    /**
     * @param JsonRpcResponse $response
     * @return float|int
     */
    public function parseEthGetBalance(JsonRpcResponse $response)
    {
        $result = $response->getResult();

        // parse to human readable
        return hexdec($result) / 10**18;
    }

    public function parseEthEstimateGas(JsonRpcResponse $response)
    {
        $result = $response->getResult();

        // parse to human readable
        return hexdec($result) / 10**18;
    }
}
