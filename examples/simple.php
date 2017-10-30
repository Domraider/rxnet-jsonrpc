<?php
require_once __DIR__ . '/../vendor/autoload.php';

// fake the server using rxnet httpd
$server = "http://localhost:8080";
$httpd = new \Rxnet\Httpd\Httpd();
$httpd->route('POST', '/', function(\Rxnet\Httpd\HttpdRequest $request, \Rxnet\Httpd\HttpdResponse $response) {
    $json = $request->getJson();
    $response->json([
        "jsonrpc" => "2.0",
        "id" => $json['id'],
        "result" => "Wow ! Such Json ! Very Rpc !"
    ]);
});
$httpd->listen(8080);

// demo starts here
$cli = new \Rxnet\JsonRpc\JsonRpc($server);

$cli->call("test")
    ->subscribeCallback(
    function (\Rxnet\JsonRpc\JsonRpcResponse $response) {
        echo "Got response : {$response->getResult()}\n";
    },
    function (\Exception $e) {
        echo "Error : {$e->getMessage()}\n";
    },
    function () {
        echo "Terminated\n";
    }
);