<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\array_utils\fixtures;

use Iterator;

/**
 * Class CartesianProductTestObjectFixture
 */
class CartesianProductTestObjectFixture implements Iterator
{
    protected array $array;
    private int $pos;

    public function __construct()
    {
        $this->array = ['!', '@'];
        $this->pos = 0;
    }

    /**
     * @function current
     * @return mixed
     */
    public function current()
    {
        return $this->array[$this->pos];
    }

    /**
     * @function next
     */
    public function next() : void
    {
        $this->pos++;
    }

    /**
     * @function key
     * @return int
     */
    public function key() : int
    {
        return $this->pos;
    }

    /**
     * @function valid
     * @return bool
     */
    public function valid() : bool
    {
        return isset($this->array[$this->pos]);
    }

    /**
     * @function rewind
     */
    public function rewind() : void
    {
        $this->pos = 0;
    }
}
