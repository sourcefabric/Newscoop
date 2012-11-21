<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Search;

/**
 * Delete Command
 */
class DeleteCommand extends AbstractCommand
{
    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('"delete":{"number":%d}', $this->article->number);
    }
}
