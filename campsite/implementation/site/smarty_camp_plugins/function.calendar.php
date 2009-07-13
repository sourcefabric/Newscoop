<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite calendar function plugin
 *
 * Type:     function
 * Name:     calendar
 * Purpose:  
 *
 * @param array
 *     $p_params List of parameters from template
 * @param object
 *     $p_smarty Smarty template object
 *
 * @return
 *     string The html content
 */
function smarty_function_calendar($p_params, &$p_smarty)
{
    global $g_ado_db;

    if (!isset($p_params['container']) || !isset($p_params['url'])) {
        return;
    }

    // gets the context variable
    $campsite = $p_smarty->get_template_vars('campsite');
    $html = '';

    // gets parameters
    $container = $p_params['container'];
    $url = $p_params['url'];
    $style = (isset($p_params['style'])) ? $p_params['style'] : 'yui-skin-sam';
    $calendarType = (isset($p_params['clickable_dates'])) ? $p_params['clickable_dates'] : 'issues';

    // gets dates
    switch($calendarType) {
    case 'articles':
        $sql = 'SELECT PublishDate FROM Articles WHERE IdPublication = '
	    . $campsite->publication->identifier . ' AND Published = \'Y\' '
	    . 'GROUP BY PublishDate ORDER BY PublishDate';
	$dateField = 'PublishDate';
	break;
    case 'issues':
    default:
        $sql = "SELECT PublicationDate FROM Issues WHERE IdPublication = "
	    . $campsite->publication->identifier . " AND Published = 'Y'";
	$dateField = 'PublicationDate';
	break;
    }
    $data = $g_ado_db->GetAll($sql);

    if (!is_array($data) || sizeof($data) < 1) {
        return;
    }

    $publishDates = array();
    foreach ($data as $publicationDate) {
        $publishDate = explode(' ', $publicationDate[$dateField]);
	list($year, $month, $day) = explode('-', $publishDate[0]);
	$publishDates[] = '"' . (int)$month . '/' . (int)$day . '/' . (int)$year . '"';
    }


$html = '
<!--Begin source code for Calendar widget //-->
<div class="' . $style .'">
<div id="' . $container . '"></div>
<form method="post" action="#" name="dates" id="dates">
  <input type="submit" id="submit" value="submit" style="display:none;">
</form>
<script type="text/javascript">
    YAHOO.namespace("campsite.calendar");
    YAHOO.campsite.calendar.init = function() {
        var contentDates = new Array(' . implode(',', $publishDates) . ');

        function handleSelect(type,args,obj) {
            var dates = args[0];
            var date = dates[0];
            var year = date[0], month = date[1], day = date[2];
            var selectedDate = month + "/" + day + "/" + year;
            var urlDate = year + "-" + month + "-" + day;

            for (var i in contentDates) {
                if (selectedDate == contentDates[i]) {
                    var linkTo = "' . $url . '?date=" + urlDate;
                    window.location = linkTo;
                }
            }
        }

        var cal1 = new YAHOO.widget.Calendar("cal1","' . $container . '");

        cal1.selectEvent.subscribe(handleSelect, cal1, true);

        for (var i in contentDates)
            cal1.addRenderer(contentDates[i], cal1.renderCellStyleHighlight1);

        cal1.render();
    }
    YAHOO.util.Event.onDOMReady(YAHOO.campsite.calendar.init);
</script>
</div>
<div style="clear:both" ></div>
<!--End source code for Calendar widget //-->
';


    return $html;
} // fn smarty_function_calendar

?>