<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\range;

use pvc\parser\ParserInterface;
use pvc\validator\base\ValidatorInterface;

abstract class Range
{
    /**
     * @var ParserInterface
     */
    protected ParserInterface $rangeElementParser;

    /**
     * @var ValidatorInterface
     */
    protected ValidatorInterface $rangeElementValidator;

    /**
     * @var array[int]mixed
     */
    protected array $range = [];

    /**
     * @var string
     */
    protected string $patternDescription;

    /**
     * @function setValidator
     * @param ValidatorInterface $validator
     */
    public function setValidator(ValidatorInterface $validator) : void
    {
        $this->rangeElementValidator = $validator;
    }

    /**
     * @function getValidator
     * @return ValidatorInterface
     */
    public function getValidator(): ValidatorInterface
    {
        return $this->rangeElementValidator;
    }

    /**
     * @function getParser
     * @return ParserInterface
     */
    public function getParser(): ParserInterface
    {
        return $this->rangeElementParser;
    }

    /**
     * @function setParser
     * @param ParserInterface $parser
     */
    public function setParser(ParserInterface $parser): void
    {
        $this->rangeElementParser = $parser;
    }

    /**
     * @function getPatternDescription
     * @return string
     */
    public function getPatternDescription(): string
    {
        return $this->patternDescription;
    }

    /**
     * @function setPatternDescription
     * @param string $description
     */
    public function setPatternDescription(string $description): void
    {
        $this->patternDescription = $description;
    }

    /**
     * @function addRangeSpec
     * @param string $rangeSpec
     * @throws SetRangeException
     */
    public function addRangeSpec(string $rangeSpec) : void
    {
        $rangeShell = explode(',', $rangeSpec);
        foreach ($rangeShell as $item) {
            $this->addStringItemToRange($item);
        }
    }

    /**
     * @function getRange
     * @return array|array[]
     */
    public function getRange()
    {
        return $this->range;
    }

    /**
     * @function addStringItemToRange
     * @param string $item
     * @throws SetRangeException
     */
    public function addStringItemToRange(string $item) : void
    {
        if ($this->rangeElementParser->parse($item)) {
            $this->addItem($this->rangeElementParser->getParsedValue());
            return;
        }

        if (preg_match('/^(.+)-(.+)$/', $item, $matches)) {
            if (false === $this->rangeElementParser->parse($matches[1])) {
                throw new SetRangeException($this->patternDescription, $item);
            }
            $start = $this->rangeElementParser->getParsedValue();

            if (false === $this->rangeElementParser->parse($matches[2])) {
                throw new SetRangeException($this->patternDescription, $item);
            }
            $end = $this->rangeElementParser->getParsedValue();

            $this->addRange($start, $end);
            return;
        }
        throw new SetRangeException($this->patternDescription, $item);
    }

    /**
     * @function addItem
     * @param int $i
     * @throws SetRangeException
     */
    public function addItem(int $i) : void
    {
        if (!$this->rangeElementValidator->validate($i)) {
            throw new SetRangeException($this->patternDescription, $i);
        }
        if (!in_array($i, $this->range)) {
            $this->range[] = $i;
        }
    }

    /**
     * @function addRange
     * @param int $start
     * @param int $end
     * @throws SetRangeException
     */
    public function addRange(int $start, int $end) : void
    {
        if (!$this->rangeElementValidator->validate($start)) {
            throw new SetRangeException($this->patternDescription, $start);
        }
        if (!$this->rangeElementValidator->validate($end)) {
            throw new SetRangeException($this->patternDescription, $end);
        }
        $this->range = array_unique(array_merge($this->range, range($start, $end)));
    }

    /**
     * @function containsValue
     * @param int $value
     * @return bool
     */
    public function containsValue(int $value)
    {
        return in_array($value, $this->range);
    }

    /**
     * @function getRangeSpec
     * @return string
     */
    public function getRangeSpec(): string
    {
        $resultArray = [];

        // end cases
        if (empty($this->range)) {
            return '';
        }
        if (count($this->range) == 1) {
            return (string) $this->range[0];
        }

        // sort range array into ascending values
        asort($this->range);

        // set up pointers to drag through the array
        $arrayLength = count($this->range);
        $beginningOfRange = $this->range[0];
        $previousValue = $this->range[0];

        for ($i = 1; $i < $arrayLength; $i++) {
            if ($this->range[$i] != $previousValue + 1) {
                // previous value is the end of the prior range so add the prior range to the result array
                if ($beginningOfRange == $previousValue) {
                    $resultArray[] = $previousValue;
                } else {
                    $resultArray[] = $beginningOfRange . '-' . $previousValue;
                }
                // start a new range
                $beginningOfRange = $this->range[$i];
            }
            $previousValue = $this->range[$i];
        }
        // add the last range
        if ($beginningOfRange == $previousValue) {
            $resultArray[] = $previousValue;
        } else {
            $resultArray[] = $beginningOfRange . '-' . $previousValue;
        }

        // convert the result array to a string
        return implode(',', $resultArray);
    }
}
