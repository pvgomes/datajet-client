<?php

namespace Dafiti\Datajet\Resource;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class ProductTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    private $config = [];

    /**
     * @var array
     */
    private $headers = [
        'Content-Type' => 'application/json; charset=utf-8',
        'Vary'         => 'Accept-Encoding'
    ];

    public function setUp()
    {
        $this->config = [
            'hawk' => [
                'import_key' => 'a',
                'search_key' => 'b'
            ]
        ];

        parent::setUp();
    }

    public function tearDown()
    {
        $this->config = [];
    }

    /**
     * @covers \Dafiti\Datajet\Resource\Product::__construct
     * @covers \Dafiti\Datajet\Resource\Product::validateConfig
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Empty config is not allowed
     */
    public function testShouldThrowExceptionWhenSetEmptyConfigIntoConstructor()
    {
        $client = $this->getMock('\GuzzleHttp\Client');

        new Product($client);
    }

    /**
     * @covers \Dafiti\Datajet\Resource\Product::__construct
     * @covers \Dafiti\Datajet\Resource\Product::validateConfig
     *
     * @expectedException \InvalidArgumentException
     */
    public function testShouldThrowExceptionWhenConfigKeysIsNotDefined()
    {
        $client = $this->getMock('\GuzzleHttp\Client');

        new Product($client, ['some' => 'thing']);
    }

    /**
     * @covers \Dafiti\Datajet\Resource\Product
     * @covers \Dafiti\Datajet\Resource\AbstractResource
     */
    public function testShouldInstanceOfAbstractResource()
    {
        $httpClient = $this->getMock('\GuzzleHttp\Client');
        $resource   = new Product($httpClient, $this->config);

        $this->assertInstanceOf('\Dafiti\Datajet\Resource\AbstractResource', $resource);
    }

    /**
     * @covers \Dafiti\Datajet\Resource\Product::import
     *
     * @expectedException \GuzzleHttp\Exception\ClientException
     */
    public function testShouldRetrieveErrorWhenImportProductWithoutRequiredParams()
    {
        $body = json_encode([
            'affected' => 0,
            'message'  => 'Required fields: id, title, published_at, brand, price',
            'status'   => 'error'
        ]);

        $mock = new MockHandler([
            new Response(400, $this->headers, $body)
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $data = [
            ['sku' => 'AAA']
        ];

        $resource = new Product($client, $this->config);

        $resource->import($data);
    }

    /**
     * @covers \Dafiti\Datajet\Resource\Product::import
     *
     * @expectedException \GuzzleHttp\Exception\ServerException
     */
    public function testShouldRetrieveErrorWhenImportProductServerFail()
    {
        $body = json_encode([
            'affected' => 0,
            'message'  => 'Something Wrong',
            'status'   => 'error'
        ]);

        $mock = new MockHandler([
            new Response(500, $this->headers, $body)
        ]);

        $handler = HandlerStack::create($mock);
        $client  = new Client(['handler' => $handler]);

        $data = [
            ['sku' => 'AAA']
        ];

        $resource = new Product($client, $this->config);

        $resource->import($data);
    }

    /**
     * @covers \Dafiti\Datajet\Resource\Product::import
     */
    public function testShouldFalseWhenImportProductFail()
    {
        $body = json_encode([
            'affected' => 0,
            'status'   => 'ok'
        ]);

        $mock = new MockHandler([
            new Response(200, $this->headers, $body)
        ]);

        $handler = HandlerStack::create($mock);
        $client  = new Client(['handler' => $handler]);

        $data = [
            [
                "id"    => "1834276",
                "title" => "Sapatilha Branca",
                "brand" => [
                    "id"   => "1",
                    "name" => "SPB",
                    "slug" => ""
                ],
                "attributes" => [
                    "material" => ["Textil"]
                ],
                "price" => [
                    "current"  => 88.88,
                    "previous" => 100.99,
                    "currency" => "BRL"
                ],
                "sku" => "SPB0001C",
            ]
        ];

        $resource = new Product($client, $this->config);

        $this->assertFalse($resource->import($data));
    }

    /**
     * @covers \Dafiti\Datajet\Resource\Product::import
     */
    public function testShouldReturnTrueWhenImportProductSuccess()
    {
        $body = json_encode([
            'affected' => 1,
            'status'   => 'ok'
        ]);

        $mock = new MockHandler([
            new Response(200, $this->headers, $body)
        ]);

        $handler = HandlerStack::create($mock);
        $client  = new Client(['handler' => $handler]);

        $data = [
            [
                "id"    => "1834276",
                "title" => "Sapatilha Branca",
                "brand" => [
                    "id"   => "1",
                    "name" => "SPB",
                    "slug" => ""
                ],
                "attributes" => [
                    "material" => ["Textil"]
                ],
                "price" => [
                    "current"  => 88.88,
                    "previous" => 100.99,
                    "currency" => "BRL"
                ],
                "sku" => "SPB0001C",
            ]
        ];

        $resource = new Product($client, $this->config);

        $this->assertTrue($resource->import($data));
    }

    /**
     * @covers \Dafiti\Datajet\Resource\Product::search
     *
     * @expectedException \GuzzleHttp\Exception\ClientException
     */
    public function testShouldRetrieveErrorWhenSearchWithoutParams()
    {
        $body = json_encode([
            'status'   => 'error',
            'message'  => 'Json format is not valid'
        ]);

        $mock = new MockHandler([
            new Response(400, $this->headers, $body)
        ]);

        $handler  = HandlerStack::create($mock);
        $client   = new Client(['handler' => $handler]);
        $resource = new Product($client, $this->config);

        $resource->search([]);
    }

    /**
     * @covers \Dafiti\Datajet\Resource\Product::search
     */
    public function testShouldReturnEmptyItemsInSearch()
    {
        $body = json_encode([
            'status' => 'ok',
            'id'     => 'ID',
            'count'  => 100
        ]);

        $mock = new MockHandler([
            new Response(200, $this->headers, $body)
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $data = ['' => ''];

        $resource = new Product($client, $this->config);
        $result   = $resource->search($data);

        $this->assertFalse(isset($result['items']));
    }

    /**
     * @covers \Dafiti\Datajet\Resource\Product::search
     */
    public function testShouldReturnSearchResults()
    {
        $body = json_encode([
            'status' => 'ok',
            'id'     => 'ID',
            'count'  => 100,
            'items'  => [
                ['sku' => 'AAA'],
                ['sku' => 'BBB']
            ]
        ]);

        $mock = new MockHandler([
            new Response(200, $this->headers, $body)
        ]);

        $handler = HandlerStack::create($mock);
        $client  = new Client(['handler' => $handler]);

        $data = ['q' => 'shoes', 'size' => 2];

        $resource = new Product($client, $this->config);
        $result   = $resource->search($data);

        $this->assertCount(2, $result['items']);
    }

    /**
     * @covers \Dafiti\Datajet\Resource\Product::delete
     *
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage ID Product must be numeric
     */
    public function testDeleteWhenIdIsntNumeric()
    {

        $mock = new MockHandler([
            new Response(200, $this->headers, json_encode([]))
        ]);

        $handler = HandlerStack::create($mock);
        $client  = new Client(['handler' => $handler]);

        $resource = new Product($client, $this->config);
        $resource->delete('DAFITI');
    }

    /**
     * @covers \Dafiti\Datajet\Resource\Product::delete
     */
    public function testDeleteWhenExistProduct()
    {

        $mock = new MockHandler([
            new Response(200, $this->headers, json_encode(['affected' => 1]))
        ]);

        $handler = HandlerStack::create($mock);
        $client  = new Client(['handler' => $handler]);

        $resource = new Product($client, $this->config);
        $this->assertTrue($resource->delete(1));
    }

    /**
     * @covers \Dafiti\Datajet\Resource\Product::delete
     */
    public function testDeleteWhenNotExistProduct()
    {
        $mock = new MockHandler([
            new Response(404, $this->headers, json_encode(['affected' => 0]))
        ]);

        $handler = HandlerStack::create($mock);
        $client  = new Client(['handler' => $handler]);

        $resource = new Product($client, $this->config);
        $this->assertFalse($resource->delete(1));
    }
}
