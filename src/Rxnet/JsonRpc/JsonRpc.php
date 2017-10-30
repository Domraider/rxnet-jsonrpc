<?php
namespace Rxnet\JsonRpc;

use GuzzleHttp\Psr7\Response;
use Ramsey\Uuid\Uuid;
use Rxnet\Http\Http;
use Rxnet\JsonRpc\Mappers\AbstractJsonRpcMapper;

class JsonRpc
{
    protected $defaultHeaders = [
        "User-Agent" => "RxnetJsonRpc/1.0",
        "Accept" => "*/*",
        "Content-Type" => "application/json",
    ];

    /** @var  Http */
    protected $client;

    protected $server;
    protected $headers;

    protected $defaultMapper;

    public function __construct($server, array $headers = [], AbstractJsonRpcMapper $defaultMapper = null)
    {
        if (!filter_var($server, FILTER_VALIDATE_URL)) {
            throw new \Exception("Unexpected server address");
        }

        $this->server = $server;
        $this->headers = array_merge(
            $this->defaultHeaders,
            []
        );

        $this->defaultMapper = $defaultMapper;

        $this->client = new Http();
    }

    public function call($method, array $params = [], $id = null)
    {
        if (null === $id) {
            $id = Uuid::uuid4()->toString();
        }

        $request = new JsonRpcRequest($method, $params, $id);

        return $this->client->post(
            $this->server,
            [
                'headers' => $this->headers,
                'json' => $request->toArray(),
            ]
        )
            ->map(function (Response $response) use ($request) {
                return new JsonRpcResponse($request, $response);
            })
            ->map(function (JsonRpcResponse $response) {
                if (null === $this->defaultMapper) {
                    return $response;
                }

                return $this->defaultMapper->map($response);
            });
    }
}
