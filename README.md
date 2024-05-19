# Mykad package is a laravel package, purposely to validate, parse, and extract Malaysian Identity Card (MyKad) numbers.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/fikrimastor/mykad.svg?style=flat-square)](https://packagist.org/packages/fikrimastor/mykad)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/fikrimastor/mykad/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/fikrimastor/mykad/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/fikrimastor/mykad/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/fikrimastor/mykad/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/fikrimastor/mykad.svg?style=flat-square)](https://packagist.org/packages/fikrimastor/mykad)

## Installation

You can install the package via composer:

```bash
composer require fikrimastor/mykad
```

Optionally, you can publish the config file with:

```bash
php artisan vendor:publish --tag="mykad-config"
```

This is the contents of the published config file:

```php
return [
    'states-code' => [
        // Source: https://www.jpn.gov.my/my/kod-negeri

        // Johor
        '01' => 'Johor',
        '21' => 'Johor',
        '22' => 'Johor',
        '23' => 'Johor',
        '24' => 'Johor',

        // Kedah
        '02' => 'Kedah',
        '25' => 'Kedah',
        '26' => 'Kedah',
        '27' => 'Kedah',

        ...
        ...
        ...

        // Negeri Tidak Diketahui
        '82' => 'Unknown',
    ],
];
```

## Usage

```php
use FikriMastor\MyKad\Facades\MyKad;

echo MyKad::sanitize('010101-01-0101'); // '010101010101'

echo MyKad::extract('010101010101'); 
//[
//  "date_of_birth" => "1 January 2001"
//  "state" => "Johor"
//  "gender" => "Male"
//]
```

You can also use the validator to validate the MyKad number.

```php
use FikriMastor\MyKad\Rules\IsMyKad;
 
$request->validate([
    'mykad' => ['required', 'string', new IsMyKad],
]);
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Fikri Mastor](https://github.com/fikrimastor)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
