<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

namespace tests\helpers;

use pvc\helpers\DomEventHelper;
use PHPUnit\Framework\TestCase;
use pvc\msg\UserMsg;

class DomEventHelperTest extends TestCase
{
    protected DomEventHelper $helper;

    public function setup() : void
    {
        $this->helper = new DomEventHelper();
    }

    public function testIsEvent() : void
    {
        self::assertTrue(DomEventHelper::isEvent('dragenter'));
        self::assertTrue(DomEventHelper::isEvent('beforeunload'));
        self::assertFalse(DomEventHelper::isEvent('foo'));
    }

    public function testValidateInvalidEventNotAStringArgument() : void
    {
        $event = new \stdClass();
        self::assertFalse($this->helper->validate($event));
        self::assertInstanceOf(UserMsg::class, $this->helper->getErrMsg());
    }

    public function testValidateFailsOnInvalidEvent() : void
    {
        $event = 'foo';
        self::assertFalse($this->helper->validate($event));
        self::assertInstanceOf(UserMsg::class, $this->helper->getErrMsg());
    }

    public function testValidateSucceedsonValidEvent() : void
    {
        // events do not have the 'on' prefix
        $event = 'resize';
        self::assertTrue($this->helper->validate($event));
        self::assertNull($this->helper->getErrMsg());
    }
}
