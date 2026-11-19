# Lightweight PHP Package for Interfacing SP Pay API

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sppay/sp-pay-php.svg?style=flat-square)](https://packagist.org/packages/sppay/sp-pay-php)
[![Tests](https://img.shields.io/github/actions/workflow/status/sppay/sp-pay-php/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/sppay/sp-pay-php/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/sppay/sp-pay-php.svg?style=flat-square)](https://packagist.org/packages/sppay/sp-pay-php)

This is a lightweight PHP package that makes it possible to interact with the SP Pay API with a line of code.

## Installation

You can install the package via composer:

```bash
composer require sppay/sp-pay-php
```

## Usage

```php
use Sppay\SpPayPhp\SpPayPhp;

$sppay = new SpPayPhp();

$auth = $sppay->generateAPIToken(
    client_id: getenv('SPPAY_CLIENT_ID'),
    client_secret: getenv('SPPAY_CLIENT_SECRET'),
    username: getenv('SPPAY_USERNAME'),
    password: getenv('SPPAY_PASSWORD'),
);

$account = $sppay->validateAccount(
    bearer_token: $auth['access_token'],
    institution_code: 'MTN',
    account_number: '0241234567',
);

if ($account['http_status'] === 200) {
    echo $account['account']['name_on_account'];
}
```

The client calls production. To use staging, pass `environment: 'staging'`:

```php
$sppay = new SpPayPhp(environment: 'staging');
```

Each method returns the response from the API, with `http_status` added. When the request cannot be sent, or the response is not JSON, the method returns an error in the same shape:

```php
[
    'http_status' => 0,
    'status' => 'error',
    'message' => 'Failed to connect to engine.sppaygh.com port 443 ...',
    'code' => 'REQUEST_FAILED',
]
```

`code` is `REQUEST_FAILED`, `INVALID_REQUEST` or `INVALID_RESPONSE`.

Requests time out after 60 seconds. To change it, pass `timeout` in seconds:

```php
$sppay = new SpPayPhp(timeout: 30);
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Michael Ekow Selby](https://github.com/ms-sppay)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
