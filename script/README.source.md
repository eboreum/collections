Eboreum/Collections: Moving PHP closer towards generics
===============================

![license](https://img.shields.io/packagist/l/eboreum/collections.svg)
![build](https://github.com/eboreum/collections/workflows/build/badge.svg?branch=main)
[![Code Coverage](https://img.shields.io/endpoint?url=https://gist.githubusercontent.com/kafoso/41726f60f5b61eb3197459c1fbfea90e/raw/test-coverage__main.json)](https://github.com/eboreum/collections/actions)
[![PHPStan Level](https://img.shields.io/endpoint?url=https://gist.githubusercontent.com/kafoso/41726f60f5b61eb3197459c1fbfea90e/raw/phpstan-level__main.json)](https://github.com/eboreum/collections/actions)

%composer.json.description%

Comes fully equipped with [phpstan](https://packagist.org/packages/phpstan/phpstan) generics annotations. For details, see: https://phpstan.org/blog/generics-in-php-using-phpdocs

The main goals of this library are:

1. **Accuracy**.
   - You know with certainty what the collections in your code base can and must contain.
2. **Safety**.
   - Immutability prevents unintentional modifications of collections.
   - You get an exception as soon as an invalid element is encountered.

It is ***not*** intended for speed. If you need speed in certain areas, you still have e.g. raw array manipulation you can rely on.

<a name="requirements"></a>
# Requirements

%composer.json.require%

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

This library has the following **predefined class collections**, all located under the namespace `\Eboreum\Collections\Object_`:

%run "script/make-readme/list-object-collection-classes-as-markdown.php"%

You may use the above files as inspiration on how to build your own specific class collections.

The above classes utilize the abstract class `\Eboreum\Collections\Abstraction\AbstractNamedClassOrInterfaceCollection` and subsequently the interface `\Eboreum\Collections\Contract\ObjectCollectionInterface`.

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

# Why is immutability necessary?

Simply put: You unintentionally risk changing mutable ("non-immutable") objects when they are being passed around the code base. You and your team may know not to change a mutable object in code bases you control, but third-party libraries (e.g. via Composer) certainly will not respect your rules. Eventually, someone in your team **_will_** forget about your "do-not-change" rule and introduce an ugly bug, which often times is hard to track down.

## Real-world example of a mutable object incident

Imagine you have a database ORM (Doctrine, Eloquent, etc.). If you use `DateTime` instead of `DateTimeImmutable`, passing that instance of `DateTime` around may change it. Consider this: The instance of `DateTime` was used to set an end date from which point a user can no longer log in to your application. The same instance of `DateTime` was used across 100 users (e.g. for optimization reasons). Now your code, because of the change on the `DateTime` instance, inadvertently blocked 100 users from using your application. Such a type of bug is **_very_** hard to find the root cause for.

# Tests

## Test/development requirements

%composer.json.require-dev%

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

## Version branches

- `2.x` = `main`
- `1.x`

## No `\ArrayAccess`!

This library does not and will not utilize `\ArrayAccess` ([https://www.php.net/manual/en/class.arrayaccess.php](https://www.php.net/manual/en/class.arrayaccess.php)).

It goes against the immutable nature of this library, it's a little bit evil, and it makes code unnecessarily obscure.

# Credits

## Authors

%composer.json.authors%

## Acknowledgements

### doctrine/collections

This library is greatly inspired by https://packagist.org/packages/doctrine/collections (https://github.com/doctrine/collections) and some of the code is indeed copied from that library (acknowledged by inclusion of the LICENSE file contents at the top of select files, as required by https://github.com/doctrine/collections/blob/94918256daa6ac99c7e5774720c0e76f01936bda/LICENSE).

## Laravel collection

For certain methods, we have also drawn inspiration from https://laravel.com/docs/12.x/collections.
