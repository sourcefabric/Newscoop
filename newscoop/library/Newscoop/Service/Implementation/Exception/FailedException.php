<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service\Implementation\Exception;

/**
 * This exception is thrown when a service requirement fails, usually this type of exception only signals failing
 * without a specific message, the message will be logged under the error handler.
 */
use Newscoop\Utils\Validation;

class FailedException extends \Exception
{

}