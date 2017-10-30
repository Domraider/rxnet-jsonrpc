<?php
describe('Json Rpc response', function() {
    beforeEach(function() {
        $this->request = new \Rxnet\JsonRpc\JsonRpcRequest('the_method_name', [], 'the_id');
    });

    context('Parse Http response', function () {
        it('Throw if http code is not correct', function() {
            expect(function () {
                new \Rxnet\JsonRpc\JsonRpcResponse(
                    $this->request,
                    new \GuzzleHttp\Psr7\Response(500)
                );
            })->to->throw(\Exception::class, "Internal Server Error");
        });
        it('Throw if content is malformed json', function() {
            expect(function () {
                new \Rxnet\JsonRpc\JsonRpcResponse(
                    $this->request,
                    new \GuzzleHttp\Psr7\Response(200, [], 'not_json')
                );
            })->to->throw(\Exception::class, "Invalid response format [0:json_decode error: Syntax error]");
        });
        it('Throw if not response id', function() {
            expect(function () {
                new \Rxnet\JsonRpc\JsonRpcResponse(
                    $this->request,
                    new \GuzzleHttp\Psr7\Response(200, [], '{}')
                );
            })->to->throw(\Exception::class, "Incorrect json response : no id field");
        });
        it('Throw if response id is not request id', function() {
            expect(function () {
                new \Rxnet\JsonRpc\JsonRpcResponse(
                    $this->request,
                    new \GuzzleHttp\Psr7\Response(200, [], '{"id":"another_id"}')
                );
            })->to->throw(\Exception::class, "Incorrect json response : id inconstancy");
        });
        it('Throw if error', function() {
            expect(function () {
                new \Rxnet\JsonRpc\JsonRpcResponse(
                    $this->request,
                    new \GuzzleHttp\Psr7\Response(200, [], '{"id":"the_id","error":{"message":"foo","code":123}}')
                );
            })->to->throw(\Exception::class, "foo");
        });
        it('Throw no result field', function() {
            expect(function () {
                new \Rxnet\JsonRpc\JsonRpcResponse(
                    $this->request,
                    new \GuzzleHttp\Psr7\Response(200, [], '{"id":"the_id"}')
                );
            })->to->throw(\Exception::class, "Incorrect json response : no result field");
        });
        it('Return formatted response', function() {
            expect(function () use (&$result) {
                $result = new \Rxnet\JsonRpc\JsonRpcResponse(
                    $this->request,
                    new \GuzzleHttp\Psr7\Response(200, [], '{"id":"the_id","result":"foobar","jsonrpc":"2.0"}')
                );
            })->to->not->throw(\Exception::class);

            expect($result)->instanceof(\Rxnet\JsonRpc\JsonRpcResponse::class);
            expect($result->getResult())->to->equal('foobar');
            expect($result->getId())->to->equal('the_id');
            expect($result->getVersion())->to->equal('2.0');
        });
    });
});