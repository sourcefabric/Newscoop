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
    public function __toString()
    {
        return sprintf('"add":%s', json_encode(array('doc' => $this->formatDoc())));
    }

    /**
     * Format document
     *
     * @return array
     */
    private function formatDoc()
    {
        $doc = array();
        foreach ($this->article as $key => $val) {
            if ($val === null || (is_array($val) && empty($val))) {
                continue;
            } elseif ($val instanceof DateTime) {
                $val = gmdate(self::DATE_FORMAT, $val->getTimestamp());
            }

            $doc[$key] = $val;
        }

        return $doc;
    }
}
