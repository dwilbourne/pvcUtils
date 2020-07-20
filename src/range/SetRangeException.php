<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\range;

use pvc\msg\ErrorExceptionMsg;
use pvc\err\throwable\exception\stock_rebrands\Exception;
use pvc\err\throwable\ErrorExceptionConstants as ec;
use Throwable;

/**
 * Class NumberRangeException
 */
class SetRangeException extends Exception
{
    /**
     * SetRangeException constructor.
     * @param string $patternDescription
     * @param int|string $providedRangeSpec
     * @param Throwable|null $previous
     */
    public function __construct(string $patternDescription, $providedRangeSpec, Throwable $previous = null)
    {
        $msgText = 'Invalid range specification:  pattern must be %s.  Spec provided = %s';
        $vars = [$patternDescription, $providedRangeSpec];
        $code = ec::SET_RANGE_EXCEPTION;
        $msg = new ErrorExceptionMsg($vars, $msgText);
        parent::__construct($msg, $code, $previous);
    }
}
