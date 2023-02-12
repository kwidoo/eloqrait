# eloqrait

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kwidoo/eloqrait.svg?style=flat-square)](https://packagist.org/packages/kwidoo/eloqrait)
[![Total Downloads](https://img.shields.io/packagist/dt/kwidoo/eloqrait.svg?style=flat-square)](https://packagist.org/packages/kwidoo/eloqrait)
![GitHub Actions](https://github.com/kwidoo/eloqrait/actions/workflows/main.yml/badge.svg)

The `eloqrait` package provides Laravel Eloquent traits for defining various types of model relations.

## Installation

You can install the package via composer:

```bash
composer require kwidoo/eloqrait
```

## Usage

```php
php artisan eloqrait <relation_type> <first_model_name> <second_model_name> --reverse --namespace=<namespace> --model_namespace=<model_namespace>
```

The available relation types are `hasmany`, `belongsto`, and `belongstomany`. The `--reverse` option can be used to generate a reverse relation trait. You can also specify a custom namespace for the generated code and the model namespace.

For more information on using the generated traits, please refer to the Laravel Eloquent documentation.

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email oleg@pashkovsky.me instead of using the issue tracker.

## Credits

- [Oleg Pashkovsky](https://github.com/kwidoo)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
