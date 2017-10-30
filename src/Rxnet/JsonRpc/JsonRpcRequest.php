<?php
namespace Rxnet\JsonRpc;

class JsonRpcRequest
{
    protected $method;
    protected $params;
    protected $id;

    /**
     * JsonRpcRequest constructor.
     * @param $method
     * @param array $params
     * @param $id
     */
    public function __construct($method, array $params, $id)
    {
        $this->method = $method;
        $this->params = $params;
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'method' => $this->method,
            'params' => $this->params,
            'id' => $this->id,
        ];
    }

    /**
     * @return string
     */
    public function toJson()
    {
        return \GuzzleHttp\json_encode($this->toArray());
    }
}
