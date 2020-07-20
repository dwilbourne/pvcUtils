<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\range\non_negative_integer;

use PHPUnit\Framework\TestCase;
use pvc\range\non_negative_integer\NonNegativeIntegerRange;
use pvc\range\SetRangeException;

class NonNegativeIntegerRangeTest extends TestCase
{

    protected NonNegativeIntegerRange $range;

    public function setUp(): void
    {
        $this->range = new NonNegativeIntegerRange();
    }

    public function testAddInteger() : void
    {
        $int = 5;
        $this->range->addItem($int);
        self::assertTrue(in_array($int, $this->range->getRange()));
    }

    public function testAddRangeFrom3To7() : void
    {
        $start = 3;
        $end = 7;
        $this->range->addRange($start, $end);
        self::assertTrue(in_array(5, $this->range->getRange()));
    }

    public function testAddRangeFromMinus3ToMinus7() : void
    {
        $start = -3;
        $end = -7;
        self::expectException(SetRangeException::class);
        $this->range->addRange($start, $end);
    }

    public function testAddStringItemToRangeWithNumber2() : void
    {
        $item = '2';
        $this->range->addStringItemToRange($item);
        self::assertTrue(in_array(2, $this->range->getRange()));
    }

    public function testAddStringItemToRangeWithRangeZeroThroughFour() : void
    {
        $item = '0-4';
        $this->range->addStringItemToRange($item);
        self::assertEquals(5, count($this->range->getRange()));
    }

    public function testAddStringItemToRangeException() : void
    {
        $item = '0-';
        self::expectException(SetRangeException::class);
        $this->range->addStringItemToRange($item);
    }
}
