<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

namespace pvc\range\non_negative_integer;

use pvc\msg\Msg;
use pvc\msg\MsgRetrievalInterface;
use pvc\validator\base\ValidatorInterface;

/**
 * Class ValidatorIntegerNonNegative
 * @package pvc\range\non_negative_integer
 */
class ValidatorIntegerNonNegative implements ValidatorInterface
{
    protected Msg $errmsg;

    public function validate($data): bool
    {
        if (!is_integer($data)) {
            $msgText = 'data (%s) is not an integer.';
            $msgVars = [$data];
            $this->errmsg = new Msg($msgVars, $msgText);
            return false;
        }

        if ($data < 0) {
            $msgText = 'integer cannot be negative.';
            $msgVars = [];
            $this->errmsg = new Msg($msgVars, $msgText);
            return false;
        }

        return true;
    }

    public function getErrMsg(): ?MsgRetrievalInterface
    {
        return $this->errmsg ?? null;
    }
}
