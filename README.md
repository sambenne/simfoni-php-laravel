# Simfoni Laravel interface for PHP

<a href="https://packagist.org/packages/mblsolutions/simfoni-php-laravel"><img src="https://github.com/mblsolutions/simfoni-php-laravel/actions/workflows/simfoni-php-laravel.yml/badge.svg"></a>
<a href="https://packagist.org/packages/mblsolutions/simfoni-php-laravel"><img src="https://img.shields.io/packagist/v/mblsolutions/simfoni-php-laravel" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/mblsolutions/simfoni-php-laravel"><img src="https://img.shields.io/packagist/l/mblsolutions/simfoni-php-laravel" alt="License"></a>


The Simfoni Laravel Interface for PHP gives you an API interface into the Simfoni Platform for your Laravel applications.

## Contents

- [Installation](#installation)
- [Configuration](#installation)
- [Authentication](#authentication)
- [Create Order](#create-order)
- [Issued Info](#issued-info)
- [Decryption](#decryption)
- [Responses](#responses)
- [Errors](#errors)
- [License](#license)

### Installation

The recommended way to install Simfoni PHP Laravel is through [Composer](https://getcomposer.org/).

```bash
composer require mblsolutions/simfoni-php-laravel
```

#### Laravel without auto-discovery

If you don't use auto-discovery, add the ServiceProvider to the providers array in config/app.php
```php
\MBLSolutions\SimfoniLaravel\SimfoniServiceProvider::class,
```

If you want to use the facade for authentication, add this to your facades in app.php

'SimfoniAuth' => \MBLSolutions\SinfoniLaravel\SimAuthFacade::class,

### Configuration

To import the default Simfoni configuration file into laravel please run the following command

```bash
php artisan vendor:publish --prodivder="MBLSolutions\SimfoniLaravel\SimfoniServiceProvider"
```

A new config file will be available in config/simfoni.php - Please ensure you update these configuration items with details provided by Redu Retail.

### Authentication

Please Note: Your API credentials (Client ID, Client Secret and access_tokens) carry many permissions. Keep these
credentials secure. Do not share this data in Github, client side code etc... If you believe any of your credentials have
been compromised, within the Simfoni application interface revoke/reset user tokens, client secrets and
encryption keys.

Authentication can be made by passing your Simfoni Application `Client ID`, `Client Secret`,
`Users Email` and `Users Password` to the  authentication password method.

```php
$simfoniAuth = new \MBLSolutions\Simfoni\Authentication();

$authentication = $simfoniAuth->password(1, 'auth-secret', 'john.doe@exmaple.com', 'password');
```

A successful authentication will return an OAuth response containing access, refresh and user information.

```php
[
    'token_type' => 'Bearer',
    'expires_in' => 31622400,
    'access_token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjBmOGMwNDAxZDAy',
    'refresh_token' => 'def5020002eca9ac7875d5d800c195024d7fb702535c0d30a0',
    'user' => [
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
        'role' => 'programme_manager'
    ]
];
```

We recommend this information is stored and reused between requests (the authentication will expire '31622400' seconds
from the moment the authentication is issued).

Use your `access_token` by setting the token in the Simfoni Configuration. The PHP library will automatically
attach this token to each api request (within the current request, each PHP request would need to re-set this token).


```php
\MBLSolutions\Simfoni\Simfoni::setToken('eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjBmOGMwNDAxZDAy');
```

### Create Order

To create an order, certain parameters are required, many are optional depending on the nature of the order.
For example, to request a single e-Code for a product with a value of £10.00, the following example would be the minimum required. Under these circumstances much of the order information is predetermined by the configuration of the Simfoni system.

The processing of orders is subject to certain rules applied to the product being ordered. One of these is a control over when the product can be ordered without manual intervention for payment. Orders will not be processed if the account does not have sufficient funds available either through a credit or debit facility (or the account is in aged debt). Similarly, the product may be subject to restrictions on when it can be ordered without manual intervention (Fast Track). Normally, this will be set to 24 X 7 processing. If for any reason there are restrictions on a product, an error message will be returned in the form:
```php

$payload = [
    'urn' => 1234,
    'items' => [
        'data' => [
            [
                'sku' => 'ECODE_SKU',
                'quantity' => 1,
                'price' => 10.00,
                'activation_date' => '2021-01-01T17:00:00+00:00',
            ],
        ]
    ]
];

\MBLSolutions\Simfoni\Simfoni::setToken('your-token');

$order = new \MBLSolutions\Simfoni\Order();
$response = $order->create($payload);
```

A successful request will show information about the placed order.

```php
[
    'data' => [
        'id' => 6000001,
        'invoice_number' => null,
        'client_name' => 'An Client',
        'account_name' => 'An Account',
        'reference' => 'order-reference-here',
        'po_number' => null,
        'discount' => 0,
        'handling' => 0,
        'status' => 'Awaiting Payment',
        'contact_company' => 'An Account',
        'contact_title' => 'Ms',
        'contact_first_name' => 'Dayton',
        'contact_last_name' => 'Quitzon',
        'contact_email' => 'bryon.tromp@example.net',
        'contact_address1' => '843',
        'contact_address2' => 'Freeway',
        'contact_address3' => 'Apt. 979',
        'contact_town_city' => 'Lake Faye',
        'contact_county' => 'Idaho',
        'contact_postcode' => '43270',
        'contact_country' => 'GB',
        'billing_company' => 'An Account',
        'billing_title' => 'Ms',
        'billing_first_name' => 'Dayton',
        'billing_last_name' => 'Quitzon',
        'billing_email' => 'bryon.tromp@example.net',
        'billing_address1' => '843',
        'billing_address2' => 'Freeway',
        'billing_address3' => 'Apt. 979',
        'billing_town_city' => 'Lake Faye',
        'billing_county' => 'Idaho',
        'billing_postcode' => '43270',
        'billing_country' => 'GB',
        'order_date' => '2021-01-01T15:00:00+00:00'
    ],
];
```

### Issued Info

To view details on encrypted PAN, Serial, Pin and Url information associated with the order you must request this using `IssuedInfo`

In order to call this end point, a Webhook must be set up to return the required parameter information when the order is complete.

```php
\MBLSolutions\Simfoni\Simfoni::setToken('your-token');

$issuedInfo = new \MBLSolutions\Simfoni\IssuedInfo();
$result = $issedInfo->show('your-hash');
```

An example successful response

```php
[
    'data' => [
        [
            'item_id' => 'XqVroNPWnw',
            'sku' => '12345678',
            'value' => 100,
            'masked_pan' => '************1350',
            'encrypted_pan' => 'eyJpdiI6ImJuY2RIM3hrSnVcL3Q4amhET1BwSjZnPT0iLCJ2YWx1ZSI6ImRwWU42anNZNlFXZUJ0REVFSXJDTFFmNjVuZHFcL3ZNaXQ1Z3gzNUZ1OUt3PSIsIm1hYyI6IjVkMjM5ODlkYWM2ZjE5OWJiYjMzNWQyYTZkZDI1ZWJkZGUzMTVlZDFjOGM5NDM4YzllNWM5ZWExN2YxNDZhMjYifQ==',
            'masked_serial' => '****4360',
            'encrypted_serial' => 'eyJpdiI6IlwvVXpuQW9KVittem1rQnVQc0t3N2p3PT0iLCJ2YWx1ZSI6IlFKSGt6OXpHWWlnSFJ5WjFGWDAxaVE9PSIsIm1hYyI6ImRlNTEwMzUzZTZlZjUyNjdkZDBhZTFlYWFiOWNiYzIyNmZlNWJhMGFiYjgxYzI1NzgzMTIzMjFiYjYwOWYyMDQifQ==',
            'pin' => 'eyJpdiI6IndXNldUdkRDeDZkVFFiSVErK25sM1E9PSIsInZhbHVlIjoidjJBUFVhUlMzaTFMREVTSEVTYTRQRTkydnlaN2tESmR4ZXVpd1hpMVZaWGdseVwvNnNFeEVZVyttZEMyRnZ1dVhXWElNM3p6dWhGYmNjTko0ZE12N3dYemFxTllMVU5SVU9EdHhaZGFad2xlUXFKXC9HWEpyTFVnUVNcL2wwQTAwT2RMWEdxUVM4ZklvdnJtNlNzY3ZYaHBuS0dzZkt5QlRoSWZHc2kyb0ppSkRiR0ttMVYrbXg3ZmYrRXBjS1ZkQVhacjV4clhjaVg1TlNxVkFmV2FZb3dKRTdIajVZRDQ1ck9vVFwvTkFZZXpOa1VSSW5pTE9lTVdXelVVTU5OR2h1MWNUK2JFMjZZT1lZQmZ1OEkrS1VINzNnPT0iLCJtYWMiOiJkNTNiOWE1NzFlZDI2MGM1Y2M1MjU3NTcxYzNmMjgxZmRlMjdmMDYwYzRmZDIyYzA2YmQzN2RkZTBkZGMxY2RmIn0=',
            'url' => 'eyJpdiI6Imk0YndiZUZQUGxmN25JMWZQT21KVkE9PSIsInZhbHVlIjoiZW02eTFCem9wRmZsV3pxNUZsZkFqZWFHYWwyOEFhajNCXC9nSVFcL0lEUk9rWnIrbTM5NjBkK0lMV0paSlVzcVRUcG0xVXk0d0ZjYk9wWjlTTlUrQlRIa3QxbHhscWlJdDJ3VlhFUkladjl5eEo4Rnk2UlwvdmNcL21ZTmMzR3ZOc3RuNVYwU0YyUmVtdVA2T0YybDhWSHlydGx6TTZLY2hUUGdxOEhmTWw3NDZoMlJnQ01GRUFSZFdUVE41UEZOditzdWxrQ3NqaGlFOEZWS0d3ZjR0VGUrdTVXcHdHT1E4dzRjQ08xUVJUdWVoSUlNamhcL3ZOTUEwY29henh0S25ZU0MrTWcrNEFBWko1MjdFR1U1RHpvcUN2K01LSzArVjZNZmR0S29EQkM4NG4zNFIxbGhXNG1Ma25OeHZLSmk3aWtcL0siLCJtYWMiOiJiMmY1M2JhNTYyNmFhNzJhNmJmNGNkOWRkMDhlODY0ZDY2MjYzNjJjN2ZkODViM2E0MzRmNWUyYTY2MjQ2MzRiIn0=',
            'activation_date' => '2018-12-25T00:00:00+00:00'
        ]
    ],
    'links' => [
        'first' => 'https://simfoni.co.uk/api/order/eyJpdiI6Im9FN1pMZ2kwMlRlcXU1c1Z5cGxxNXc9PSIsInZhbHVlIjoiXC9SXC9xOFFSSklWa3lEWG82bDhsN293PT0iLCJtYWMiOiIxMDBmYmVjODlhYjE3MDcxNzc2ZDM2NmMxY2YwNThlNWFjN2E3ZDQ3MjBkMTE5NGQwYzE4MDZlNmI3YjkwZjdmIn0=/issuedinfo?page=1',
        'last' => 'https://simfoni.co.uk/api/order/eyJpdiI6Im9FN1pMZ2kwMlRlcXU1c1Z5cGxxNXc9PSIsInZhbHVlIjoiXC9SXC9xOFFSSklWa3lEWG82bDhsN293PT0iLCJtYWMiOiIxMDBmYmVjODlhYjE3MDcxNzc2ZDM2NmMxY2YwNThlNWFjN2E3ZDQ3MjBkMTE5NGQwYzE4MDZlNmI3YjkwZjdmIn0=/issuedinfo?page=1',
        'prev' => null,
        'next' => null
    ],
    'meta' => [
        'current_page' => 1,
        'from' => 1,
        'last_page' => 1,
        'path' => 'https://simfoni.co.uk/api/order/eyJpdiI6Im9FN1pMZ2kwMlRlcXU1c1Z5cGxxNXc9PSIsInZhbHVlIjoiXC9SXC9xOFFSSklWa3lEWG82bDhsN293PT0iLCJtYWMiOiIxMDBmYmVjODlhYjE3MDcxNzc2ZDM2NmMxY2YwNThlNWFjN2E3ZDQ3MjBkMTE5NGQwYzE4MDZlNmI3YjkwZjdmIn0=/issuedinfo',
        'per_page' => 20,
        'to' => 1,
        'total' => 1
    ],
];
```


### Decryption

When utilising `IssuedInfo` the data returned contains encrypted PAN, Serial, Pin and Url information associated with the order.
As this information is confidential, it is returned in an encrypted form. To decrypt the values, a secret key is required. 

To decrypt the values, a secret key is required. This is specifically for the decryption of this information.
This key will be provided by Redu Retail as part of the configuration.

```php

// test decrypting a 4 digit pin
$decrypt = new new \MBLSolutions\Simfoni\Decrypt('JD62JFgGrKJdc1UsZmHykg==');
$string = 'eyJpdiI6Ikhjdlp1Uzc0WFZ2MkdDZ3lHc3VVQnc9PSIsInZhbHVlIjoiXC8reHhBditLWUc3eDdiWlFhVm96enBXQlhJSUR6VzZZb3I4NE9MNkd6Tms9IiwibWFjIjoiYjFiNWRlYzI0NDY2ZmZmYTk4NGJhMjgxN2EwZTAyZjg0YzJmNjg5YmNiMDA2ZDQ1OWViODgxM2QwM2FiNjk3YSJ9';

$pin = $decrypt->data($string);
// pin = 3840
```

### Responses

All responses will be returned as an array. To make calls consistent, all will paginate their results, even if the response only contains one result, or no results at all.

There will be three top-level keys in a response
- `data`, which contains an array of results.
- `links`, which contains links for pagination purposes.
- `meta`, which contains additional pagination-related information.

```php
[
    'data' => [
        // results
    ],
    'links' => [
        'first' => 'https://simfoni.co.uk/api/order?page=1',
        'next' => 'https://simfoni.co.uk/api/order?page=2',
    ],
    'meta' => [
        'current_page' => 1,
        'from' => 1,
        'last_page' => 1,
        'path' => 'https://simfoni.co.uk/api/order',
        'per_page' => 20,
        'to' => 1,
        'total' => 1
    ]
];
```

### Errors

#### Exceptions

For exception levels errors retrieving data from Simfoni an array will be returned with a single `message` index.

```php
[
    'message' => 'A message describing the error',
];
```

#### Validation Failures

For methods that require specific parameters, if a parameter fails validation then you will receive the following response body:

```php
[
    'message' => 'The given data was invalid.',
    'errors' =>  [
        // errors here
    ],
];
```

The `errors` key will contain an object where the keys are the name of the field that failed validation, and the value is an array of messages describing the validation failure so you can correct your input.

Field names use “dotted” notation to designate nested field names. For example, the field items[0][sku] would be represented as items.0.sku in the errors object.

### License

Simfoni Interface for PHP is free software distributed under the terms of the MIT license.

A contract/subscription to Simfoni is required to make use of this package, for more information contact
[Redu Group Technical](mailto:tech@redu.co.uk)