<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Search;

use DateTime;

/**
 * Add Command
 */
class AddCommand extends AbstractCommand
{
    const DATE_FORMAT = 'Y-m-d\TH:i:s\Z';

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('"add":{"doc":%s}', $this->formatArticle());
    }

    /**
     * Format article for solr index
     *
     * @param Newscoop\View\ArticleView $article
     * @return string
     */
    private function formatArticle()
    {
        $data = array();
        foreach ($this->article as $key => $val) {
            $data[$key] = $val;
            if ($val instanceof DateTime) {
                $data[$key] = gmdate(self::DATE_FORMAT, $val->getTimestamp());
            }
        }

        return json_encode((object) $data);
    }
}
