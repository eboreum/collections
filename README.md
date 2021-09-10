Eboreum/Collections: Moving PHP closer towards generics
===============================

Wish you had generics in PHP? This library provides a sensible means of managing collections of data (i.e. arrays with restrictions), immutably, until such a time that PHP generics are bestowed upon us.

[comment]: # (The README.md is generated using `script/generate-readme.php`)



<a name="requirements"></a>
# Requirements

```json
"php": ">=7.4",
"eboreum/caster": "^0.0.2"
```

For more information, see the [`composer.json`](composer.json) file.

# Installation

Via [Composer](https://getcomposer.org/) (https://packagist.org/packages/eboreum/collections):

    composer install eboreum/collections

Via GitHub:

    git clone git@github.com:eboreum/collections.git

# Fundamentals

# Examples

# Tests

## Test/development requirements

```json
"phpstan/phpstan": "^0.12.99",
"phpunit/phpunit": "^9.5",
"wikimedia/composer-merge-plugin": "^2.0"
```

## Running tests

For all unit tests, first follow these steps:

```
cd tests
php ../vendor/bin/phpunit
```

# License & Disclaimer

See [`LICENSE`](LICENSE) file. Basically: Use this library at your own risk.

# Credits

## Authors

- **Kasper Søfren** (kafoso)<br>E-mail: <a href="mailto:soefritz@gmail.com">soefritz@gmail.com</a><br>Homepage: <a href="https://github.com/kafoso">https://github.com/kafoso</a>
- **Carsten Jørgensen** (corex)<br>E-mail: <a href="mailto:dev@corex.dk">dev@corex.dk</a><br>Homepage: <a href="https://github.com/corex">https://github.com/corex</a>

## Acknowledgements

### doctrine/collections

This library is greatly inspired by https://packagist.org/packages/doctrine/collections (https://github.com/doctrine/collections) and some of the code is indeed copied from that library (acknowledged by inclusion of the LICENSE file contents at the top of select files, as required by https://github.com/doctrine/collections/blob/94918256daa6ac99c7e5774720c0e76f01936bda/LICENSE).
