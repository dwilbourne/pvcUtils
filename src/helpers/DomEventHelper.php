<?php

namespace pvc\helpers;

use pvc\msg\UserMsg;
use pvc\msg\UserMsgInterface;
use pvc\validator\base\ValidatorInterface;

/**
 *
 * class DOMEventHelper.  Assists in working with DOM events.
 *
 */
class DomEventHelper implements ValidatorInterface
{

    protected UserMsgInterface $errmsg;

    /**
     * @var array $domEventList.  list of valid Dom events
     */

    protected static array $domEventList = [
        "abort",
        "afterprint",
        "animationend",
        "animationiteration",
        "animationstart",
        "beforeprint",
        "beforeunload",
        "blur",
        "canplay",
        "canplaythrough",
        "change",
        "click",
        "contextmenu",
        "copy",
        "cut",
        "dblclick",
        "drag",
        "dragend",
        "dragenter",
        "dragleave",
        "dragover",
        "dragstart",
        "drop",
        "durationchange",
        "ended",
        "error",
        "focus",
        "focusin",
        "focusout",
        "fullscreenchange",
        "fullscreenerror",
        "hashchange",
        "input",
        "invalid",
        "keydown",
        "keypress",
        "keyup",
        "load",
        "loadeddata",
        "loadedmetadata",
        "loadstart",
        "message",
        "mousedown",
        "mouseenter",
        "mouseleave",
        "mousemove",
        "mouseover",
        "mouseout",
        "mouseup",
        // "mousewheel", deprecated - use wheel event instead
        "offline",
        "online",
        "open",
        "pagehide",
        "pageshow",
        "paste",
        "pause",
        "play",
        "playing",
        "popstate",
        "progress",
        "ratechange",
        "resize",
        "reset",
        "scroll",
        "search",
        "seeked",
        "seeking",
        "select",
        "show",
        "stalled",
        "storage",
        "submit",
        "suspend",
        "timeupdate",
        "toggle",
        "touchcancel",
        "touchend",
        "touchmove",
        "touchstart",
        "transitionend",
        "unload",
        "volumechange",
        "waiting"
    ];

    /**
     * @function isEvent boolean. Returns whether a given string is a DOM event or not
     * @return bool
     * @var string $event .
     */

    public static function isEvent(string $eventName): bool
    {
        $key = array_search($eventName, self::$domEventList);
        return ($key === false ? false : true);
    }

    public function validate($data): bool
    {
        unset($this->errmsg);
        if (!is_string($data)) {
            $msgText = 'argument passed to validate method must be a string.';
            // do not pass event name to errmsg if it is not a string.
            $this->setErrMsg($msgText);
            return false;
        }
        $result = self::isEvent($data);
        if (!$result) {
            $msgText = "%s is not a valid javascript event name.";
            $this->setErrMsg($msgText, $data);
            return false;
        }
        return true;
    }

    protected function setErrMsg(string $msgText, string $eventName=null) : void
    {
        $msgVars = [$eventName ?: $eventName];
        $msg = new UserMsg($msgVars, $msgText);
        $this->errmsg = $msg;
    }

    public function getErrMsg(): ?UserMsgInterface
    {
        return $this->errmsg ?? null;
    }
}
