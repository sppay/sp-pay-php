# Lightweight PHP Package for Interfacing SP Pay API

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sppay/sp-pay-php.svg?style=flat-square)](https://packagist.org/packages/sppay/sp-pay-php)
[![Tests](https://img.shields.io/github/actions/workflow/status/sppay/sp-pay-php/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/sppay/sp-pay-php/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/sppay/sp-pay-php.svg?style=flat-square)](https://packagist.org/packages/sppay/sp-pay-php)

This is where your description should go. Try and limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require sppay/sp-pay-php
```

## Usage

```php
$spPay = new Sppay\SpPayPhp();
echo $spPay->getTransaction('transactionReferenceGoesHere');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Michael Ekow Selby](https://github.com/ms-sppay)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
