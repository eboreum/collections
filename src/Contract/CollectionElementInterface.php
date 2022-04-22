<?php

declare(strict_types=1);

namespace Eboreum\Collections\Contract;

/**
 * {@inheritDoc}
 *
 * Denotes that the implementing class or extending interface has a partner collection class.
 *
 * This interface is mainly for use with "eboreum/collection-class-generator", allowing said code base to scan for
 * classes implementing this interface, and subsequently making automatically generated PHP code for the respective
 * collection class, containing properly typed methods.
 *
 * /!\ NOTICE /!\
 *
 * "eboreum/collection-class-generator" is NOT YET publicly available!
 *
 * @see https://packagist.org/packages/eboreum/collection-class-generator
 */
interface CollectionElementInterface
{
}
