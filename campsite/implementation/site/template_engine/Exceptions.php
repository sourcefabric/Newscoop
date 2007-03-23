<?php
/**
 * @package Campsite
 */

final class InvalidFunctionException extends Exception {

    public function __construct($p_className, $p_function)
    {
        parent::__construct("$p_function() method is not available for the $p_className class", 0);
    } // fn __construct

} // class InvalidFunctionException

?>