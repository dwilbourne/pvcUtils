<?php declare(strict_types = 1);

namespace pvc\xml\LibXmlWrapper;

use Exception;
use LibXMLError;
use Throwable;
use pvc\err\throwable\ErrorExceptionConstants as ec;

/**
 * Class LibXmlException
 */
class LibXmlException extends Exception
{
    /**
     * LibXmlException constructor.
     * @param LibXMLError $error
     * @param Throwable|null $previous
     */
    public function __construct(LibXMLError $error, Throwable $previous = null)
    {
        $code = ec::LIB_XML_EXCEPTION;
        parent::__construct($error->message, $code, $previous);
    }
}
