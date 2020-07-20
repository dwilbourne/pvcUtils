<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\xml\LibXmlWrapper;

use LibXMLError;
use pvc\err\throwable\exception\pvc_exceptions\InvalidArrayValueException;
use pvc\err\throwable\exception\stock_rebrands\InvalidArgumentException;
use pvc\xml\LibXmlWrapper\LibXmlErrorHandler;
use PHPUnit\Framework\TestCase;
use pvc\xml\LibXmlWrapper\LibXmlException;
use stdClass;

class LibXmlErrorHandlerTest extends TestCase
{

    protected LibXmlErrorHandler $handler;
    protected LibXMLError $error_0;
    protected LibXMLError $error_1;
    protected LibXMLError $error_2;
    protected LibXMLError $error_with_unknown_error_level;
    protected array $errors;

    public function setUp(): void
    {
        $this->error_0 = new LibXMLError();
        $this->error_0->level = LIBXML_ERR_WARNING;
        $this->error_0->code = 5;
        $this->error_0->message = 'this is message 0';

        $this->error_1 = new LibXMLError();
        $this->error_1->level = LIBXML_ERR_FATAL;
        $this->error_1->code = 1;
        $this->error_1->message = 'this is message 1';

        $this->error_2 = new LibXMLError();
        $this->error_2->level = LIBXML_ERR_ERROR;
        $this->error_2->code = 4;
        $this->error_2->message = 'this is message 2';

        $this->error_with_unknown_error_level = new LibXMLError();
        $this->error_with_unknown_error_level->level = 47;
        $this->error_with_unknown_error_level->code = 4;
        $this->error_with_unknown_error_level->message = 'this is message unknown error level';

        $this->errors = [$this->error_0, $this->error_1, $this->error_2];
        $this->handler = new LibXmlErrorHandler();
        $this->handler->setErrors($this->errors);
    }

    public function testSetErrors() : void
    {
        $this->handler->setReportingLevel($this->handler::REPORT_ALL);
        self::assertEquals(3, count($this->handler->getReportableErrors()));
    }

    public function testSetErrorsException() : void
    {
        $notError = new stdClass();
        $this->errors[] = $notError;
        self::expectException(InvalidArrayValueException::class);
        $this->handler->setErrors($this->errors);
    }

    public function testSetGetErrorReportingLevel() : void
    {
        self::assertEquals(LibXmlErrorHandler::REPORT_ALL, $this->handler->getReportingLevel());
        $this->handler->setReportingLevel(LibXmlErrorHandler::REPORT_WARNINGS);
        self::assertEquals(LibXmlErrorHandler::REPORT_WARNINGS, $this->handler->getReportingLevel());
    }

    public function testSetBadErrorReportingLevel() : void
    {
        self::expectException(InvalidArgumentException::class);
        $this->handler->setReportingLevel(99);
    }

    public function testSetGetFailureThreshold() : void
    {
        self::assertEquals(LIBXML_ERR_WARNING, $this->handler->getFailureThreshold());
        $this->handler->setFailureThreshold(LIBXML_ERR_ERROR);
        self::assertEquals(LIBXML_ERR_ERROR, $this->handler->getFailureThreshold());
    }

    public function testSetFailureThresholdException() : void
    {
        self::expectException(InvalidArgumentException::class);
        $this->handler->setFailureThreshold(2002);
    }

    public function testErrorShouldBeReported() : void
    {
        $flags = LibXmlErrorHandler::REPORT_RECOVERABLE_ERRORS | LibXmlErrorHandler::REPORT_FATAL_ERRORS;
        $this->handler->setReportingLevel($flags);
        // this one is a warning
        self::assertFalse($this->handler->errorShouldBeReported($this->error_0));
        // this one is a fatal error
        self::assertTrue($this->handler->errorShouldBeReported($this->error_1));
        // this one is a recoverable error
        self::assertTrue($this->handler->errorShouldBeReported($this->error_2));

        self::assertTrue($this->handler->errorShouldBeReported($this->error_with_unknown_error_level));
    }

    public function testCreateException() : void
    {
        $exception = $this->handler->createException();
        self::assertTrue($exception instanceof LibXmlException, 'LibXmlException is created');
        /* phpstan cannot know that $exception is not null */
        /** @phpstan-ignore-next-line */
        $previous = $exception->getPrevious();
        self::assertTrue($previous instanceof LibXmlException, 'LibXmlException is created');
    }

    public function testGetErrorsAndCreateMsgCollectionWithAllErrors() : void
    {
        $this->handler->setReportingLevel($this->handler::REPORT_ALL);
        $collection = $this->handler->getMsgCollection();
        self::assertEquals(3, count($collection));
        self::assertEquals(3, count($this->handler->getReportableErrors()));
    }

    public function testCreateMsgCollectionWithFatalAndRecoverableErrors() : void
    {
        $flags = $this->handler::REPORT_FATAL_ERRORS | $this->handler::REPORT_RECOVERABLE_ERRORS;
        $this->handler->setReportingLevel($flags);
        $collection = $this->handler->getMsgCollection();
        self::assertEquals(2, count($collection));
        self::assertEquals(2, count($this->handler->getReportableErrors()));
    }

    public function testCreateMsgCollectionWithFatalErrors() : void
    {
        $flags = $this->handler::REPORT_FATAL_ERRORS;
        $this->handler->setReportingLevel($flags);
        $collection = $this->handler->getMsgCollection();
        self::assertEquals(1, count($collection));
        self::assertEquals(1, count($this->handler->getReportableErrors()));
    }
}
