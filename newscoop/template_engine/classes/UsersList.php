<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Users list
 */
class UsersList extends ListObject
{
    /** @var array */
    private static $s_orderFields = array(
        'default',
        'byusername',
        'byfirstname',
        'bylastname',
    );

    /** @var array */
    private $orderMapping = array(
        'byusername' => 'username',
        'byfirstname' => 'first_name',
        'bylastname' => 'last_name',
        'bycreated' => 'created',
    );

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
        $service = $GLOBALS['controller']->getHelper('service')->getService('user.list');
        $count = $service->countBy($this->m_constraints);

        $users = array();
        foreach ($service->findBy($this->m_constraints, $this->m_order, $p_limit, $p_start) as $user)
            $users[] = new MetaUser($user);
        }

        return $users;
	}

	/**
	 * Processes list constraints passed in an array.
	 *
	 * @param array $p_constraints
	 * @return array
	 */
	protected function ProcessConstraints(array $p_constraints)
	{
        return $p_constraints + array(
            'is_public' => true,
        );
	}

	/**
	 * Processes order constraints passed in an array.
	 *
	 * @param array $p_order
	 * @return array
	 */
	protected function ProcessOrder(array $p_order)
	{
        $order = array();
        for ($i = 0; $i < count($p_order); $i++) {
            if (in_array(strtolower($p_order[$i]), array('asc', 'desc'))) {
                continue;
            }

            if (!isset($this->orderMapping[$p_order[$i]])) {
                continue;
            }

            $dir = 'asc';
            if (isset($p_order[$i + 1]) && strtolower($p_order[$i + 1]) == 'desc') {
                $dir = 'desc';
            }

            $field = $this->orderMapping[$p_order[$i]];
            $order[$field] = $dir;
        }

        return $order;
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
	            case 'columns':
	            case 'name':
	            case 'order':
	                if ($parameter == 'length' || $parameter == 'columns') {
	                    $intValue = (int)$value;
	                    if ("$intValue" != $value || $intValue < 0) {
	                        CampTemplate::singleton()->trigger_error("invalid value $value of parameter $parameter in statement list_article_authors");
	                    }
	                    $parameters[$parameter] = (int) $value;
	                } else {
	                    $parameters[$parameter] = $value;
	                }
	                break;
	            default:
	                CampTemplate::singleton()->trigger_error("invalid parameter $parameter in list_article_authors", $p_smarty);
	        }
	    }

		return $parameters;
	}
}
