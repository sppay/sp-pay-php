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
    clientId: getenv('SPPAY_CLIENT_ID'),
    clientSecret: getenv('SPPAY_CLIENT_SECRET'),
    username: getenv('SPPAY_USERNAME'),
    password: getenv('SPPAY_PASSWORD'),
);

$account = $sppay->validateAccount(
    bearerToken: $auth['access_token'],
    institutionCode: 'MTN',
    accountNumber: '0241234567',
);

if ($account['code'] === 200) {
    echo $account['account']['name_on_account'];
}
```

The client calls production. To use staging, pass `environment: Environment::Staging`:

```php
use Sppay\SpPayPhp\Environment;
use Sppay\SpPayPhp\SpPayPhp;

$sppay = new SpPayPhp(environment: Environment::Staging);
```

Each method returns an array. `code` is the HTTP status, and the other keys are the response from the API. When the request cannot be sent or the response is not JSON, `code` is `0` or the HTTP status, and `error` is `request_failed`, `invalid_request` or `invalid_response`.

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
