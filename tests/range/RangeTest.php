<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\range;

use Mockery;
use pvc\parser\ParserInterface;
use pvc\range\non_negative_integer\ParserInteger;
use PHPUnit\Framework\TestCase;
use pvc\range\Range;
use pvc\range\SetRangeException;
use pvc\validator\base\ValidatorInterface;

class RangeTest extends TestCase
{

    /** @phpstan-ignore-next-line */
    protected $range;

    /** @phpstan-ignore-next-line */
    protected $parser;

    /** @phpstan-ignore-next-line */
    protected $validator;

    protected string $patternDescription;

    public function setUp(): void
    {
        $this->range = Mockery::mock(Range::class)->makePartial();

        $this->parser = Mockery::mock(ParserInterface::class);
        $this->range->setParser($this->parser);

        $this->validator = Mockery::mock(ValidatorInterface::class);
        $this->range->setValidator($this->validator);

        $this->patternDescription = 'This is a pattern description';
        $this->range->setPatternDescription($this->patternDescription);
    }

    public function testSetGetParser() : void
    {
        self::assertEquals($this->parser, $this->range->getParser());
    }

    public function testSetGetValidator() : void
    {
        self::assertEquals($this->validator, $this->range->getValidator());
    }

    public function testSetgetPatternDescription() : void
    {
        self::assertEquals($this->patternDescription, $this->range->getPatternDescription());
    }

    public function testAddItem() : void
    {
        $int = 5;
        $this->validator->shouldReceive('validate')->with($int)->andReturn(true);
        $this->parser->shouldReceive('parse')->with($int)->andReturn(true);
        $this->parser->shouldReceive('getParsedValue')->withNoArgs()->andReturn($int);

        $this->range->addItem($int);
        self::assertTrue(in_array($int, $this->range->getRange()));
    }

    public function testAddItemSetRangeException() : void
    {
        $int = 5;
        $this->validator->shouldReceive('validate')->with($int)->andReturn(false);
        self::expectException(SetRangeException::class);
        $this->range->addItem($int);
    }

    public function testAddRange() : void
    {
        $start = -3;
        $end = -7;

        $this->validator->shouldReceive('validate')->with($start)->andReturn(true);
        $this->validator->shouldReceive('validate')->with($end)->andReturn(true);
        $this->parser->shouldReceive('parse')->with($start)->andReturn(true);
        $this->parser->shouldReceive('parse')->with($end)->andReturn(true);
        $this->parser->shouldReceive('getParsedValue')->withNoArgs()->andReturn($start, $end);

        $this->range->addRange($start, $end);
        self::assertTrue(in_array(-5, $this->range->getRange()));
    }

    public function testAddRangeExceptionWithInvalidStartAndInvalidEnd() : void
    {
        $start = -3;
        $end = -7;

        $this->validator->shouldReceive('validate')->with($start)->andReturn(false);
        $this->validator->shouldReceive('validate')->with($end)->andReturn(false);
        self::expectException(SetRangeException::class);
        $this->range->addRange($start, $end);
    }

    public function testAddRangeExceptionWithInvalidEnd() : void
    {
        $start = -3;
        $end = -7;

        $this->validator->shouldReceive('validate')->with($start)->andReturn(true);
        $this->validator->shouldReceive('validate')->with($end)->andReturn(false);
        self::expectException(SetRangeException::class);
        $this->range->addRange($start, $end);
    }

    public function testAddStringItemToRangeWithNumber2() : void
    {
        $item = '2';
        $this->validator->shouldReceive('validate')->with($item)->andReturn(true);
        $this->parser->shouldReceive('parse')->with($item)->andReturn(true);
        $this->parser->shouldReceive('getParsedValue')->withNoArgs()->andReturn(2);
        $this->range->addStringItemToRange($item);
        self::assertTrue(in_array(2, $this->range->getRange()));
    }

    public function testAddStringItemToRangeExceptionWithInvalidStartAndInvalidEnd() : void
    {
        $item = '0-4';
        $this->validator->shouldReceive('validate')->with($item)->andReturn(true);
        $this->parser->shouldReceive('parse')->with($item)->andReturn(false);
        $this->parser->shouldReceive('parse')->with('0')->andReturn(false);
        self::expectException(SetRangeException::class);
        $this->range->addStringItemToRange($item);
    }

    public function testAddStringItemToRangeExceptionWithValidStartAndInvalidEnd() : void
    {
        $item = '0-4';
        $this->validator->shouldReceive('validate')->with($item)->andReturn(true);
        $this->parser->shouldReceive('parse')->with($item)->andReturn(false);
        $this->parser->shouldReceive('parse')->with('0')->andReturn(true);
        $this->parser->shouldReceive('getParsedValue')->withNoArgs()->andReturn(0);
        $this->parser->shouldReceive('parse')->with('4')->andReturn(false);
        self::expectException(SetRangeException::class);
        $this->range->addStringItemToRange($item);
    }

    public function testAddStringItemToRange() : void
    {
        $item = '0-4';
        $this->validator->shouldReceive('validate')->withAnyArgs()->andReturn(true);
        $this->parser->shouldReceive('parse')->with($item)->andReturn(false);
        $this->parser->shouldReceive('parse')->with('0')->andReturn(true);
        $this->parser->shouldReceive('parse')->with('4')->andReturn(true);
        $this->parser->shouldReceive('getParsedValue')->withNoArgs()->andReturn(0, 4);
        $this->range->addStringItemToRange($item);
        self::assertEquals(5, count($this->range->getRange()));
    }

    public function testAddStringItemToRangeExceptionWithInvalidRange() : void
    {
        $item = '0-';
        $this->parser->shouldReceive('parse')->with('0-')->andReturn(false);
        self::expectException(SetRangeException::class);
        $this->range->addStringItemToRange($item);
    }

    /** @phpstan-ignore-next-line */
    public function testSetRange()
    {
        $parser = new ParserInteger();
        $this->range->setParser($parser);
        $this->validator->shouldReceive('validate')->withAnyArgs()->andReturn(true);
        $spec = '2,4,7-9,11-13,8';
        $this->range->addRangeSpec($spec);
        self::assertTrue(in_array(2, $this->range->getRange()));
        self::assertTrue(in_array(4, $this->range->getRange()));
        self::assertTrue(in_array(8, $this->range->getRange()));
        self::assertTrue(in_array(13, $this->range->getRange()));
        // duplicate value (8) is not added a second time
        self::assertEquals(8, count($this->range->getRange()));

        return $this->range;
    }

    /**
     * @function testContainsValue
     * @param Range $range
     * @depends testSetRange
     */
    public function testContainsValue(Range $range) : void
    {
        self::assertTrue($range->containsValue(4));
        self::assertTrue($range->containsValue(2));
        self::assertTrue($range->containsValue(13));
    }

    public function testGetRangeSpecEmptyRange() : void
    {
        self::assertEquals('', $this->range->getRangeSpec());
    }

    public function testGetRangeSpecSingleIntegerRange() : void
    {
        $this->parser->shouldReceive('parse')->with('4')->andReturn(true);
        $this->validator->shouldReceive('validate')->withAnyArgs()->andReturn(true);
        $this->range->addItem(4);
        self::assertEquals('4', $this->range->getRangeSpec());
    }

    /**
     * @function testGetRangeSpec
     * @param Range $range
     * @depends testSetRange
     */
    public function testGetRangeSpecFromRange(Range $range) : void
    {
        $spec = '2,4,7-9,11-13';
        self::assertEquals($spec, $range->getRangeSpec());
    }

    public function testGetRangeSpecFromAnotherRange() : void
    {
        $parser = new ParserInteger();
        $this->range->setParser($parser);
        $this->validator->shouldReceive('validate')->withAnyArgs()->andReturn(true);
        $spec = '2,6-9,15-19,22';
        $this->range->addRangeSpec($spec);
        self::assertEquals($spec, $this->range->getRangeSpec());
    }
}
