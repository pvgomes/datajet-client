<?php

namespace Dafiti\Datajet\Resource;

use GuzzleHttp\Client;

abstract class AbstractResource
{
    /**
     * @var GuzzleHttp\Client;
     */
    protected $client;

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var string
     */
    protected $uri;

    /**
     * Construct with Guzzle Http Client.
     *
     * @param \GuzzleHttp\Client $httpClient Guzzle Http Client
     */
    public function __construct(Client $client, array $config = [])
    {
        $this->client = $client;
        $this->config = $config;
    }
}
