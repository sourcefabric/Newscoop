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

    // if mandatory fields are missed, do nothing
    if (!isset($p_params['container']) || !isset($p_params['url'])) {
        return;
    }

    // get the context variable
    $campsite = $p_smarty->get_template_vars('campsite');
    $html = '';

    // language must be set in context, otherwise function does nothing
    if (!$campsite->language->defined) {
        return;
    }

    // get parameters
    $container = $p_params['container'];
    $url = $p_params['url'];
    $style = (isset($p_params['style'])) ? $p_params['style'] : 'yui-skin-sam';
    $type = (isset($p_params['clickable_dates'])) ? $p_params['clickable_dates'] : 'issues';
    $minDate = (isset($p_params['min_date'])) ? $p_params['min_date'] : '';
    $maxDate = (isset($p_params['max_date'])) ? $p_params['max_date'] : '';

    if (strpos($type, ',') !== false &&
	    ($type == 'issues,articles' || $type == 'articles,issues')) {
        $type = 'issuesarticles';
    }

    // get list of clickable dates
    $issues = array();
    $articles = array();
    switch($type) {
    case 'issuesarticles':
        $articles = Article::GetPublicationDates($campsite->publication->identifier,
						 $campsite->language->number);
	$issues = Issue::GetPublicationDates($campsite->publication->identifier,
					     $campsite->language->number);
	break;
    case 'articles':
        $articles = Article::GetPublicationDates($campsite->publication->identifier,
						 $campsite->language->number);
	break;
    case 'issues':
    default:
        $issues = Issue::GetPublicationDates($campsite->publication->identifier,
					     $campsite->language->number);
	break;
    }

    $issuesDates = array();
    if (is_array($issues)) {
	foreach ($issues as $publicationDate) {
	    $publishDate = explode(' ', $publicationDate);
	    list($year, $month, $day) = explode('-', $publishDate[0]);
	    $issuesDates[] = '"' . (int)$month . '/' . (int)$day . '/' . (int)$year . '"';
	}
	$issuesDates = array_unique($issuesDates);
    }
    $articlesDates = array();
    if (is_array($articles)) {
	foreach ($articles as $publicationDate) {
	    $publishDate = explode(' ', $publicationDate);
	    list($year, $month, $day) = explode('-', $publishDate[0]);
	    $articlesDates[] = '"' . (int)$month . '/' . (int)$day . '/' . (int)$year . '"';
	}
	$articlesDates = array_unique($articlesDates);
    }
    if (sizeof($articlesDates) >= sizeof($issuesDates)) {
        $articlesDates = array_diff($articlesDates, $issuesDates);
    }

    $publishDates = array_unique(array_merge($issuesDates, $articlesDates));

    // localize, only if language is other than English
    if ($campsite->language->code != 'en') {
        $monthsShort = 'cal1.cfg.setProperty("MONTHS_SHORT", [';
	$monthsLong = 'cal1.cfg.setProperty("MONTHS_LONG", [';
	for ($i = 1; $i <= 12; $i++) {
	    $attrib = 'short_month' . $i;
	    $monthsShort .= '"'.$campsite->language->$attrib.'"';
	    $monthsShort .= ($i < 12) ? ',' : ']);';
	    $attrib = 'month' . $i;
	    $monthsLong .= '"'.$campsite->language->$attrib.'"';
	    $monthsLong .= ($i < 12) ? ',' : ']);';
	}
	$wdaysShort = 'cal1.cfg.setProperty("WEEKDAYS_SHORT", [';
	$wdaysLong = 'cal1.cfg.setProperty("WEEKDAYS_LONG", [';
	for ($i = 1; $i <= 7; $i++) {
	    $attrib = 'short_weekday' . $i;
	    $wdaysShort .= '"'.$campsite->language->$attrib.'"';
	    $wdaysShort .= ($i < 7) ? ',' : ']);';
	    $attrib = 'weekday' . $i;
	    $wdaysLong .= '"'.$campsite->language->$attrib.'"';
	    $wdaysLong .= ($i < 7) ? ',' : ']);';
	}
    }


    // build javascript code
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
        var issuesDates = new Array(' . implode(',', $issuesDates) . ');
        var articlesDates = new Array(' . implode(',', $articlesDates) . ');
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

        cal1.selectEvent.subscribe(handleSelect, cal1, true);';

    if (!empty($minDate)) {
        $html .= "\n\n\t" . 'cal1.cfg.setProperty("mindate", "'.$minDate.'");';
    }
    if (!empty($maxDate)) {
        $html .= "\n\n\t" . 'cal1.cfg.setProperty("maxdate", "'.$maxDate.'");';
    }
    if ($campsite->language->code != 'en') {
        $html .= "\n\n\t" . $monthsShort . "\n\t" . $monthsLong
	    . "\n\t" . $wdaysShort . "\n\t" . $wdaysLong . "\n";
    }
        
    $html .= '
        for (var i in articlesDates)
            cal1.addRenderer(articlesDates[i], cal1.renderCellStyleHighlight3);
        for (var i in issuesDates)
            cal1.addRenderer(issuesDates[i], cal1.renderCellStyleHighlight1);

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