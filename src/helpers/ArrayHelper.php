<?php

namespace pvc\helpers;

/**
 *
 * @class ArrayHelper.  Provides a set of helper utilities to be able to inspect and manipulate arrays beyond
 * the core functions provided with php
 *
 */
class ArrayHelper
{

    /**
     * @function getArrayMaxDimensions integer.  Returns the largest number of dimensions of an array.
     * @param array $array.  Array to be tested.
     *
     * There's really no efficient way to do this and be sure you are correct - you must inspect all elements
     * in the array.  So for large arrays this function will take some time to execute.
     * @return int
     */

    public static function getArrayMaxDimensions(array $array) : int
    {
        $n[] = 1;
        foreach ($array as $arrayElement) {
            if (is_array($arrayElement)) {
                $n[] = 1 + self::getArrayMaxDimensions($arrayElement);
            }
        }
        return max($n);
    }

    /**
     * isAtomic
     * @param mixed $x
     * @return bool
     */
    protected static function isAtomic($x) : bool
    {
        return (is_integer($x) || is_float($x) || is_string($x) || is_bool($x));
    }

    /**
     * @function arrayIsOneDimensionalAtomic boolean.  Returns true if each element of the array is 'atomic' in nature.
     * 'Atomic' means must be of type integer, float, string or boolean.
     *
     * $var array array.  Array to be tested.
     * @param array $array
     * @return bool
     */

    public static function arrayIsOneDimensionalAtomic(array $array)
    {
        foreach ($array as $arrayElement) {
            if (!self::isAtomic($arrayElement)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @function arrayIsTwoDimensionalAtomic boolean.  Returns true if each element of the array is a one dimensional
     * atomic array
     * @param array $array
     *
     * $var array array.  Array to be tested.
     * @return bool
     */

    public static function arrayIsTwoDimensionalAtomic(array $array): bool
    {
        foreach ($array as $arrayElement) {
            if (!is_array($arrayElement) || !self::arrayIsOneDimensionalAtomic($arrayElement)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @function arrayIsTwoDimensionalAtomic boolean.  Returns true if each element of the array is a one
     * dimensional atomic array
     * @param array $array
     *
     * $var array array.  Array to be tested.
     * @return bool
     */

    public static function arrayIsTwoDimensionalAtomicSquare(array $array): bool
    {
        $firstElement = $array[0];
        if (!is_array($firstElement)) {
            return false;
        }
        $arrayWidth = count($firstElement);
        foreach ($array as $arrayElement) {
            if (!self::arrayIsOneDimensionalAtomic($arrayElement)) {
                return false;
            }
            if ($arrayWidth != count($arrayElement)) {
                return false;
            }
        }
        return true;
    }
}
