<?php
/**
 * @package Newscoop\CommunityTickerBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\CommunityTickerBundle\TemplateList;

use Newscoop\ListResult;
use Newscoop\TemplateList\BaseList;
use Newscoop\CommunityTickerBundle\Meta\MetaCommunityTicker;

/**
 * Community Feeds List
 */
class CommunityFeedsList extends BaseList 
{
    /**
     * Gets ListResult object with list elements
     * 
     * @param  Criteria $criteria
     * 
     * @return ListResult
     */
    protected function prepareList($criteria, $params)
    {   
        $service = \Zend_Registry::get('container')->get('newscoop_ticker_plugin.service');
        $lists = $service->findByCriteria($criteria);
        foreach ($lists as $key => $feed) {
            $lists->items[$key] = new MetaCommunityTicker($feed);
        }

        return $lists;
    }

    /**
     * Converts parameters array to Criteria
     * 
     * @param  integer  $firstResult
     * @param  array    $parameters
     * 
     * @return void
     */
    protected function convertParameters($firstResult, $parameters)
    {
        $this->criteria->orderBy = array();
        // run default simple parameters converting
        parent::convertParameters($firstResult, $parameters);

        if (array_key_exists('length', $parameters)) {
            $parameter = (int) $parameters['length'];
            if ($parameter < 0) {
                throw new \Exception("Invalid value of parameter \"length\" in statement list_community_feeds", 1);
            }

            $this->criteria->length = $parameter;
        }    
    }
}