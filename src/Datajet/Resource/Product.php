<?php

namespace Dafiti\Datajet\Resource;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Respect\Validation\Validator as v;

class Product extends AbstractResource
{
    public function __construct(Client $client, array $config = [])
    {
        $this->validateConfig($config);

        parent::__construct($client, $config);

        $this->uriSearch = "{$this->config['search']['uri']}/2.0/";
        $this->uriImport = "{$this->config['data']['uri']}/2.0/";
    }

    private function validateConfig(array $config)
    {
        if (empty($config)) {
            throw new \InvalidArgumentException('Empty config is not allowed');
        }

        $validator = v::arrayVal()->notEmpty()
            ->key(
                'search',
                v::arrayVal()->notEmpty()
                    ->key(
                        'uri',
                        v::url()->notEmpty()
                    )
                    ->key(
                        'key',
                        v::alnum()->notEmpty()
                    )
            )
            ->key(
                'data',
                v::arrayVal()->notEmpty()
                    ->key(
                        'uri',
                        v::url()->notEmpty()
                    )
                    ->key(
                        'key',
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
        $response = $this->client->post("{$this->uriImport}product/", [
            'json' => $data,
            'query' => [
                'key' => $this->config['data']['key'],
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

        $response = $this->client->post("{$this->uriSearch}search/", [
            'json' => $data,
            'query' => [
                'key' => $this->config['search']['key'],
            ],
        ]);

        $response = json_decode($response->getBody(), true);

        return $response;
    }

    /**
     * Product Delete.
     *
     * @param string $id
     *
     * @return bool
     */
    public function delete($id)
    {
        if (empty($id)) {
           throw new \InvalidArgumentException('ID Product cannot be empty');
        }
	
        try {
            $response = $this->client->delete("{$this->uriImport}product/{$id}", [
                'query' => [
                    'key' => $this->config['data']['key'],
                ],
            ]);
        } catch (ClientException $e) {
            return false;
        }

        $response = json_decode($response->getBody(), true);

        if (isset($response['affected']) && $response['affected'] > 0) {
            return true;
        }

        return false;
    }
}
