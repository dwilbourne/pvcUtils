<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

namespace tests\range\non_negative_integer;

use Error;
use Exception;
use pvc\msg\Msg;
use pvc\range\non_negative_integer\ParserInteger;
use PHPUnit\Framework\TestCase;

class ParserIntegerTest extends TestCase
{
    protected ParserInteger $parser;

    public function setUp() : void
    {
        $this->parser = new ParserInteger();
    }

    public function testParseSuccess() : void
    {
        self::assertTrue($this->parser->parse('234'));
        self::assertEquals(234, $this->parser->getParsedValue());
        self::assertNull($this->parser->getErrmsg());
    }

    public function testFailure() : void
    {
        self::assertFalse($this->parser->parse('a234'));
        self::expectException(Error::class);
        $value = $this->parser->getParsedValue();
        self::assertInstanceOf(Msg::class, $this->parser->getErrmsg());
    }
}
