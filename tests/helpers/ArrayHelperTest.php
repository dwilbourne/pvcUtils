<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

namespace tests\helpers;

use pvc\helpers\ArrayHelper;
use PHPUnit\Framework\TestCase;
use stdClass;

class ArrayHelperTest extends TestCase
{
    protected ArrayHelper $helper;

    public function setUp() : void
    {
        $this->helper = new ArrayHelper();
    }

    /**
     * testArrayIsTwoDimensionalAtomicSquare
     */
    public function testArrayIsTwoDimensionalAtomicSquare() : void
    {
        $array = [1, 2, 3];
        self::assertFalse($this->helper::arrayIsTwoDimensionalAtomicSquare($array));

        $array = [
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9]
        ];
        self::assertTrue($this->helper::arrayIsTwoDimensionalAtomicSquare($array));

        $array = [
            [1, 2, 3],
            [4, 5, 6, 7],
            [8, 9, 10]
        ];
        self::assertFalse($this->helper::arrayIsTwoDimensionalAtomicSquare($array));

        $array = [
            [1, 2, 3],
            [4, 5, [6, 7]],
            [8, 9, 10]
        ];
        self::assertFalse($this->helper::arrayIsTwoDimensionalAtomicSquare($array));
    }

    /**
     * testArrayIsTwoDimensionalAtomic
     */
    public function testArrayIsTwoDimensionalAtomic() : void
    {
        $array = [1, 2, 3];
        self::assertFalse($this->helper::arrayIsTwoDimensionalAtomic($array));

        $array = [
            [1, 2, 3],
            [4, 5, 6]
        ];
        self::assertTrue($this->helper::arrayIsTwoDimensionalAtomic($array));

        $array = [
            [1, 2, 3],
            [4, 5, [6, 7]]
        ];
        self::assertFalse($this->helper::arrayIsTwoDimensionalAtomic($array));
    }

    /**
     * testGetArrayMaxDimensionsWithOneDimensionalArray
     */
    public function testGetArrayMaxDimensionsWithOneDimensionalArray() : void
    {
        $array = [1, 2, 3];
        self::assertEquals(1, $this->helper::getArrayMaxDimensions($array));
    }

    /**
     * testGetArrayMaxDimensionsWithThreeDimensionalArray
     */
    public function testGetArrayMaxDimensionsWithThreeDimensionalArray() : void
    {
        $array = [
            [1, 2, 3],
            [4, 5,
                [6, 7, 8]
            ]
        ];
        self::assertEquals(3, $this->helper::getArrayMaxDimensions($array));
    }

    /**
     * testArrayIsOneDimensionalAtomic
     */
    public function testArrayIsOneDimensionalAtomic() : void
    {
        $array = [1, 2, 3];
        self::assertTrue($this->helper::arrayIsOneDimensionalAtomic($array));
        $array = [1, 2, new stdClass()];
        self::assertFalse($this->helper::arrayIsOneDimensionalAtomic($array));
    }
}
