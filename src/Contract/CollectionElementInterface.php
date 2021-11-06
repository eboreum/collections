<?php
/**
 * @codingStandardsIgnoreStart
 *
 * This file is largely based on: @see https://github.com/doctrine/collections/blob/94918256daa6ac99c7e5774720c0e76f01936bda/lib/Doctrine/Common/Collections/Collection.php
 *
 * From the LICENSE file in doctrine/collections (@see https://github.com/doctrine/collections/blob/94918256daa6ac99c7e5774720c0e76f01936bda/LICENSE):
 *
 * Copyright (c) 2006-2013 Doctrine Project
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @codingStandardsIgnoreEnd
 */

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
 * @see https://packagist.org/packages/eboreum/collection-class-generator
 */
interface CollectionElementInterface
{
}
