<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\xml\LibXmlWrapper;

use LibXMLError;
use pvc\msg\UserMsg;

/**
 * Class LibXMLErrorFrmtr
 */
class LibXmlMsg extends UserMsg
{
    /**
     * LibXmlMsg constructor.
     * @param LibXMLError $libXMLError
     */
    public function __construct(LibXMLError $libXMLError)
    {
        // LibXMLError->line appears not to exist despite the documentation
        $msgVars = [(string)$libXMLError->level, (string)$libXMLError->code, (string)$libXMLError->message];
        $msgText = 'Level = %s; Code = %s; Message = %s';
        parent::__construct($msgVars, $msgText);
    }
}
