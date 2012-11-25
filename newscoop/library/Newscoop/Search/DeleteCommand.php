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
    public function update(SimpleXmlElement $xml)
    {
        $delete = $xml->xpath('delete');
        $delete = !empty($delete) ? $delete[0] : $xml->addChild('delete');
        $delete->addChild('id', $this->article->number);
    }
}
