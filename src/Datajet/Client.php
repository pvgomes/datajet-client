<?php

namespace Dafiti\Datajet;

use Dafiti\Datajet\Exception\ResourceNotFound;
use GuzzleHttp\Client as HttpClient;

/**
 * Http client thats provide a simple way to use Datajet service in PHP.
 */
class Client
{
    const API_URL = 'http://hawk.euw.datajet.io/1.1/';

    /**
     * @var array
     */
    private $resources = [];

    /**
     * @var GuzzleHttp\Client
     */
    private $httpClient;

    /**
     * @var array
     */
    private $config = [];

    /**
     * Client accept a Guzzle Http Client into constructor param.
     *
     * @param \GuzzleHttp\Client $httpClient Guzzle Http client instance
     */
    public function __construct(HttpClient $httpClient, array $config = [])
    {
        $this->httpClient = $httpClient;
        $this->config = $config;
    }

    /**
     * Get return instance.
     *
     * @param string $resource Resource name
     *
     * @throws Dafiti\Datajet\Exception\ResourceNotFound
     *
     * @return Dafiti\Datajet\Resource\AbstractResource
     */
    public function __get($resource)
    {
        if (isset($this->resources[$resource])) {
            return $this->resources[$resource];
        }

        $class = '\\Dafiti\\Datajet\\Resource\\'.ucfirst($resource);

        if (!class_exists($class)) {
            throw new ResourceNotFound($resource);
        }

        $this->resources[$resource] = new $class($this->httpClient, $this->config);

        return $this->resources[$resource];
    }

    /**
     * Create a instance of Client with defined config.
     *
     * @param array $config Datajet request config
     *
     * @return \Dafiti\Datajet\Client
     */
    public static function create(array $config = [])
    {
        $httpConfig = [
            'base_uri' => self::API_URL,
            'defaults' => [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ],
        ];

        $httpClient = new HttpClient($httpConfig);

        return new self($httpClient, $config);
    }
}
