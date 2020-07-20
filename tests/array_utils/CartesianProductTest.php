<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\array_utils;

use ArrayIterator;
use pvc\array_utils\CartesianProduct\CartesianProduct;
use PHPUnit\Framework\TestCase;
use pvc\array_utils\CartesianProduct\CartesianProductException;
use stdClass;
use tests\array_utils\fixtures\CartesianProductTestObjectFixture;

class CartesianProductTest extends TestCase
{
    protected array $testSet;
    protected array $expectedResult = [
        ['a', 1, '!'],
        ['b', 1, '!'],
        ['a', 2, '!'],
        ['b', 2, '!'],
        ['a', 3, '!'],
        ['b', 3, '!'],
        ['a', 1, '@'],
        ['b', 1, '@'],
        ['a', 2, '@'],
        ['b', 2, '@'],
        ['a', 3, '@'],
        ['b', 3, '@'],
    ];

    public function setUp(): void
    {
        $iteratorA = new ArrayIterator(['a', 'b']);
        $iteratorB = new ArrayIterator([1, 2, 3]);
        $iteratorC = new CartesianProductTestObjectFixture();

        $this->testSet = [$iteratorA, $iteratorB, $iteratorC];
    }

    public function testConstructEmpty() : void
    {
        self::expectException(CartesianProductException::class);
        $cp = new CartesianProduct([]);
    }

    public function testConstructWithOneEmptySet() : void
    {
        $iteratorA = new ArrayIterator(['a', 'b']);
        $iteratorB = new ArrayIterator([]);
        self::expectException(CartesianProductException::class);
        $cp = new CartesianProduct([$iteratorA, $iteratorB]);
    }

    public function testConstructBadSets() : void
    {
        // neither array nor object is iterable
        $obj = new stdClass();
        $testSet = [
            [1, 2, 3],
            $obj
        ];

        self::expectException(CartesianProductException::class);
        $cp = new CartesianProduct($testSet);
    }

    public function testConstruct() : void
    {
        $cp = new CartesianProduct($this->testSet);
        self::assertEquals(0, $cp->key());
        self::assertTrue($cp->valid());
    }

    public function testIteration() : void
    {
        $cp = new CartesianProduct($this->testSet);
        $actualResult = [];
        foreach ($cp as $tuple) {
            $actualResult[] = $tuple;
        }
        self::assertEquals($this->expectedResult, $actualResult);
        self::assertFalse($cp->valid());
        $cp->rewind();
        self::assertEquals(0, $cp->key());
    }
}
