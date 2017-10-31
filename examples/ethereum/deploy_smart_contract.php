<?php
require_once __DIR__ . '/../../vendor/autoload.php';

// you must export EHTEREUM_PWD as en variable prior running this script

// use a geth node for example
$server = "http://localhost:8545";

if ($argc != 2) {
    echo "please send smart contract binaries as single parameter";
}

$bin = $argv[1];
if (substr($bin, 0, 2) != "0x") {
    $bin = "0x" . $bin;
}

$cli = new \Rxnet\JsonRpc\JsonRpc($server, [], new \Rxnet\JsonRpc\Mappers\EthereumMapper());

$cli->call("eth_coinbase")
    ->flatMap(function ($coinbase) use ($cli, $bin) {
        return $cli->call(
            "personal_unlockAccount",
            [
                $coinbase,
                getenv("EHTEREUM_PWD"),
            ]
        )->map(function ($unlocked) use ($coinbase) {
            if (!$unlocked) {
                throw new \Exception("Failed to unlock");
            };

            return $coinbase;
        });
    })
    ->flatMap(function ($coinbase) use ($cli, $bin) {
        return $cli->call(
            "eth_estimateGas",
            [
                [
                    "from" => $coinbase,
                    "data" => $bin,
                ]
            ]
        )->map(function ($gaz) use ($coinbase, $bin) {
            return [
                'from' => $coinbase,
                'gaz' => "0x" . dechex($gaz * 10 ** 18),
                'bin' => $bin,
            ];
        });
    })
    ->flatMap(function ($data) use ($cli, $bin) {
        return $cli->call(
            "eth_sendTransaction",
            [
                $data,
            ]
        );
    })
    ->subscribeCallback(
        function ($response) {
            echo "Contract deployed at : $response\n";
        },
        function (\Exception $e) {
            echo "Error : {$e->getMessage()}\n";
        },
        function () {
            echo "Terminated\n";
        }
    );
