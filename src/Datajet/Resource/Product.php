<?php

namespace Dafiti\Datajet\Resource;

use GuzzleHttp\Client;
use Respect\Validation\Validator as v;

class Product extends AbstractResource
{
    public function __construct(Client $client, array $config = [])
    {
        $this->validateConfig($config);

        parent::__construct($client, $config);
    }

    private function validateConfig(array $config)
    {
        if (empty($config)) {
            throw new \InvalidArgumentException('Empty config is not allowed');
        }

        $validator = v::arrayVal()->notEmpty()
            ->key(
                'hawk',
                v::arrayVal()->notEmpty()
                    ->key(
                        'search_key',
                        v::alnum()->notEmpty()
                    )
                    ->key(
                        'import_key',
                        v::alnum()->notEmpty()
                    )
            );

        $validator->assert($config);
    }

    /**
     * Atomic product import.
     *
     * @param array $data A list of product to import
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return bool
     */
    public function import(array $data)
    {
        $response = $this->client->post('product/', [
            'json' => $data,
            'query' => [
                'key' => $this->config['hawk']['import_key'],
            ],
        ]);

        $response = json_decode($response->getBody(), true);

        if (isset($response['affected']) && $response['affected'] > 0) {
            return true;
        }

        return false;
    }

    /**
     * Product Search.
     *
     * @param array $data Search parameters
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return array
     */
    public function search(array $data)
    {
        if (!isset($data['size'])) {
            $data['size'] = 10;
        }

        $response = $this->client->post('search/', [
            'json' => $data,
            'query' => [
                'key' => $this->config['hawk']['search_key'],
            ],
        ]);

        $response = json_decode($response->getBody());

        return $response;
    }
}
