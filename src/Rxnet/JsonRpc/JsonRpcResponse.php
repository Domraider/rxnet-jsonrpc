<?php
namespace Rxnet\JsonRpc;

use GuzzleHttp\Psr7\Response;

class JsonRpcResponse
{
    protected $request;
    protected $id;
    protected $result;
    protected $version;

    /**
     * @return JsonRpcRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * JsonRpcResponse constructor.
     * @param JsonRpcRequest $request
     * @param Response $response
     * @throws \Exception
     */
    public function __construct(JsonRpcRequest $request, Response $response)
    {
        $this->request = $request;

        if ($response->getStatusCode() >= 300) {
            throw new \Exception($response->getReasonPhrase(), $response->getStatusCode());
        }

        try {
            $data = \GuzzleHttp\json_decode((string)$response->getBody(), true);
        } catch (\InvalidArgumentException $e) {
            throw new \Exception("Invalid response format [{$e->getCode()}:{$e->getMessage()}]");
        }

        if (!isset($data['id'])) {
            throw new \Exception("Incorrect json response : no id field");
        }
        $this->id = $data['id'];

        if ($this->id !== $request->getId()) {
            throw new \Exception("Incorrect json response : id inconstancy");
        }

        if (isset($data['error']) && $data['error']) {
            $message = isset($data['error']['message']) ? $data['error']['message'] : 'unknown';
            $code = isset($data['error']['code']) ? $data['error']['code'] : 'unknown';

            throw new \Exception($message, $code);
        }

        if (!isset($data['result'])) {
            throw new \Exception("Incorrect json response : no result field");
        }
        $this->result = $data['result'];

        if (isset($data['jsonrpc'])) {
            $this->version = $data['jsonrpc'];
        }
    }
}
