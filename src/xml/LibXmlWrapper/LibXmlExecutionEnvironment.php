<?php declare(strict_types = 1);
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

namespace pvc\xml\LibXmlWrapper;

/**
 * Class LibXmlExecutionEnvironment
 */
class LibXmlExecutionEnvironment
{
    /**
     * @var array
     */
    protected array $errors;

    /**
     *
     * Execute a callable ensuring that the execution will occur inside an environment
     * where libxml use internal errors is true.
     *
     * After executing the callable the value of libxml use internal errors is set to
     * previous value.
     * @function executeCallable
     * @param callable $callable
     * @param array $params
     * @return mixed
     */
    public function executeCallable(callable $callable, array $params = [])
    {
        $previousErrorReporting = error_reporting();
        error_reporting(0);

        // libxml_use_internal_errors(true) disables libxml from throwing errors and allows us to
        // retrieve them instead.  The function returns the prior setting.
        $previousLibXmlUseInternalErrors = libxml_use_internal_errors(true);
        libxml_clear_errors();

        $result = call_user_func_array($callable, $params);

        $this->errors = libxml_get_errors();

        error_reporting($previousErrorReporting);
        libxml_use_internal_errors($previousLibXmlUseInternalErrors);

        return $result;
    }

    /**
     * @function getErrors
     * @return array
     */
    public function getErrors() : array
    {
        return $this->errors;
    }

    /**
     * @function hasErrors
     * @return bool
     */
    public function hasErrors() : bool
    {
        return !empty($this->errors);
    }
}
