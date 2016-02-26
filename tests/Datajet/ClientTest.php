<?php

namespace Dafiti\Datajet;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Dafiti\Datajet\Client::create
     * @covers Dafiti\Datajet\Client::__construct
     * @covers Dafiti\Datajet\Client::__get
     */
    public function testShouldCreateClient()
    {
        $client = Client::create();

        $this->assertInstanceOf('Dafiti\Datajet\Client', $client);
    }

    /**
     * @covers Dafiti\Datajet\Client::__construct
     * @covers Dafiti\Datajet\Client::__get
     * @covers Dafiti\Datajet\Exception\ResourceNotFound
     *
     * @expectedException Dafiti\Datajet\Exception\ResourceNotFound
     * @expectedExceptionMessage The resource movie not found
     */
    public function testShouldThrowExceptionWhenResourceNotFound()
    {
        $httpClient = $this->getMockBuilder('\GuzzleHttp\Client')
            ->disableOriginalConstructor()
            ->getMock();

        $client = new Client($httpClient);

        $client->movie->findAll();
    }

    /**
     * @covers Dafiti\Datajet\Client::__construct
     * @covers Dafiti\Datajet\Client::__get
     */
    public function testShouldGetResource()
    {
        $httpClient = $this->getMockBuilder('\GuzzleHttp\Client')
            ->disableOriginalConstructor()
            ->getMock();

        $client = new Client($httpClient, [
            'data' => [
                'uri'        => 'http://fisherman.local',
                'key' => 'b'
            ],
            'search' => [
                'uri'        => 'http://hawk.local',
                'key' => 'b'
            ]
        ]);

        $result = $client->product;

        $this->assertInstanceOf('Dafiti\Datajet\Resource\AbstractResource', $result);
    }

    /**
     * @covers Dafiti\Datajet\Client::__construct
     * @covers Dafiti\Datajet\Client::__get
     */
    public function testShouldGetLoadedResource()
    {
        $httpClient = $this->getMockBuilder('\GuzzleHttp\Client')
            ->disableOriginalConstructor()
            ->getMock();

        $client = new Client($httpClient, [
            'data' => [
                'uri'        => 'http://fisherman.local',
                'key' => 'b'
            ],
            'search' => [
                'uri'        => 'http://hawk.local',
                'key' => 'b'
            ]
        ]);

        $client->product;

        $result = $client->product;

        $this->assertInstanceOf('Dafiti\Datajet\Resource\AbstractResource', $result);
    }
}
