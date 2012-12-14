<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Search;

use DateTime;
use SimpleXmlElement;

/**
 * Add Command
 */
class AddCommand extends AbstractCommand
{
    const DATE_FORMAT = 'Y-m-d\TH:i:s\Z';

    /**
     * @inheritdoc
     */
    public function update(SimpleXmlElement $xml)
    {
        $add = $xml->xpath('add');
        $add = !empty($add) ? $add[0] : $xml->addChild('add');
        $doc = $add->addChild('doc');
        $this->addFields($doc);
    }

    /**
     * Add article fields
     *
     * @param SimpleXmlElement $doc
     * @return void
     */
    private function addFields(SimpleXmlElement $doc)
    {
        foreach ($this->article as $key => $val) {
            if ($val === null) {
                continue;
            }

            if ($val instanceof DateTime) {
                $val = gmdate(self::DATE_FORMAT, $val->getTimestamp());
            }

            foreach ((array) $val as $value) {
                $field = $doc->addChild('field', $value);
                $field->addAttribute('name', $key);
            }
        }
    }
}
