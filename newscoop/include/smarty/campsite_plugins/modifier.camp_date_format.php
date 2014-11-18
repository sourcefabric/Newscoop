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
 string     string the formatted date
 *     null in case a non-valid format was passed
 */
function smarty_modifier_camp_date_format($p_unixtime, $p_format = null, $p_onlyEnglish = false)
{
    static $attributes = array('year'=>'%Y', 'mon'=>'%c', 'mday'=>'%e', 'yday'=>'%j',
                        'wday'=>'%w', 'hour'=>'%H', 'min'=>'%i', 'sec'=>'%S',
                        'mon_name'=>'%M', 'wday_name'=>'%W');
    static $specifiersMap = array('%h'=>'%I', '%i'=>'%M', '%s'=>'%S');
    static $conversionMap = array('%M'=>'__month_name__', '%W'=>'__weekday_name__',
                                  '%c'=>'__month__', '%e'=>'__day_of_the_month__',
                                  '%D'=>'__day_of_the_month_suffix__', '%l'=>'__hour_12_clock__');
    static $numberSuffix = array(0=>'th', 1=>'st', 2=>'nd', 3=>'rd', 4=>'th', 5=>'th', 6=>'th',
                                 7=>'th', 8=>'th', 9=>'th');

    if (array_key_exists(trim(strtolower($p_format)), $attributes)) {
        $p_format = $attributes[trim(strtolower($p_format))];
    }

    // gets the context variable
    $campsite = CampTemplate::singleton()->getTemplateVars('gimme');

    // makes sure $p_unixtime is unixtime stamp
    CampTemplate::singleton()->loadPlugin('smarty_shared_make_timestamp');
    $p_unixtime = smarty_make_timestamp($p_unixtime);

    if (is_null($p_format) || empty($p_format)) {
    	return strftime('%D %T', $p_unixtime);
    }

    $p_replaceCount = 0;
    $p_format = str_replace(array_keys($conversionMap), array_values($conversionMap),
    $p_format, $p_replaceCount);

    $p_format = str_replace(array_keys($specifiersMap), array_values($specifiersMap), $p_format);

    $formattedDate = strftime($p_format, $p_unixtime);
    if ($p_replaceCount > 0) {
        $cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
        $cacheKey = $cacheService->getCacheKey(array('languageObj', $campsite->language->number), 'language');
        if ($cacheService->contains($cacheKey)) {
            $languageObj = $cacheService->fetch($cacheKey);
        } else {
            $languageObj = new Language($campsite->language->number);
            $cacheService->save($cacheKey, $languageObj);
        }
        if (!$languageObj->exists()) {
            $languageObj = new Language(1);
        }
        $timeArray = getdate($p_unixtime);
        $suffixNo = $timeArray['mday'] % 10;
        $hour = $timeArray['hours'] % 12;
        if ($hour == 0) {
        	$hour = 12;
        }
        $replaceValues = array($languageObj->getProperty('Month'.$timeArray['mon']),
                               $languageObj->getProperty('WDay'.(1+$timeArray['wday'])),
                               $timeArray['mon'],
                               $timeArray['mday'],
                               $timeArray['mday'].$numberSuffix[$suffixNo],
                               $hour);
        $formattedDate = str_replace(array_values($conversionMap), $replaceValues, $formattedDate);
    }
    return $formattedDate;
} // fn smarty_modifier_camp_date_format

?>
