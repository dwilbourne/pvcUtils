<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

namespace pvc\range\non_negative_integer;

use pvc\msg\Msg;
use pvc\msg\MsgRetrievalInterface;
use pvc\parser\ParserInterface;
use pvc\regex\numeric\RegexIntegerSimple;

/**
 * Class ParserInteger
 * @package pvc\range\non_negative_integer
 */
class ParserInteger implements ParserInterface
{
    protected int $parsedValue;
    protected ?Msg $errmsg;

    public function parse(string $data): bool
    {
        $regex = new RegexIntegerSimple();
        if ($regex->match($data)) {
            $this->parsedValue = $regex->getMatch(0);
            $this->errmsg = null;
            return true;
        } else {
            $msgText = 'value (%s) is not a simple integer.';
            $msgVars = [$data];
            $this->errmsg = new Msg($msgVars, $msgText);
            return false;
        }
    }

    public function getParsedValue()
    {
        return $this->parsedValue;
    }

    public function getErrmsg(): ?MsgRetrievalInterface
    {
        return $this->errmsg;
    }
}
