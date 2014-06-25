<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Exception;

use Newscoop\NewscoopException;

/**
 * Empty resource exception
 */
class ResourceIsEmptyException extends NewscoopException
{
    /**
     * Constructor.
     *
     * @param string $message The internal exception message
     */
    public function __construct($message = null)
    {
        parent::__construct($message, 204);
    }
}
