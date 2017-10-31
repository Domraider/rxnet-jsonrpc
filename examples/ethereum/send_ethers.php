<?php
require_once __DIR__ . '/../../vendor/autoload.php';

// you must export EHTEREUM_PWD as en variable prior running this script

// use a geth node for example
$server = "http://localhost:8545";

$cli = new \Rxnet\JsonRpc\JsonRpc($server, [], new \Rxnet\JsonRpc\Mappers\EthereumMapper());

const FROM = "0x635128f079377bf95f4b679e232ae0e2507c1a42";
const TO = "0xd44d259015b61a5fe5027221239d840d92583adb";
const AMOUNT = 0.1 * 10 ** 18;

$cli->call(
    "personal_unlockAccount",
    [
        FROM,
        getenv("EHTEREUM_PWD"),
    ]
)
    ->flatMap(function ($unlocked) use ($cli) {
        if (!$unlocked) {
            throw new \Exception("Failed to unlock");
        }
        return $cli->call(
            "eth_estimateGas",
            [
                [
                    "from" => FROM,
                    "to" => TO,
                    "value" => "0x" . dechex(AMOUNT),
                ]
            ]
        );
    })
    ->flatMap(function ($gaz) use ($cli) {
        return $cli->call(
            "eth_sendTransaction",
            [
                [
                    "from" => FROM,
                    "to" => TO,
                    "gaz" => "0x" . dechex($gaz * 10 ** 18),
                    "value" => "0x" . dechex(AMOUNT),
                ]
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