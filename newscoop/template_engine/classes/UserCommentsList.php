<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * UserComments list
 */
class UserCommentsList extends ListObject
{
    /** @var array */
    private static $s_orderFields = array(
        'bydate',
    );

    /** @var array */
    private $orderMapping = array(
        'bydate' => 'time_created'
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
	    $comments = array();

        if(!isset( $p_parameters['commenters'])) {
            return $comments;
	    }

        $comment_service = \Zend_Registry::get('container')->getService('comment');

        foreach ($comment_service->findUserComments($p_parameters, $this->m_order, $p_limit, $p_start) as $comment) {
            $comments[] = new MetaComment($comment->getId());
        }

        $p_count = is_array($comments) ? count($comments) : 0;

        return $comments;
	}

	/**
	 * Processes list constraints passed in an array.
	 *
	 * @param array $p_constraints
	 * @return array
	 */
	protected function ProcessConstraints(array $p_constraints)
	{
        return $p_constraints;
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
        $user_service = \Zend_Registry::get('container')->getService('user');

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
	                        throw new \InvalidArgumentException("CommentsList Property '$parameter' invalid");
	                    }
	                    $parameters[$parameter] = (int) $value;
	                } else {
	                    $parameters[$parameter] = $value;
	                }
	                break;
	            case 'user':
	                $user = $user_service->find((int)$value);
	                $commenter_ids = array();

	                foreach ($user->getCommenters() as $commenter) {
	                    $commenter_ids[] = $commenter->getId();
	                }
	                if (count($commenter_ids) > 0) {
	                   $parameters['commenters'] = $commenter_ids;
	                }
                    break;
                case 'status':
                    $validValues = \Newscoop\Entity\Comment::$status_enum;

                    if (is_array($value)) {
                        foreach ($value AS $parameterKey => $parameterValue) {
                            if (!in_array($parameterValue, $validValues)) {
                                throw new \InvalidValueException("CommentsList Property '$parameter' has invalid value");
                            } else {
                                $value[$parameterKey] = array_search($parameterValue, $validValues);
                            }
                        }

                        $parameters[$parameter] = $value;

                    } elseif (!in_array($value, $validValues)) {
                        throw new \InvalidValueException("CommentsList Property '$parameter' has invalid value");
                    } else {
                        $parameters[$parameter] = array(array_search($value, $validValues));
                    }

                    break;
	            default:
	                throw new \InvalidArgumentException("CommentsList Property '$parameter' invalid");
	        }
	    }

		return $parameters;
	}
}
