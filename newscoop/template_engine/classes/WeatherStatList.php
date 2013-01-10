<?php

require_once('ListObject.php');
require_once($GLOBALS['g_campsiteDir'] . '/classes/CampCache.php');

use Newscoop\Entity\WeatherStat;

/**
 * WeatherStatList class
 *
 */
class WeatherStatList extends ListObject
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
        $doctrine = Zend_Registry::get('doctrine');
        if (!$doctrine) {
            return false;
        }

        $repo = $doctrine->getEntityManager()->getRepository('Newscoop\Entity\WeatherStat');
        /* @var $repo \Newscoop\Entity\Repository\WeatherStatRepository */

        // get weather stats
        $criteria = array();
        if (isset($p_parameters['location_name']) && trim($p_parameters['location_name'])!="") {
            $criteria = array_merge($criteria, array( "locationName" => $p_parameters['location_name'] ));
        }
        if (isset($p_parameters['location_id']) && trim($p_parameters['location_id'])!="") {
            $criteria = array_merge($criteria, array( "locationId" => $p_parameters['location_id'] ));
        }
        if (isset($p_parameters['location_list']) && trim($p_parameters['location_list'])!="") {
            $criteria = array_merge($criteria, array( "locationList" => $p_parameters['location_list'] ));
        }
        if (isset($p_parameters['region_name']) && trim($p_parameters['region_name'])!="") {
            $criteria = array_merge($criteria, array( "regionName" => $p_parameters['region_name'] ));
        }
        if (isset($p_parameters['hour']) && trim($p_parameters['hour'])!="") {
            $criteria = array_merge($criteria, array( "hour" => $p_parameters['hour'] ));
        } else {
            // use current hour
            $hour = date('G');
            $criteria = array_merge($criteria, array( "hour" => $hour ));
        }

        $weatherStats = $repo->findBy($criteria);

        $length = null;
        if (isset($p_parameters['length']) && trim($p_parameters['length'])!="") {
            $length = $p_parameters['length'];
        }
        $start = null;
        if (isset($p_parameters['start']) && trim($p_parameters['start'])!="") {
            $start = $p_parameters['start'];
        }

        $weatherStatList = array();
        $count = 0;
        foreach ($weatherStats as $stat) {
            if ((count($weatherStatList) < $length) || (!isset($length))){
                if ((isset($start)) && ($count < $start)) {
                    $count++;
                    continue; 
                } else {
                    $metaWeatherStat = new MetaWeatherStat($stat->getId());
                    $weatherStatList[] = $metaWeatherStat;
                }
            }
        }

        $p_count = count($weatherStats);
        return $weatherStatList;
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
        return $p_order;
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
        foreach ($p_parameters as $parameter=>$value)
        {
            switch (($parameter = strtolower($parameter)))
            {
                case 'start' :
                case 'length' :
                case 'hour':
                    $intValue = (int)$value;
                    if ("$intValue" != $value || $intValue < 0) {
                        CampTemplate::singleton()->trigger_error("invalid value $value of parameter $parameter in statement list_weather_stats");
                    }
                    $parameters[$parameter] = (int)$value;
                    break;
                case 'location_list' :
                case 'location_name' :
                case 'region_name' :
                    $parameters[$parameter] = (string)$value;
                    break;
                case 'location_id' :
                    $parameters[$parameter] = (int)$value;
                    break;
            }
        }
        return $parameters;
    }
}

