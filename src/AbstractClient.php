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
                return $this->request($name, $uri, array_shift($arguments), array_shift($arguments));
        }

        $this->query[] = $name;
        $this->query = array_merge($this->query, $arguments);
        return $this;
    }

    public function withOptions(array $options = [])
    {
        $this->options = $options;
    }

    protected function clientOptions()
    {
        return [];
    }

    protected function handleGet($data = [])
    {
        return [
            'query' => $data,
        ];
    }

    protected function handlePost($data = [])
    {
        return [
            'json' => $data
        ];
    }

    protected function handlePut($data = [])
    {
        return [
            'json' => $data
        ];
    }

    protected function handleDelete($data = [])
    {
        return [
            'query' => $data
        ];
    }

    protected function handleFile($data = [])
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

    protected function request($method, $uri, $data = [], $requestOptions = [])
    {
        $options = $this->clientOptions();

        if (is_null($data))
            $data = [];

        if (is_null($requestOptions))
            $requestOptions = [];

        $handler = 'handle' . ucfirst(strtolower($method));

        if (method_exists($this, $handler))
            $options = array_replace_recursive(
                $options,
                call_user_func([$this, $handler], $data)
            );

        $options = array_replace_recursive(
            $options,
            $this->options,
            $requestOptions
        );

        $callback = function () use ($method, $uri, $options) {
            $client = new Client(
                $options
            );

            return $client->request($method, $uri);
        };

        return $this->handleResponse($callback);
    }
}
