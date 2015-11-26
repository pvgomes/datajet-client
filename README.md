# Dafiti Datajet Client for PHP
[![Build Status](https://img.shields.io/travis/dafiti/datajet-client/master.svg?style=flat-square)](https://travis-ci.org/dafiti/datajet-client)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/dafiti/datajet-client/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/dafiti/datajet-client/?branch=master)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/dafiti/datajet-client/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/dafiti/datajet-client/?branch=master)
[![HHVM](https://img.shields.io/hhvm/dafiti/datajet-client.svg?style=flat-square)](https://travis-ci.org/dafiti/datajet-client)
[![Latest Stable Version](https://img.shields.io/packagist/v/dafiti/datajet-client.svg?style=flat-square)](https://packagist.org/packages/dafiti/datajet-client)
[![Total Downloads](https://img.shields.io/packagist/dt/dafiti/datajet-client.svg?style=flat-square)](https://packagist.org/packages/dafiti/datajet-client)
[![License](https://img.shields.io/packagist/l/dafiti/datajet-client.svg?style=flat-square)](https://packagist.org/packages/dafiti/datajet-client)

[Datajet.IO](https://github.com/datajet-io) Client for PHP

## Instalation
The package is available on [Packagist](http://packagist.org/packages/dafiti/datajet).
Autoloading is [PSR-4](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md) compatible.
```json
{
    "require": {
        "dafiti/datajet-client": "dev-master"
    }
}
```

## Usage

#### Basic
```php

$config = [
    'hawk' => [
        'import_key' => '<your-key>',
        'search_key' => '<your-key>'
    ]
];

$client = \Dafiti\Datajet\Client::create($config);
```
All returned data is a \stdClass object.

#### Import products
```php
$products = [
    [
        'id'      => '1',
        'title'   => 'Product',
        'payload' => [
            'some' => 'thing'
        ],
        'brand'   => [
            'id'   => '2',
            'name' => 'Dafiti',
            'slug' => 'dafiti'
        ],
        'attributes' => [
            'color' => 'black'
        ],
        'price' => [
            'current'  => 100.00,
            'previous' => 200.00,
            'currency' => 'BRL'
        ],
        'sku' => 'APCC',
        'published_at' => date('Y-m-d H:i:s'),
        'stock_count'  => 3
    ]
];

$client->product->import($products);
```
Please see Datajet.IO docs: [https://github.com/datajet-io/docs/wiki](https://github.com/datajet-io/docs/wiki)

#### Search products
```php

$search = [
    'q'    => 'shoe',
    'filters' => [
        'brand.id' => ['1']
    ],
    'size' => 10,
    'page' => 1
];

$client->product->search($search);
```
Please see Datajet.IO docs: [https://github.com/datajet-io/docs/wiki](https://github.com/datajet-io/docs/wiki)

## License

MIT License
