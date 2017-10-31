<?php
describe('Ethereum mapper', function() {
    beforeEach(function() {
        $this->mapper = new \Rxnet\JsonRpc\Mappers\EthereumMapper();

        $this->prophet = new \Prophecy\Prophet();

        $this->response = $this->prophet->prophesize(\Rxnet\JsonRpc\JsonRpcResponse::class);
    });
    afterEach(function () {
        $this->prophet->checkPredictions();
    });

    it('Ethereum mapper by default return the response result fields', function() {
        $this->response->getRequest()
            ->willReturn(new \Rxnet\JsonRpc\JsonRpcRequest('the_method_name', [], 'the_id'));
        $this->response->getResult()
            ->willReturn('foo');

        $result = $this->mapper->map($this->response->reveal());

        expect($result)->to->equal('foo');
    });

    it('eth_getBalance returns human readable result (amount in eth)', function() {
        $this->response->getRequest()
            ->willReturn(new \Rxnet\JsonRpc\JsonRpcRequest('eth_getBalance', [], 'the_id'));
        $this->response->getResult()
            ->willReturn('0x3cdd90e39f8c9e600');

        $result = $this->mapper->map($this->response->reveal());

        expect($result)->to->equal(70.173134711);
    });
    it('eth_estimateGas returns human readable result (amount in eth)', function() {
        $this->response->getRequest()
            ->willReturn(new \Rxnet\JsonRpc\JsonRpcRequest('eth_estimateGas', [], 'the_id'));
        $this->response->getResult()
            ->willReturn('0x3cdd90e39f8c9e600');

        $result = $this->mapper->map($this->response->reveal());

        expect($result)->to->equal(70.173134711);
    });
});