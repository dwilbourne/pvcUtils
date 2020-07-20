<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\array_utils\CartesianProduct;

use ArrayIterator;
use Iterator;

/**
 * This is a modification of code found more or less here:
 * https://github.com/hoaproject/Math/blob/master/Source/Combinatorics/Combination/CartesianProduct.php
 *
 * It was published with a BSD license and that license is included below.
 *
 */

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2018, Hoa community. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

/**
 * Cartesian product creates a cross product, or Cartesian product, of elements from different sets.
 *
 * It is an iterator that produces tuples that represent all possible combinations of "one from set a,
 * one from set b...." etc.
 *
 *
 * Class CartesianProduct
 *
 */
class CartesianProduct implements Iterator
{

    /**
     *
     * array of iterators used to create the cartesian product.  It is set at construction time and
     * cannot be modified after that, because doing in the middle of iterating would produce inconsistent element
     * dimensionality between the last set produced prior to the addition / subtraction and the next tuple after.
     *
     * @var Iterator[]
     */
    protected array $arrayOfIterators = [];

    /**
     * In order to conform to the Iterator interface, key has to be an integer.  But you can think of each
     * incremented key as mapping to an array which has the same length as the arrayOfIterators, and each
     * element of the array would be an integer that corresponds to the current position in the corresponding iterator.
     *
     * @var int
     */
    protected int $key = 0;

    /**
     * reference to the last iterator in the arrayOfIterators, which is used to determine whether the internal
     * pointers are valid
     *
     * @var Iterator
     */
    protected Iterator $lastIterator;

    /**
     * The constructor creates an array of iterators, which gives us access to the current position in each array.
     *
     * CartesianProduct constructor.
     *
     * @param array $iterators
     * @throws CartesianProductException
     */
    public function __construct(array $iterators)
    {
        if (count($iterators) == 0) {
            throw new CartesianProductException();
        }
        for ($i = 0; $i < count($iterators); $i++) {
            $this->addSet($iterators[$i]);
        }
        $this->lastIterator = $this->arrayOfIterators[$i - 1];
    }

    /**
     * @function addSet
     * @param array|Iterator $set
     * @throws CartesianProductException
     */
    protected function addSet($set) : void
    {
        if (is_array($set)) {
            $set = new ArrayIterator($set);
        }
        if (!$set instanceof Iterator || 0 == iterator_count($set)) {
            throw new CartesianProductException();
        }
        $set->rewind();
        $this->arrayOfIterators[] = $set;
    }

    /**
     * @function current
     *
     * Get the current tuple.
     *
     * @return array
     */
    public function current(): array
    {
        $currentTuple = [];
        foreach ($this->arrayOfIterators as $iterator) {
            $currentTuple[] = $iterator->current();
        }
        return $currentTuple;
    }

    /**
     * @function key
     *
     * Get the current key.
     *
     * @return int
     */
    public function key(): int
    {
        return $this->key;
    }

    /**
     * @function next
     *
     * Advance the internal pointers.
     *
     */
    public function next(): void
    {
        foreach ($this->arrayOfIterators as $iterator) {
            $iterator->next();
            if (!$iterator->valid() && ($iterator != $this->lastIterator)) {
                $iterator->rewind();
            } else {
                // break the foreach loop - $iterator is valid or it is the last iterator
                // and the last iterator is invalid.
                break;
            }
        }
        $this->key++;
    }

    /**
     * @function rewind
     *
     * Rewind the internal pointers.
     */
    public function rewind() : void
    {
        $this->key = 0;
        foreach ($this->arrayOfIterators as $iterator) {
            $iterator->rewind();
        }
    }

    /**
     * @function valid
     *
     * The iterator is in a valid state if the lastIterator exists (meaning the arrayOfIterators is not empty)
     * and the last iterator is itself valid.
     *
     * @return bool
     */
    public function valid(): bool
    {
        return (isset($this->lastIterator) && $this->lastIterator->valid());
    }
}
