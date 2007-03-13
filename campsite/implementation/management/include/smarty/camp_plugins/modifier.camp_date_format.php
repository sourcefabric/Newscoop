<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite camp_date_format modifier plugin
 *
 * Type:     modifier
 * Name:     camp_date_format
 * Purpose:  format datestamps via MySQL date and time functions
 *
 * @param string
 *     $p_unixtime the date in unixtime format from $smarty.now
 * @param string
 *     $p_format the date format wanted
 *
 * @return
 *     string the formatted date
 *     null in case a non-valid format was passed
 */
function smarty_modifier_camp_date_format($p_unixtime, $p_format = null)
{
    global $g_ado_db;

    // TODO: We need to use the global language id variable instead.
    //       Hard coded so far.
    $langId = 3;

    if (is_null($p_format) || empty($p_format)) {
        $dbQuery = "SELECT FROM_UNIXTIME('".$p_unixtime."') AS date";
        $row = $g_ado_db->GetRow($dbQuery);
        return $row['date'];
    }

    $dbQuery =
        "SELECT FROM_UNIXTIME('".$p_unixtime."', '".$p_format."') AS date, "
        ."MONTH(FROM_UNIXTIME('".$p_unixtime."')) AS month, "
        ."DAYOFWEEK(FROM_UNIXTIME('".$p_unixtime."')) AS day";
    $row = $g_ado_db->GetRow($dbQuery);
    if (sizeof($row) < 1) {
        return null;
    }
    $formattedDate = $row['date'];

    $hasTxtMonth = (strpos($p_format, '%M') !== false) ? true : false;
    $hasTxtWDay = (strpos($p_format, '%W') !== false) ? true : false;
    if ($hasTxtMonth || $hasTxtWDay) {
        $dbQuery =
            "SELECT Month".$row['month']." AS month, "
            ."WDay".$row['day']." AS day "
            ." FROM Languages WHERE Id = 1 OR Id = ".$langId
            ." ORDER BY Id";
        $lang = $g_ado_db->GetAll($dbQuery);
        if (sizeof($lang) != 2) {
            return $formattedDate;
        }
        if ($hasTxtMonth) {
            $formattedDate = str_replace($lang[0]['month'], $lang[1]['month'], $formattedDate);
        }
        if ($hasTxtWDay) {
            $formattedDate = str_replace($lang[0]['day'], $lang[1]['day'], $formattedDate);
        }
    }

    return $formattedDate;
} // fn smarty_modifier_camp_date_format

?>