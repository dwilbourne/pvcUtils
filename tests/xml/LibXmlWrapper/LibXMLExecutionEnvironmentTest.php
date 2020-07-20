<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\xml\LibXmlWrapper;

use DOMDocument;
use LibXMLError;
use PHPUnit\Framework\TestCase;
use pvc\xml\LibXmlWrapper\LibXmlExecutionEnvironment;

class LibXMLExecutionEnvironmentTest extends TestCase
{

    protected LibXmlExecutionEnvironment $env;

    public function setUp(): void
    {
        $this->env = new LibXmlExecutionEnvironment();
    }

    public function testValidatePreserveGlobalEnvironment() : void
    {
        error_reporting(E_NOTICE);
        libxml_use_internal_errors(false);

        $callable = function () {
        };
        $this->env->executeCallable($callable);

        static::assertSame(E_NOTICE, error_reporting());
        static::assertSame(false, libxml_use_internal_errors());
    }

    public function testHasNoErrors() : void
    {
        $callable = function () {
        };
        $this->env->executeCallable($callable);
        self::assertEmpty($this->env->getErrors());
    }

    public function testHasErrorsGetErrors() : void
    {
        $document = new DOMDocument();
        $badXml = 'invalid xml';
        $callable = function () use ($document, $badXml) {
            $document->loadXML($badXml);
        };

        $this->env->executeCallable($callable);
        self::assertTrue(0 < count($this->env->getErrors()));
        $errors = $this->env->getErrors();
        $firstError = $errors[0];
        self::assertTrue($firstError instanceof LibXMLError);
    }
}
