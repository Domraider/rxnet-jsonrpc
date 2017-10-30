# rxnet-jsonrpc

JSON RPC bundle for rxnet.

[![License](https://poser.pugx.org/domraider/rxnet/license)](https://packagist.org/packages/domraider/rxnet)

## TESTS
```bash
vendor
```

## EXAMPLES
### simple example
```php
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
```

run it from :
```bash
php examples/simple.php
```

### ethereum examples

API doc is here : https://github.com/ethereum/wiki/wiki/JSON-RPC#json-rpc-methods

#### get coinbase balance
```php
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
```

run it from :
```bash
php examples/geth/get_coinbase_balance.php
```