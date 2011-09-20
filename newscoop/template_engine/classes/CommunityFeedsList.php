<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Community Feeds list
 */
class CommunityFeedsList extends ListObject
{
    /** @var int */
    private $user;

    /**
     * @param int $start
     * @param array $params
     */
    public function __construct($start, $params)
    {
        if (isset($params['user'])) {
            $this->user = (int) $params['user'];
        }

        parent::__construct($start, $params);
    }

	/**
	 * Creates the list of objects. Sets the parameter $p_hasNextElements to
	 * true if this list is limited and elements still exist in the original
	 * list (from which this was truncated) after the last element of this
	 * list.
	 *
	 * @param int $p_start
	 * @param int $p_limit
	 * @param array $p_parameters
	 * @param int &$p_count
	 * @return array
	 */
	protected function CreateList($p_start = 0, $p_limit = 0, array $p_parameters, &$p_count)
	{
        $service = $GLOBALS['controller']->getHelper('service')->getService('community_feed');
        $count = $service->countBy($this->m_constraints);

        $feeds = array();
        foreach ($service->findBy($this->m_constraints, $this->m_order, $p_limit, $p_start) as $feed) {
            $feeds[] = new MetaCommunityFeed($feed);
        }

        return $feeds;
	}

	/**
	 * Processes list constraints passed in an array.
	 *
	 * @param array $p_constraints
	 * @return array
	 */
	protected function ProcessConstraints(array $p_constraints)
	{
        return $this->user ? array('user' => $this->user) : array();
	}

	/**
	 * Processes order constraints passed in an array.
	 *
	 * @param array $p_order
	 * @return array
	 */
	protected function ProcessOrder(array $p_order)
	{
        return array('id' => 'desc');
    }

	/**
	 * Processes the input parameters passed in an array; drops the invalid
	 * parameters and parameters with invalid values. Returns an array of
	 * valid parameters.
	 *
	 * @param array $p_parameters
	 * @return array
	 */
	protected function ProcessParameters(array $p_parameters)
	{
	    $parameters = array();
	    foreach ($p_parameters as $parameter => $value) {
	        $parameter = strtolower($parameter);
	        switch ($parameter) {
	            case 'length':
	                $intValue = (int) $value;
	                if ("$intValue" != $value || $intValue < 0) {
	                    CampTemplate::singleton()->trigger_error("invalid value $value of parameter $parameter in statement list_article_authors");
	                }

	                $parameters[$parameter] = (int) $value;
	                break;

	            default:
	                CampTemplate::singleton()->trigger_error("invalid parameter $parameter in list_article_authors", $p_smarty);
	        }
	    }

		return $parameters;
	}
}
