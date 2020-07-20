<?php

namespace pvc\helpers;

use pvc\err\throwable\exception\pvc_exceptions\InvalidValueException;
use pvc\msg\ErrorExceptionMsg;

/**
 * class StringQuoteHelper.  Class of methods that can be called statically
 */
class StringQuoteHelper
{

    /**
     * isQuoted
     * @param string $string
     * @return bool
     */
    public static function isQuoted(string $string) : bool
    {
        $firstChar = substr($string, 0, 1);
        if ($firstChar != "'" && $firstChar != "\"") {
            return false;
        }

        $lastChar = substr($string, -1, 1);
        return ($firstChar != $lastChar ? false : true);
    }

    /**
     * unQuote
     * @param string $string
     * @return string
     */
    public static function unQuote(string $string) : string
    {
        return (!self::isQuoted($string) ? $string : substr($string, 1, -1));
    }

    /**
     * quote
     * @param string $string
     * @return string
     */
    public static function quote(string $string, string $quoteChar = "'") : string
    {
        if ($quoteChar != "'" && $quoteChar != "\"") {
            $msg = new ErrorExceptionMsg([], 'Invalid quote char specified - must be single or double quote.');
            throw new InvalidValueException($msg);
        }
        return (self::isQuoted($string) ? $string : $quoteChar . $string . $quoteChar);
    }
}
