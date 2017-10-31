<?php
require_once __DIR__ . '/../../vendor/autoload.php';

// you must export EHTEREUM_PWD as en variable prior running this script

// use a geth node for example
$server = "http://localhost:8545";

$cli = new \Rxnet\JsonRpc\JsonRpc($server, [], new \Rxnet\JsonRpc\Mappers\EthereumMapper());

$cli->call("eth_coinbase")
    ->flatMap(function ($coinbase) use ($cli) {
        return $cli->call(
            "personal_unlockAccount",
            [
                $coinbase,
                getenv("EHTEREUM_PWD"),
            ]
        )->map(function ($unlocked) {
            if (!$unlocked) {
                throw new \Exception("Failed to unlock");
            };

            return $unlocked;
        });
    })
    ->subscribeCallback(
    function ($response) {
        echo "Unlock succeed\n";
    },
    function (\Exception $e) {
        echo "Error : {$e->getMessage()}\n";
    },
    function () {
        echo "Terminated\n";
    }
);
