<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Search;

use SimpleXmlElement;

/**
 * Delete Command
 */
class DeleteCommand extends AbstractCommand
{
    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return sprintf('"delete":%s', json_encode(array('id' => $this->article->number)));
    }
}
