<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Builds a Calendar in HTML using Ordered Lists.
 * php5-intl is required for this to run
 *
 * Type:     function
 * Name:     daterange_calendar_html
 * Purpose:
 *
 * @param array $p_params
 *
 * @return string $html
 *      The requested calendar as HTML
 *
 * @example
 *  {{ daterange_calendar_html rangestart="2013-10-30" rangeend="2014-10-30" rangeformatmonth="MM" rangeformatday="dd" locale="de-DE"}}
 *  For the full range of formatting possible please refer to http://userguide.icu-project.org/formatparse/datetime
 *
 */

function smarty_function_daterange_calendar_html($p_params = array(), &$p_smarty)
{
    // The $p_params override the $_GET
    $acceptedParams = array('rangestart', 'rangeend', 'rangeformat', 'rangeformatmonth', 'rangeformatday', 'locale');
    $cleanParam = array();

    foreach ($acceptedParams as $key) {
        if (array_key_exists($key, $_GET) && !empty($_GET[$key])) {
            $cleanParam[$key] = $_GET[$key];
        } else if (array_key_exists($key, $p_params) && !empty($p_params[$key])) {
            $cleanParam[$key] = $p_params[$key];
        }
    }

    $cleanParam['rangeformatmonth'] = (!array_key_exists('rangeformatmonth', $cleanParam)) ? 'MM' : $cleanParam['rangeformatmonth'];
    $cleanParam['rangeformatday'] = (!array_key_exists('rangeformatday', $cleanParam)) ? 'dd' : $cleanParam['rangeformatday'];

    $cleanParam['rangestart'] = (!array_key_exists('rangestart', $cleanParam)) ? date('Y-m-d', strtotime('-1 month')) : $cleanParam['rangestart'];
    $cleanParam['rangeend'] = (!array_key_exists('rangeend', $cleanParam)) ? date('Y-m-d', time('now')) : $cleanParam['rangeend'];

    $cleanParam['locale'] = (!array_key_exists('locale', $cleanParam)) ? 'en-GB' : $cleanParam['locale'];
    
    $cleanParam['start']['year'] = date('Y', strtotime($cleanParam['rangestart']));
    $cleanParam['start']['month'] = date('m', strtotime($cleanParam['rangestart']));
    $cleanParam['start']['day'] = date('d', strtotime($cleanParam['rangestart']));
    $cleanParam['end']['year'] = date('Y', strtotime($cleanParam['rangeend']));
    $cleanParam['end']['month'] = date('m', strtotime($cleanParam['rangeend']));
    $cleanParam['end']['day'] = date('d', strtotime($cleanParam['rangeend']));

    $dateFormatter['dayName'] = \IntlDateFormatter::create(
      $cleanParam['locale'],
      \IntlDateFormatter::NONE,
      \IntlDateFormatter::NONE,
      \date_default_timezone_get(),
      \IntlDateFormatter::GREGORIAN,
      'EEE'
    );

    $dateFormatter['day'] = \IntlDateFormatter::create(
      $cleanParam['locale'],
      \IntlDateFormatter::NONE,
      \IntlDateFormatter::NONE,
      \date_default_timezone_get(),
      \IntlDateFormatter::GREGORIAN,
      $cleanParam['rangeformatday']
    );

    $dateFormatter['month'] = \IntlDateFormatter::create(
      $cleanParam['locale'],
      \IntlDateFormatter::NONE,
      \IntlDateFormatter::NONE,
      \date_default_timezone_get(),
      \IntlDateFormatter::GREGORIAN,
      $cleanParam['rangeformatmonth']
    );

    $html  = '<div id="archive_list" data-weekdays="';
    for ($i=0; $i <= 6; $i++) {
        $html .= mb_substr($dateFormatter['dayName']->format(strtotime("Monday +$i days")), 0, 2);
        $html .= ($i != 6) ? ' ' : '';
    }
    $html .= '">';

    $html .= '<ol>';

    for ($year=$cleanParam['start']['year']; $year <= $cleanParam['end']['year']; $year++) { 
        $yearString = '<li><h2>'.$year.'</h2>';
        $monthString = '<ol class="year y'.$year.'">';

        $month = ($year == $cleanParam['start']['year']) ? $cleanParam['start']['month'] : 1;
        $endmonth = ($year == $cleanParam['end']['year']) ? $cleanParam['end']['month'] : 12;

        for ($month; $month <= $endmonth; $month++) {
            $maxDay = date('t',strtotime($year.'-'.$month));
            $beginDay = 1;
            if ($year == $cleanParam['start']['year'] && $month == $cleanParam['start']['month'] && $beginDay <= $cleanParam['start']['day']) {
                $beginDay = $cleanParam['start']['day'];
            }
            if ($year == $cleanParam['end']['year'] && $month == $cleanParam['end']['month'] && $maxDay >= $cleanParam['end']['day']) {
                $maxDay = $cleanParam['end']['day'];
            }

            $monthString .= '<li><h3><a href="?fqfrom='.date('Y-m-d', strtotime($year.'-'.$month.'-'.$beginDay)).'&fqto='.date('Y-m-d', strtotime($year.'-'.$month.'-'.$maxDay)).'">';
            $monthString .= $dateFormatter['month']->format(new \DateTime($year.'-'.$month));
            $monthString .= '</h3>';

            $dayString = '<ol class="month m'.$month.'">';
            for ($day=$beginDay; $day <= $maxDay; $day++) { 
                $currIterateDate = date('Y-m-d', strtotime($year.'-'.$month.'-'.$day));
                $dayString .= '<li';
                $week = 'w'.date('W', strtotime($currIterateDate));
                $dayoftheweek = 'd'.date('N', strtotime($currIterateDate));
                $dayString .= ' class="day '.$week.' '.$dayoftheweek.'"';
                $dayString .= '><a href="?fqfrom='.$currIterateDate.'&fqto='.$currIterateDate.'">'.$dateFormatter['day']->format(new \DateTime($currIterateDate)).'</a></li>';
            }
            $dayString .= '</ol>';
            $monthString .= $dayString;
        }
        $monthString .= '</li></ol>';
        $html .= $yearString."\n".$monthString.'</li>';
    }

    $html .= '</ol></div>';

    return $html;
}
