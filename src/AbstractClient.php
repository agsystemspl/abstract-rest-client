<?php

namespace AGSystems\REST;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

abstract class AbstractClient
{
    protected $query = [];
    protected $options = [];

    public function __get($name)
    {
        $name = $this->handlePath($name);
        $this->query[] = $name;
        return $this;
    }

    public function __call($name, $arguments)
    {
        switch ($name) {
            case 'get':
            case 'post':
            case 'put':
            case 'delete':
            case 'file':
                $uri = implode('/', array_filter($this->query));
                $this->query = [];
                return $this->request($name, $uri, array_shift($arguments));
        }

        $this->query[] = $name;
        $this->query = array_merge($this->query, $arguments);
        return $this;
    }

    protected function clientOptions()
    {
        return [];
    }

    protected function handleGet($data = null)
    {
        return [
            'query' => $data,
        ];
    }

    protected function handlePost($data = null)
    {
        return [
            'json' => $data
        ];
    }

    protected function handlePut($data = null)
    {
        return [
            'json' => $data
        ];
    }

    protected function handleDelete($data = null)
    {
        return [
            'query' => $data
        ];
    }

    protected function handleFile($data = null)
    {
        return [
            'file' => $data
        ];
    }

    protected function handlePath($path)
    {
        return $path;
    }

    protected function handleResponse(callable $callback)
    {
        /**
         * @var $response Response
         */
        $response = call_user_func($callback);
        return $response->getBody()->getContents();
    }

    protected function request($method, $uri, $data = null)
    {
        $options = [];

        $handler = 'handle' . ucfirst(strtolower($method));

        if (method_exists($this, $handler))
            $options = $this->$handler($data);

        $callback = function () use ($method, $uri, $options) {
            $client = new Client(
                $this->clientOptions()
            );

            return $client->request($method, $uri, $options);
        };

        return $this->handleResponse($callback);
    }
}
