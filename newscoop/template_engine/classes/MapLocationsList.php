<?php

require_once('ListObject.php');


/**
 * MapLocationsList class
 *
 */
class MapLocationsList extends ListObject
{
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
        $p_count = 0;

        if (!is_numeric($p_start)) {
            $p_start = 0;
        }
        $p_start = 0 + $p_start;
        if (0 > $p_start) {$p_start = 0;}

        if (!is_numeric($p_limit)) {
            $p_limit = 0;
        }
        $p_limit = 0 + $p_limit;
        if (0 > $p_limit) {$p_limit = 0;}

        $campsite = CampTemplate::singleton()->context();

        $mapLocationsList = $campsite->map_dynamic_points_objects;
        if (!is_array($mapLocationsList)) {
            return array();
        }
        if ($campsite->map_dynamic_tot_points) {
            $p_count = $campsite->map_dynamic_tot_points;
        }

        if ($p_limit) {
            $mapLocationsList = array_slice($mapLocationsList, $p_start, $p_limit);
        }
        if ($p_start && (!$p_limit)) {
            $mapLocationsList = array_slice($mapLocationsList, $p_start);
        }

        $metaLocationsList = array();
        foreach ($mapLocationsList as $location) {
            $metaLocationsList[] = new MetaMapLocation($location);
        }
        return $metaLocationsList;
    }

    /**
     * Processes list constraints passed in an array.
     *
     * @param array $p_constraints
     * @return array
     */
    protected function ProcessConstraints(array $p_constraints)
    {
        return array();
    }

    /**
     * Processes order constraints passed in an array.
     *
     * @param array $p_order
     * @return array
     */
    protected function ProcessOrder(array $p_order)
    {
        return array();
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
                    if ($parameter == 'length' || $parameter == 'columns') {
                        $intValue = (int)$value;
                        if ("$intValue" != $value || $intValue < 0) {
                            CampTemplate::singleton()->trigger_error("invalid value $value of parameter $parameter in statement list_article_locations");
                        }
                        $parameters[$parameter] = (int)$value;
                    } else {
                        $parameters[$parameter] = $value;
                    }
                    break;
                default:
                    CampTemplate::singleton()->trigger_error("invalid parameter $parameter in list_article_locations", $p_smarty);
            }
        }
        return $parameters;
    }
}

?>
