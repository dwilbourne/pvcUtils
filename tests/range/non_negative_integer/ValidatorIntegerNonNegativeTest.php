<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

namespace tests\range\non_negative_integer;

use pvc\msg\Msg;
use pvc\range\non_negative_integer\ValidatorIntegerNonNegative;
use PHPUnit\Framework\TestCase;

class ValidatorIntegerNonNegativeTest extends TestCase
{
    protected ValidatorIntegerNonNegative $validator;

    public function setUp() : void
    {
        $this->validator = new ValidatorIntegerNonNegative();
    }

    public function testNonInteger() : void
    {
        self::assertFalse($this->validator->validate('1e'));
        self::assertInstanceOf(Msg::class, $this->validator->getErrMsg());
    }

    public function testNegativeInteger() : void
    {
        self::assertFalse($this->validator->validate('-2'));
        self::assertInstanceOf(Msg::class, $this->validator->getErrMsg());
    }

    public function testNonNegativeInteger() : void
    {
        self::assertTrue($this->validator->validate(2));
        self::assertNull($this->validator->getErrMsg());
    }
}
