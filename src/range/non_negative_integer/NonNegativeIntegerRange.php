<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\range\non_negative_integer;

use pvc\range\Range;

/**
 * Class NonNegativeIntegerRange
 */
class NonNegativeIntegerRange extends Range
{
    public function __construct()
    {
        $v = new ValidatorIntegerNonNegative();
        $this->setValidator($v);
        $p = new ParserInteger();
        $this->setParser($p);
        $rangeDescription = 'must be comma separated non-negative integers or subranges ';
        $rangeDescription .= '(e.g. n-m where n and m are non-negative integers)';
        $this->setPatternDescription($rangeDescription);
    }
}
