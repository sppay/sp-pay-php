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
$validatedAccount = (new Sppay\SpPayPhp())->validateAccount(
    bearerToken: 'bearer_token_from_authentication'
    institutionCode: 'SPP',
    accountNumber: '1000'
);
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
