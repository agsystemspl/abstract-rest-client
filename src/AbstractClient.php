<?php

namespace AGSystems\REST;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

abstract class AbstractClient
{
    protected $query = [];

    public function __get($name)
    {
        $name = $this->pathHandler($name);
        $this->query[] = $name;
        return $this;
    }

    public function __call($name, $arguments)
    {
        switch ($name) {
            case 'get':
            case 'post':
            case 'put':
            case 'file':
                $uri = implode('/', array_filter($this->query));
                $this->query = [];
                return $this->request($name, $uri, array_shift($arguments));
        }

        $this->query[] = $name;
        $this->query = array_merge($this->query, $arguments);
        return $this;
    }

    protected function withOptions()
    {
        return [];
    }

    protected function pathHandler($path)
    {
        return $path;
    }

    protected function responseHandler(callable $callback)
    {
        /**
         * @var $response Response
         */
        $response = call_user_func($callback);
        return $response->getBody()->getContents();
    }

    protected function postHandler($data)
    {
        return [
            'json' => $data,
        ];
    }

    protected function putHandler($data)
    {
        return [
            'json' => $data,
        ];
    }

    protected function request($method, $uri, $data = null)
    {
        $options = [
            'http_errors' => false,
        ];

        switch (strtoupper($method)) {
            case 'GET':
            case 'DELETE':
                $options += [
                    'query' => $data,
                ];
                break;
            case 'POST':
                $options += $this->postHandler($data);
                break;
            case 'PUT':
                $options += $this->putHandler($data);
                break;
            case 'FILE':
                $options += [
                    'file' => $data,
                ];
                break;
        }

        $options = array_merge_recursive($options, $this->withOptions());

        $callback = function () use ($method, $uri, $options) {
            $client = new Client($options);
            return $client->request($method, $uri);
        };

        return $this->responseHandler($callback);
    }
}
