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
 null|string     string The html content
 */
function smarty_function_calendar($p_params, &$p_smarty)
{
    global $g_ado_db;

    // if mandatory fields are missed, do nothing
    if (!isset($p_params['container']) || !isset($p_params['url'])) {
        return;
    }

    // get the context variable
    $campsite = $p_smarty->getTemplateVars('gimme');
    $html = '';

    // language must be set in context, otherwise function does nothing
    if (!$campsite->language->defined) {
        return;
    }

    // get parameters
    $container = $p_params['container'];
    $url = $p_params['url'];
    $style = (isset($p_params['style'])) ? $p_params['style'] : 'search-calendar';
    $clickableDates = (isset($p_params['clickable_dates'])) ? isValidClickableDatesType($p_params['clickable_dates']) : 'all';
    if ($clickableDates == false) {
        return;
    }
    $minDate = (isset($p_params['min_date'])) ? $p_params['min_date'] : '';
    $maxDate = (isset($p_params['max_date'])) ? $p_params['max_date'] : '';

    // get list of clickable dates
    $issues = array();
    $articles = array();
    switch($clickableDates) {
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
        $issues = Issue::GetPublicationDates($campsite->publication->identifier,
                                             $campsite->language->number);
        break;
    }

    if ($clickableDates != 'all') {
        $issuesDates = array();
        if (is_array($issues)) {
            foreach ($issues as $publicationDate) {
                $publishDate = explode(' ', $publicationDate);
                list($year, $month, $day) = explode('-', $publishDate[0]);
                $issuesDates[] = '"' . $year . '-' . $month . '-' . $day . '"';
            }
            $issuesDates = array_unique($issuesDates);
        }
        $articlesDates = array();
        if (is_array($articles)) {
            foreach ($articles as $publicationDate) {
                $publishDate = explode(' ', $publicationDate);
                list($year, $month, $day) = explode('-', $publishDate[0]);
                $articlesDates[] = '"' . $year . '-' . $month . '-' . $day . '"';
            }
            $articlesDates = array_unique($articlesDates);
        }
        if (sizeof($articlesDates) >= sizeof($issuesDates)) {
            $articlesDates = array_diff($articlesDates, $issuesDates);
        }

        $publishDates = array_unique(array_merge($issuesDates, $articlesDates));
    }

    $datepickerSettings = 'dateFormat: "yy-mm-dd", ';
    if (!empty($minDate)) {
        $datepickerSettings .= 'minDate: "' . $minDate . '", ';
    }
    if (!empty($maxDate)) {
        $datepickerSettings .= 'maxDate: "' . $maxDate . "\",\n";
    }

    // build javascript code
    $html = '
<!-- Begin Calendar widget //-->
<div class="' . $style . '">
<div id="' . $container . '"></div>
<form method="post" action="#" name="dates" id="dates">
  <input type="text" id="datepicker" />
  <input type="submit" id="submit" value="submit" style="display:none;">
</form>
<script type="text/javascript">
    $(function() {
        var issueDates = new Array(' . implode(',', $issuesDates) . ');
        var articleDates = new Array(' . implode(',', $articlesDates) . ');
        $.datepicker.setDefaults($.datepicker.regional["' . $campsite->language->code . '"]);
        $("#datepicker").datepicker({';
    $html .= $datepickerSettings;
    if ($clickableDates != 'all') {
        $html .= 'beforeShowDay: function(displayedDate) {
                      var dDate = "";
                      var dDay = displayedDate.getDate();
                      var dMonth = displayedDate.getMonth() + 1;

                      if (dDay < 10) dDay = "0" + dDay;
                      if (dMonth < 10) dMonth = "0" + dMonth;
                      dDate = displayedDate.getFullYear() + "-" + dMonth + "-" + dDay;

                      for (i = 0; i < issueDates.length; i++) {
                          if (dDate == issueDates[i]) {
                              return [false,"ui-state-active",""];
                          }
                      }
                      for (i = 0; i < articleDates.length; i++) {
                          if (dDate == articleDates[i]) {
                              return [true, "ui-state-active", ""];
                          }
                      }
                      return [false, ""]; // disable all other days
                  },';
        $html .= 'onSelect: function(selectedDate) {
                      var contentDates = new Array(' . implode(',', $publishDates) . ');
                      for (var i in contentDates) {
                          if (selectedDate == contentDates[i]) {
                              var linkTo = "' . $url . '&date=" + selectedDate;
                              window.location = linkTo;
                          }
                      }
                  }';
    } else {
        $html .= 'onSelect: function(selectedDate) {
                      var linkTo = "' . $url . '&date=" + selectedDate;
                      window.location = linkTo;
                  }';
    }
    $html .= '});
    });
</script>
</div>
<div style="clear:both" ></div>
<!--End Calendar widget //-->
';

    return $html;
} // fn smarty_function_calendar


/**
 * @param string $p_clickableDates
 *      Type of clickable dates
 * @return string/boolean
 *      The type name if valid, otherwise FALSE
 */
function isValidClickableDatesType($p_clickableDates)
{
    $p_clickableDates = strtolower($p_clickableDates);
    $validTypes = array('articles','issues','issues,articles','articles,issues','all');
    if (in_array($p_clickableDates, $validTypes)) {
        if ($p_clickableDates == 'issues,articles' || $p_clickableDates == 'articles,issues') {
            $p_clickableDates = 'issuesarticles';
        }
        return $p_clickableDates;
    }
    return false;
} // fn isValidClickableDatesType
?>
