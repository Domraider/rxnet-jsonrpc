<?php
require_once __DIR__ . '/../../vendor/autoload.php';

// use a geth node for example
$server = "http://localhost:8545";

$cli = new \Rxnet\JsonRpc\JsonRpc($server, [], new \Rxnet\JsonRpc\Mappers\EthereumMapper());

$cli->call("eth_coinbase")
    ->flatMap(function ($coinbase) use ($cli) {
        return $cli->call(
            "eth_getBalance",
            [
                $coinbase,
                "latest"
            ]
        );
    })
    ->subscribeCallback(
    function ($response) {
        echo "Got response : $response\n";
    },
    function (\Exception $e) {
        echo "Error : {$e->getMessage()}\n";
    },
    function () {
        echo "Terminated\n";
    }
);