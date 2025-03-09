Eboreum/Collections: Moving PHP closer towards generics
===============================

![license](https://img.shields.io/packagist/l/eboreum/collections.svg)
![build](https://github.com/eboreum/collections/workflows/build/badge.svg?branch=main)
[![Code Coverage](https://img.shields.io/endpoint?url=https://gist.githubusercontent.com/kafoso/41726f60f5b61eb3197459c1fbfea90e/raw/test-coverage__main.json)](https://github.com/eboreum/collections/actions)
[![PHPStan Level](https://img.shields.io/endpoint?url=https://gist.githubusercontent.com/kafoso/41726f60f5b61eb3197459c1fbfea90e/raw/phpstan-level__main.json)](https://github.com/eboreum/collections/actions)

Wish you had generics in PHP? This library provides a sensible means of managing collections of data (i.e. arrays with restrictions), immutably, until such a time that PHP generics are bestowed upon us.

[comment]: # (The README.md is generated using `script/generate-readme.php`)


Comes fully equipped with [phpstan](https://packagist.org/packages/phpstan/phpstan) generics annotations. For details, see: https://phpstan.org/blog/generics-in-php-using-phpdocs

<a name="requirements"></a>
# Requirements

```json
"php": "^8.3",
"eboreum/caster": "^2.1",
"eboreum/exceptional": "^2.0"
```

For more information, see the [`composer.json`](composer.json) file.

# Installation

Via [Composer](https://getcomposer.org/) (https://packagist.org/packages/eboreum/collections):

    composer install eboreum/collections

Via GitHub:

    git clone git@github.com:eboreum/collections.git

# Fundamentals

This library has two core components:

 1. The class `Eboreum\Collections\Collection`.
 2. The interface `Eboreum\Collections\Contract\CollectionInterface`.

`Eboreum\Collections\Collection` by itself imposes no restrictions and may used for storing any data type. It can be thought of as a fancy array. However, it has two crucial differences and advantages over simple arrays: It's **immutable** and it's **type hinted**, including annotations for generics ([https://phpstan.org/blog/generics-in-php-using-phpdocs](https://phpstan.org/blog/generics-in-php-using-phpdocs)).

The true power of this library shows once you start making collection classes, extending `Eboreum\Collections\Collection`, which have restrictions.

This library comes equipped with the following simple data type collection classes:

- `Eboreum\Collections\FloatCollection`: A collection, which only ever contains float numbers.
- `Eboreum\Collections\IntegerCollection`: A collection, which only ever contains integers.
- `Eboreum\Collections\ObjectCollection`: A collection, which only ever contains objects – any objects. More on how to make collections for specific class instances below.
- `Eboreum\Collections\StringCollection`: A collection, which only ever contains strings.

The real shine comes when we start making restrictions on the contents of the collection classes, for instance **restrictions on specific classes**.

This library has the following **predefined class collections**:

 - `\Eboreum\Collections\Object_\ClosureCollection`: A collection, which may and will only ever contain instances of `\Closure`.
 - `\Eboreum\Collections\Object_\DateTimeCollection`: A collection, which may and will only ever contain instances of `\DateTime`.
 - `\Eboreum\Collections\Object_\DateTimeImmutableCollection`: A collection, which may and will only ever contain instances of `\DateTimeImmutable`.
 - `\Eboreum\Collections\Object_\DateTimeInterfaceCollection`: A collection, which may and will only ever contain instances of `\DateTimeInterface`.
 - `\Eboreum\Collections\Object_\DirectoryCollection`: A collection, which may and will only ever contain instances of `\Directory`.
 - `\Eboreum\Collections\Object_\ErrorCollection`: A collection, which may and will only ever contain instances of `\Error`.
 - `\Eboreum\Collections\Object_\ExceptionCollection`: A collection, which may and will only ever contain instances of `\Exception`.
 - `\Eboreum\Collections\Object_\SplFileInfoCollection`: A collection, which may and will only ever contain instances of `\SplFileInfo`.
 - `\Eboreum\Collections\Object_\SplFileObjectCollection`: A collection, which may and will only ever contain instances of `\SplFileObject`.
 - `\Eboreum\Collections\Object_\ThrowableCollection`: A collection, which may and will only ever contain instances of `\Throwable`.
 - `\Eboreum\Collections\Object_\stdClassCollection`: A collection, which may and will only ever contain instances of `\stdClass`.


You may use the above files as inspiration on how to build your own specific class collections. However, this process can be automated; read more about automation further down.

The above classes utilize the abstract class `\Eboreum\Collections\Abstraction\AbstractNamedClassOrInterfaceCollection` and subsequently the interface `\Eboreum\Collections\Contract\ObjectCollectionInterface`.

## Automating writing of collection classes

> **⚠️ NOTICE ⚠️**
>
> **"eboreum/collection-class-generator" is NOT YET publicly available!**

Writing tens or even hundreds of collection classes, which essentially have the same structure, is tedious. This process can be automated by utilizing [https://packagist.org/packages/eboreum/collection-class-generator](https://packagist.org/packages/eboreum/collection-class-generator), which will write all desired collection classes for you, directly into in your own project to one or more locations of your choosing. It even comes with opt-in writing of unit tests ([https://packagist.org/packages/phpunit/phpunit](https://packagist.org/packages/phpunit/phpunit))!

## Make collections for anything – not just classes

You may make restrictions for anything – not just classes – including unions ([https://php.watch/versions/8.0/union-types](https://php.watch/versions/8.0/union-types)). Essentially, you merely have to override the `isElementAccepted` method in the collection class you are implementing.

Need something that will accept both integers and float numbers (a union)?

Simply do the following:

```php
/**
 * {@inheritDoc}
 */
public static function isElementAccepted($element): bool
{
    return is_int($element) || is_float($element);
}
```

Do remember: If you utilize phpstan, you should also add and/or update the `@template` and its references throughout your custom collection class.

You should also override methods such as `current`, `find`, `first`, etc. with a suitable return type. For instance:

PHP ^8.1:

```php
/**
 * {@inheritDoc}
 */
public function current(): int|float
{
    // ...
}
```

Notice: Intersection types ([https://php.watch/versions/8.1/intersection-types](https://php.watch/versions/8.1/intersection-types)) are **not supported** as they are not supported by PHP 8 due to https://wiki.php.net/rfc/nullable_intersection_types having been declined.

The reason is that methods such as `current`, `find`, `first`, etc. cannot have nullable return types when handling intersections.

# Tests

## Test/development requirements

```json
"phpstan/phpstan": "^2.1.5",
"phpunit/phpunit": "^11.3",
"slevomat/coding-standard": "8.15.0",
"squizlabs/php_codesniffer": "3.10.2"
```

## Running tests

For all unit tests, first follow these steps:

```
cd tests
php ../vendor/bin/phpunit
```

# License & Disclaimer

See [`LICENSE`](LICENSE) file. Basically: Use this library at your own risk.

# Contributing

We prefer that you create a ticket and or a pull request at https://github.com/eboreum/collections, and have a discussion about a feature or bug here.

## No `\ArrayAccess`!

This library does not and will not utilize `\ArrayAccess` ([https://www.php.net/manual/en/class.arrayaccess.php](https://www.php.net/manual/en/class.arrayaccess.php)).

It goes against the immutable nature of this library, it's a little bit evil, and it makes code unnecessarily obscure.

# Credits

## Authors

- **Kasper Søfren** (kafoso)<br>E-mail: <a href="mailto:soefritz@gmail.com">soefritz@gmail.com</a><br>Homepage: <a href="https://github.com/kafoso">https://github.com/kafoso</a>
- **Carsten Jørgensen** (corex)<br>E-mail: <a href="mailto:dev@corex.dk">dev@corex.dk</a><br>Homepage: <a href="https://github.com/corex">https://github.com/corex</a>

## Acknowledgements

### doctrine/collections

This library is greatly inspired by https://packagist.org/packages/doctrine/collections (https://github.com/doctrine/collections) and some of the code is indeed copied from that library (acknowledged by inclusion of the LICENSE file contents at the top of select files, as required by https://github.com/doctrine/collections/blob/94918256daa6ac99c7e5774720c0e76f01936bda/LICENSE).
