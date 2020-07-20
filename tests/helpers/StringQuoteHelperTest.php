<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

namespace tests\helpers;

use pvc\err\throwable\exception\pvc_exceptions\InvalidValueException;
use pvc\helpers\StringQuoteHelper;
use PHPUnit\Framework\TestCase;

class StringQuoteHelperTest extends TestCase
{
    protected StringQuoteHelper $helper;

    public function setUp() : void
    {
        $this->helper = new StringQuoteHelper();
    }

    public function testQuote() : void
    {
        $input = 'some string';
        $expectedResult = '"some string"';
        self::assertEquals($expectedResult, $this->helper::quote($input, "\""));

        $input = 'some string';
        $expectedResult = '\'some string\'';
        self::assertEquals($expectedResult, $this->helper::quote($input));
    }

    public function testQuoteException() : void
    {
        $input = 'some string';
        self::expectException(InvalidValueException::class);
        $this->helper::quote($input, 'foo');
    }

    public function testUnQuote() : void
    {
        $expectedResult = 'some string';

        $input = '"some string"';
        self::assertEquals($expectedResult, $this->helper::unQuote($input));

        $input = '\'some string\'';
        self::assertEquals($expectedResult, $this->helper::unQuote($input));

        $input = 'some string';
        self::assertEquals($expectedResult, $this->helper::unQuote($input));
    }

    public function testIsQuoted() : void
    {
        $string = '\'This string is quoted\'';
        self::assertTrue($this->helper::isQuoted($string));

        $string = '"This string is also quoted"';
        self::assertTrue($this->helper::isQuoted($string));

        $string = '"This string is not properly quoted';
        self::assertFalse($this->helper::isQuoted($string));

        $string = 'Nor is this one"';
        self::assertFalse($this->helper::isQuoted($string));

        $string = 'And this one has no quotes at all';
        self::assertFalse($this->helper::isQuoted($string));
    }
}
