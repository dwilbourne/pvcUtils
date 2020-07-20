<?php declare(strict_types = 1);
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

namespace pvc\xml\LibXmlWrapper;

use LibXMLError;
use pvc\err\throwable\exception\pvc_exceptions\InvalidArrayValueException;
use pvc\err\throwable\exception\stock_rebrands\InvalidArgumentException;
use pvc\err\throwable\exception\stock_rebrands\InvalidArgumentMsg;
use pvc\msg\ErrorExceptionMsg;
use pvc\msg\UsrMsgCollection;

/**
 * Class LibXmlErrorHandler
 *
 * It would have been really nice if the error constants from the LibXML library make a nice bitmask themselves
 * in order to facilitate error reporting levels (as is done in other parts of PHP).  In this case we set up
 * our own bitmask using class constants to set the error reporting level.  This can lead to some confusion
 * because error reporting level is set using class constants from this class and 'failureThreshold' is
 * set using the LibXML error constants which appear as error levels in the errors themselves.
 */

class LibXmlErrorHandler
{
    /**
     * @var array
     */
    protected array $errors;

    /**
     * @var int
     */
    protected int $reportingLevel;

    /**
     * @var int
     */
    protected int $failureThreshold;

    public const REPORT_FATAL_ERRORS = 4;
    public const REPORT_RECOVERABLE_ERRORS = 2;
    public const REPORT_WARNINGS = 1;
    public const REPORT_ALL = 7;

    /**
     * LibXmlErrorHandler constructor.
     * @throws InvalidArgumentException
     */
    public function __construct()
    {

        // report all errors by default
        $this->setReportingLevel(self::REPORT_ALL);

        // strict threshold - any sort of warning or error triggers a failure condition
        $this->setFailureThreshold(LIBXML_ERR_WARNING);
    }

    /**
     * @function setErrors
     * @param array $errors
     * @throws InvalidArrayValueException
     */
    public function setErrors(array $errors) : void
    {
        foreach ($errors as $error) {
            if (!$error instanceof LibXMLError) {
                $msgText = 'Expected array value to be of type LibXmlError.';
                $msg = new ErrorExceptionMsg([], $msgText);
                throw new InvalidArrayValueException($msg);
            }
        }
        $this->errors = $errors;
    }

    /**
     * @function getErrors
     * @return array
     */
    public function getErrors() : array
    {
        return $this->errors;
    }

    /**
     * @function setReportingLevel
     * @param int $flags
     * @throws InvalidArgumentException
     */
    public function setReportingLevel(int $flags) : void
    {
        if ($flags > 7 || $flags < 1) {
            $addtlMsg = 'Value must be between 1 and 7 (bitwise OR of error constants from this class)';
            $msg = new InvalidArgumentMsg('integer', $addtlMsg);
            throw new InvalidArgumentException($msg);
        }
        $this->reportingLevel = $flags;
    }

    /**
     * @function getReportingLevel
     * @return int
     */
    public function getReportingLevel() : int
    {
        return $this->reportingLevel;
    }

    /**
     * @function setFailureThreshold
     * @param int $failureThreshold
     * @throws InvalidArgumentException
     */
    public function setFailureThreshold(int $failureThreshold) : void
    {
        switch ($failureThreshold) {
            case LIBXML_ERR_WARNING:
            case LIBXML_ERR_ERROR:
            case LIBXML_ERR_FATAL:
                $this->failureThreshold = $failureThreshold;
                break;
            default:
                $addtlMsg = 'Value must be one of the LIBXML error constants (warning, error or fatal)';
                $msg = new InvalidArgumentMsg('integer', $addtlMsg);
                throw new InvalidArgumentException($msg);
        }
    }

    /**
     * @function getFailureThreshold
     * @return int
     */
    public function getFailureThreshold() : int
    {
        return $this->failureThreshold;
    }

    /**
     * @function errorShouldBeReported
     * @param LibXMLError $error
     * @return bool
     */
    public function errorShouldBeReported(LibXMLError $error) : bool
    {
        switch ($error->level) {
            case LIBXML_ERR_WARNING:
                return (0 < (self::REPORT_WARNINGS & $this->reportingLevel));
            case LIBXML_ERR_ERROR:
                return (0 < (self::REPORT_RECOVERABLE_ERRORS & $this->reportingLevel));
            case LIBXML_ERR_FATAL:
                return (0 < (self::REPORT_FATAL_ERRORS & $this->reportingLevel));
            default:
                return true;
        }
    }

    /**
     * @function errorsExceedThreshold
     * @return bool
     */
    public function errorsExceedThreshold() : bool
    {
        foreach ($this->errors as $error) {
            if ($error->level >= $this->failureThreshold) {
                return true;
            }
        }
        return false;
    }

    /**
     * @function createException
     * @return LibXmlException|null
     */
    public function createException() : ? LibXmlException
    {
        $previous = null;
        // make a copy of the errors attribute
        $errors = $this->errors;
        while ($error = array_pop($errors)) {
            $previous = new LibXmlException($error, $previous);
        }
        return $previous;
    }

    /**
     * @function getMsgCollection
     * @return UsrMsgCollection
     */
    public function getMsgCollection(): UsrMsgCollection
    {
        $result = new UsrMsgCollection();
        foreach ($this->errors as $error) {
            if ($this->errorShouldBeReported($error)) {
                $result->addMsg(new LibXmlMsg($error));
            }
        }
        return $result;
    }

    /**
     * @function getReportableErrors
     * @return array
     */
    public function getReportableErrors() : array
    {

        $result = [];
        foreach ($this->errors as $error) {
            if ($this->errorShouldBeReported($error)) {
                $result[] = $error;
            }
        }
        return $result;
    }
}
