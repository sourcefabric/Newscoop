<?php

/**
 * Check if user has access to the admin.
 * @param array $p_request
 * @return array
 */
function camp_check_admin_access($p_request)
{
	global $ADMIN, $g_ado_db, $LiveUser;

	require_once($GLOBALS['g_campsiteDir'].'/classes/User.php');

	$access = false;
	$XPerm = array();
	$user = array();

    if (!$LiveUser->isLoggedIn()) {
        return array($access, $user, $XPerm);
    }

	// check for required info
	if (!isset($p_request['LoginUserId']) || !isset($p_request['LoginUserKey'])
	 	|| !is_numeric($p_request['LoginUserId']) || !is_numeric($p_request['LoginUserKey'])) {
		return array($access, $user, $XPerm);
	}

	// we passed LiveUser->isLoggedIn() so we can be sure the user
    // actually exists in database table
    if ($LiveUser->getProperty('keyid') == $p_request['LoginUserKey']) {
        $access = true;
        $user = new User($LiveUser->getProperty('auth_user_id'));
    }
	return array($access, $user);
} // fn check_basic_access


/**
 * Compute the difference between two string times.
 *
 * @param string $p_time1
 *      Any string that can be converted with strtotime();
 *
 * @param string $p_time2
 *      (optional) Any string that can be converted with strtotime();
 *      If not specified, the current time is used.
 *
 * @return array
 *      An array of (days, hours, minutes, seconds)
 */
function camp_time_diff_str($p_time1, $p_time2 = null)
{
    // Convert the string times into absolute seconds
    $p_time1 = strtotime($p_time1);
    if (is_null($p_time2)) {
        $p_time2 = time();
    }
    else {
        $p_time2 = strtotime($p_time2);
    }

    // Compute the absolute difference between the times.
    $diffSeconds = abs($p_time1 - $p_time2);
    $days = floor($diffSeconds/86400);
    $diffSeconds -= ($days * 86400);
	$hours = floor($diffSeconds/3600);
	$diffSeconds -= $hours * 3600;
	$minutes = floor($diffSeconds/60);
	$diffSeconds -= $minutes * 60;
	return array('days' => $days, 'hours' => $hours, 'minutes' => $minutes, 'seconds' => $diffSeconds);
} // fn camp_time_diff_str


/**
 * Format a time data given in the playtime format.
 *
 * @param string $p_time
 *
 * @return string
 *      A string in the [H:]m:i format
 */
function camp_time_format($p_time)
{
    if (strpos($p_time, '.')) {
        list ($p_time, $lost) = explode('.', $p_time);
    }
    $p_time = str_replace('&nbsp;', '', $p_time);

    if (preg_match('/^[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}$/', $p_time)) {
        list($h, $i, $s) = explode(':', $p_time);
    } elseif (preg_match('/^[0-9]{1,2}:[0-9]{1,2}$/', $p_time)) {
        list($i, $s) = explode(':', $p_time);
    } else {
        $s = $p_time;
    }

    if ((isset($all) && $all) || ($h > 0) ) {
        $H = sprintf('%02d', $h).':';
    } else {
        $H = '';
    }
    $I = sprintf('%02d', $i).':';
    $S = sprintf('%02d', $s);

    return $H.$I.$S;
} // fn camp_time_format


/**
 * Return a value from the array, or if the value does not exist,
 * return the given default value.
 *
 * @param array $p_array
 * @param mixed $p_index
 * @param mixed $p_defaultValue
 *
 * @return mixed
 */
function camp_array_get_value($p_array, $p_index, $p_defaultValue = null)
{
	if (isset($p_array[$p_index])) {
		return $p_array[$p_index];
	}
	else {
		return $p_defaultValue;
	}
} // fn camp_array_get_value


/**
 * Convert a string so that it can be placed within
 * a javascript string that begins and ends with a single quote (').
 * This is for confirmation dialogs so that database strings do not
 * mess things up.
 *
 * @param string $p_string
 * @return string
 */
function camp_javascriptspecialchars($p_string)
{
	$encodedString = htmlspecialchars($p_string);
	$slashedString = addslashes($encodedString);
	return $slashedString;
} // fn camp_javascriptspecialchars


/**
 * Format the values of an array into a string.
 *
 * @param array $p_array
 *      The array to format.
 * @param string $p_keyValueSeparator
 *      The string to put between the key and the value.
 * @param string $p_elementSeparator
        The string to put between elements of the array.
 * @param string $p_keyPrefixString
 *      The string to put before each key. Default is the empty string.
 * @param string $p_keyPostfixString
 *      The string to put after each key.  Default is the empty string.
 * @param string $p_valuePrefixString
 *      The string to put before each element value. Default is the empty string.
 * @param string $p_valuePostfixString
 *      The string to put after each element value. Default is the empty string.
 * @return string
 */
function camp_implode_keys_and_values($p_array,
                                      $p_keyValueSeparator,
                                      $p_elementSeparator,
                                      $p_keyPrefixString = '',
                                      $p_keyPostfixString = '',
                                      $p_valuePrefixString = '',
                                      $p_valuePostfixString = '')
{
    $returnString = '';
    if (is_array($p_array)) {
        $elements = array();
        foreach ($p_array as $key => $value) {
            $elements[] = $p_keyPrefixString . $key . $p_keyPostfixString .
                $p_keyValueSeparator . $p_valuePrefixString . $value . $p_valuePostfixString;
        }
        $returnString = implode($p_elementSeparator, $elements);
    }
    return $returnString;
} // fn camp_implode_keys_and_values


/**
 * An alias for "print()".
 * @param string $p_string
 * @return void
 */
function p($p_string = null)
{
    print $p_string;
} // fn p


/**
 * Transform bytes into a human-readable string.
 *
 * @param int $p_bytes
 * @return string
 */
function camp_format_bytes($p_bytes)
{
	if ( ($p_bytes / 1073741824) > 1) {
		return round($p_bytes/1073741824, 1).' '.getGS('GB');
	} else if ( ($p_bytes / 1048576) > 1) {
		return round($p_bytes/1048576, 1).' '.getGS('MB');
	} else if ( ($p_bytes / 1024) > 1) {
		return round($p_bytes/1024, 1).' '.getGS('KB');
	} else {
		return round($p_bytes, 1).' '.getGS('bytes');
	}
} // fn camp_format_bytes


/**
 * Transform a human-readable string into bytes.
 *
 * @param string $p_val
 * @return int
 */
function camp_convert_bytes($p_val)
{
	$p_val = trim($p_val);
	if ($p_val == '') {
		return false;
	}
	$last = strtolower($p_val{strlen($p_val)-1});
	switch($last) {
		// The 'G' modifier is available since PHP 5.1.0
		case 'g':
			$p_val *= 1024;
		case 'm':
			$p_val *= 1024;
		case 'k':
			$p_val *= 1024;
			break;
		default:
			$p_val = false;
	}
	return $p_val;
} // fn camp_convert_bytes


/**
 * Find out the Mime Content Type for the given file.
 * Replacement for the PHP native but not-always
 * available function mime_content_type()
 *
 * @param string $p_file
 * @return string
 */
function camp_mime_content_type($p_file)
{
	return exec(trim('file -bi ' . escapeshellarg($p_file)));
} // fn camp_mime_content_type


/**
 * Load the language files for the given prefix.
 *
 * @param string $p_prefix
 * @return void
 */
function camp_load_translation_strings($p_prefix, $p_langCode = null)
{
    require_once('localizer/Localizer.php');
    $langCode = null;
    if (!is_null($p_langCode)) {
        $langCode = $p_langCode;
    } elseif (isset($_REQUEST['TOL_Language'])) {
         $langCode = $_REQUEST['TOL_Language'];
    }
    Localizer::LoadLanguageFiles($p_prefix, $langCode);
} // fn camp_load_translation_strings


/**
 * Return TRUE if the given name is a valid URL name for a issue or section.
 *
 * @param string $p_name
 * @return boolean
 */
function camp_is_valid_url_name($p_name)
{
	if (strlen($p_name) == 0) {
		return false;
	}
	for ($i = 0; $i < strlen($p_name); $i++) {
		$c = $p_name[$i];
		$ok = ($c >= 'A' && $c <= 'Z') || ($c >= 'a' && $c <= 'z') || $c == '_' || ($c >= '0' && $c <= '9');
		if (!$ok) {
			return false;
		}
	}
	return true;
} // fn camp_is_valid_url_name



function camp_is_valid_url($p_url)
{
	if (preg_match('/^(http|https|ftp):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(:(\d+))?\//i', $p_url, $m)) {
		return true;
	} else {
		return false;
	}
} // fn camp_is_valid_url


/**
 * Get the first element from the given array, but do not modify
 * the array the way array_pop() does.
 * @param array $p_array
 * @param boolean $p_getKeyValuePair
 * 		If TRUE, return both the key and the element,
 * 		if FALSE, just return the element.
 * @param int $p_offset
 * 		Which element to peek at.  If -1, peek at the last element.
 * @return mixed
 */
function camp_array_peek($p_array, $p_getKeyValuePair = false, $p_offset = 0)
{
	reset($p_array);
	if ($p_offset == -1) {
		end($p_array);
	}
	list($key, $element) = each($p_array);
	if ($p_getKeyValuePair) {
		return array($key, $element);
	} else {
		return $element;
	}
} // fn camp_array_peek


/**
 * Get a persistant value.  If the value is present in the $_REQUEST
 * array, the session variable will be set to this value and returned.
 * If the value is not yet set, it will be set to the default value.
 * In all other cases the value from the session variable is returned.
 *
 * @param string $p_name
 * @param mixed $p_defaultValue
 * @return mixed
 */
function camp_session_get($p_name, $p_defaultValue)
{
	// Use the REQUEST variable if it is set.
	if (isset($_REQUEST[$p_name])) {
		$_SESSION[$p_name] = $_REQUEST[$p_name];
	}
	elseif (!isset($_SESSION[$p_name])) {
		$_SESSION[$p_name] = $p_defaultValue;
	}
	return $_SESSION[$p_name];
} // fn camp_session_get


/**
 * A wrapper around setting a session variable.
 *
 * @param string $p_name
 * @param mixed $p_value
 * @return void
 */
function camp_session_set($p_name, $p_value)
{
    $_SESSION[$p_name] = $p_value;
} // fn camp_session_set


/**
 * A wrapper around unsetting a session variable.
 *
 * @param string $p_name
 * @param mixed $p_value
 * @return void
 */
function camp_session_unset($p_name)
{
	if (isset($_SESSION[$p_name])) {
	    unset($_SESSION[$p_name]);
	}
} // fn camp_session_set


/**
 * Performs Campcaster authentication
 *
 * @param string $f_cc_username
 * @param string $f_cc_password
 * @return boolean true or PEAR_Error
 */
function camp_campcaster_login($f_cc_username, $f_cc_password)
{
    global $mdefs;

    $xrc =& XR_CcClient::Factory($mdefs);
    if (PEAR::isError($xrc)) {
    	return $xrc;
    }
    $r = $xrc->xr_login($f_cc_username, $f_cc_password);
    if (is_string($r) && $r == 'Connection refused') {
        $r = new PEAR_Error(getGS("Connection refused"));
    }
    if (PEAR::isError($r)) {
        return $r;
    }
    if (!is_array($r) && !isset($r['sessid'])) {
        return new PEAR_Error(getGS('Unable to connect to the Campcaster server, please verify the Campcaster server settings.'));
    }
    camp_session_set('cc_sessid', $r['sessid']);
    return true;
} // fn camp_campcaster_login


/**
 * Print out the array or object surrounded with PRE tags so that its readable.
 * @param mixed $p_object
 * @return void
 */
function camp_dump($p_object)
{
	echo "<pre>";
	ob_start();
	print_r($p_object);
	$buffer = ob_get_clean();
	echo htmlspecialchars(wordwrap($buffer, 100));
	echo "</pre>";
}


/**
 * Get the error message for standard errors.
 *
 * @param int $p_errorCode
 * @param mixed $p_arg1
 */
function camp_get_error_message($p_errorCode, $p_arg1 = null, $p_arg2 = null)
{
	global $Campsite;
	if (function_exists("camp_load_translation_strings")) {
		camp_load_translation_strings("home");
	}

	switch ($p_errorCode) {
	case CAMP_ERROR_CREATE_FILE:
		return getGS("The system was unable to create the file '$1'.", basename($p_arg1))
			.(!is_null($p_arg2) ? ' '.getGS("This file is stored on disk as '$1'.", $p_arg2) : '')
			.' '.getGS("Please check if the user '$1' has permission to write to the directory '$2'.", $Campsite['APACHE_USER'], dirname($p_arg1));
		break;
	case CAMP_ERROR_WRITE_FILE:
		return getGS("The system was unable to write to the file '$1'.", basename($p_arg1))
			.(!is_null($p_arg2) ? ' '.getGS("This file is stored on disk as '$1'.", $p_arg2) : '')
			.' '.getGS("Please check if the user '$1' has permission to write to this file.", $Campsite['APACHE_USER']);
		break;
	case CAMP_ERROR_READ_FILE:
		return getGS("The system was unable to read the file '$1'.", basename($p_arg1))
			.(!is_null($p_arg2) ? ' '.getGS("This file is stored on disk as '$1'.", $p_arg2) : '')
			.' '.getGS("Please check if the user '$1' has permission to read this file.", $Campsite['APACHE_USER']);
		break;
	case CAMP_ERROR_DELETE_FILE:
		return getGS("The system was unable to delete the file '$1'.", basename($p_arg1))
			.(!is_null($p_arg2) ? ' '.getGS("This file is stored on disk as '$1'.", $p_arg2) : '')
			.' '.getGS("Please check if the user '$1' has permission to write to the directory '$2'.", $Campsite['APACHE_USER'], dirname($p_arg1));
		break;
    case CAMP_ERROR_UPLOAD_FILE:
        return getGS("The system was unable to upload the file '$1'. ", basename($p_arg1))
            .getGS('Please check the file you are trying to upload, it might be corrupted.');
        break;
	case CAMP_ERROR_MKDIR:
		return getGS("The system was unable to create the directory '$1'.", $p_arg1).' '.getGS("Please check if the user '$1' has permission to write to the directory '$2'.", $Campsite['APACHE_USER'], dirname($p_arg1));
		break;
	case CAMP_ERROR_RMDIR:
		return getGS("The system was unable to delete the directory '$1'.", $p_arg1).' '.getGS("Please check if the directory is empty and the user '$1' has permission to write to the directory '$2'.", $Campsite['APACHE_USER'], dirname($p_arg1));
		break;
	case CAMP_ERROR_WRITE_DIR:
		return getGS("The system is unable to write to the directory '$1'.", $p_arg1).' '.getGS("Please check if the user '$1' has permission to write to the directory '$2'.", $Campsite['APACHE_USER'], $p_arg1);
		break;
	}
	return "";
} // fn camp_get_error_message


function camp_get_plugin_path($p_plugin_name, $p_source_fullpath)
{
    global $ADMIN_DIR;

    $PLUGIN_PATH = dirname(__FILE__).'/../plugins';
    
    $target_subpath = str_replace(dirname(__FILE__), '', $p_source_fullpath);
    $target_fullpath = realpath("$PLUGIN_PATH/$p_plugin_name/$ADMIN_DIR/include/$target_subpath");
    
    if (file_exists($target_fullpath)) {
        return $target_fullpath;
    }  
    
    else return false; 
}


function get($p_input)
{
    return $p_input;   
}


/**
 * Decode an structured array
 *
 * @param array $input
 * @return array
 */
function camp_html_entity_decode_array($p_input, $p_decode_keys=false)
{
    if ($p_decode_keys) {
        $function = 'html_entity_decode';    
    } else {
        $function = 'get';   
    }
    
    if (is_array($p_input)) {
        foreach ($p_input as $key=>$val) { 
            if (is_array($val)) {
                $arr[$function($key)] = html_entity_decode_array($val, $p_decode_keys);
            } else {
                $arr[$function($key)] = $function($val);
            }            
        }
        return $arr;
    } else {
        return html_entity_decode($p_input);
    }
}


function htmlspecialchars_array($p_input, $p_decode_keys=false)
{
    if ($p_decode_keys) {
        $function = 'html_specialchars';    
    } else {
        $function = 'get'; 
    }
    
    if (is_array($p_input)) {
        foreach ($p_input as $key=>$val) { 
            if (is_array($val)) {
                $arr[$function($key)] = html_entity_decode_array($val, $p_decode_keys);
            } else {
                $arr[$function($key)] = $function($val);
            }            
        }
        return $arr;
    } else {
        return html_entity_decode($p_input);
    }
}


/**
 * Set Lock Info and Row Class strings
 * for the usage in Article list tables.
 *
 * @param object $p_articleObj
 * @param string $p_lockInfo
 * @param string $p_rowClass
 * @param boolean $p_color
 */
function camp_set_article_row_decoration(&$p_articleObj, &$p_lockInfo, &$p_rowClass, &$p_color) {
    global $g_user;
    $p_lockInfo = '';
    
    $timeDiff = camp_time_diff_str($p_articleObj->getLockTime());
    if ($p_articleObj->isLocked() && ($timeDiff['days'] <= 0)) {
        $lockUserObj = new User($p_articleObj->getLockedByUser());
        if ($timeDiff['hours'] > 0) {
            $p_lockInfo = getGS('The article has been locked by $1 ($2) $3 hour(s) and $4 minute(s) ago.',
            htmlspecialchars($lockUserObj->getRealName()),
            htmlspecialchars($lockUserObj->getUserName()),
            $timeDiff['hours'], $timeDiff['minutes']);
        } else {
            $p_lockInfo = getGS('The article has been locked by $1 ($2) $3 minute(s) ago.',
            htmlspecialchars($lockUserObj->getRealName()),
            htmlspecialchars($lockUserObj->getUserName()),
            $timeDiff['minutes']);
        }
    }
        
    if ($p_articleObj->isLocked() && ($timeDiff['days'] <= 0) && $p_articleObj->getLockedByUser() != $g_user->getUserId()) {
        $p_rowClass = "article_locked";
    } else {
        if ($p_color) {
            $p_rowClass = "list_row_even";
        } else {
            $p_rowClass = "list_row_odd";
        }
    }
    $p_color = !$p_color;
}


function camp_get_calendar_include($p_languageCode = null)
{
	$calendarPath = $GLOBALS['Campsite']['CAMPSITE_DIR'] . '/javascript/jscalendar';
    $calendarLocalization = "lang/calendar-$p_languageCode.js";
    if (!file_exists("$calendarPath/$calendarLocalization")) {
        $codeParts = explode('_', $p_languageCode);
        if (count($codeParts) > 1) {
            $p_languageCode = $codeParts[0];
            $calendarLocalization = "lang/calendar-$p_languageCode.js";
            if (!file_exists("$calendarPath/$calendarLocalization")) {
                $p_languageCode = 'en';
                $calendarLocalization = "lang/calendar-$p_languageCode.js";
            }
        } else {
            $p_languageCode = 'en';
            $calendarLocalization = "lang/calendar-$p_languageCode.js";
        }
    }
	
	$websiteURL = $GLOBALS['Campsite']["WEBSITE_URL"];
	$calendarURL = "$websiteURL/javascript/jscalendar";
	ob_start();
?>
<style type="text/css">@import url(<?php echo htmlspecialchars($calendarURL); ?>/calendar-system.css);</style>
<script type="text/javascript" src="<?php echo htmlspecialchars($calendarURL); ?>/calendar.js"></script>
<script type="text/javascript" src="<?php echo htmlspecialchars($calendarURL); ?>/<?php echo $calendarLocalization; ?>"></script>
<script type="text/javascript" src="<?php echo htmlspecialchars($calendarURL); ?>/calendar-setup.js"></script>
<?php
	return ob_get_clean();
}


function camp_get_calendar_field($p_fieldName, $p_defaultValue = null,
                                 $p_showTime = false, $p_htmlCode = null)
{
	$websiteURL = $GLOBALS['Campsite']["WEBSITE_URL"];
	$showTime = $p_showTime ? 'true' : 'false';
	$buttonName = $p_fieldName . '_trigger';
	$size = $p_showTime ? 19 : 10;
	$format = $p_showTime ? "%Y-%m-%d %H:%M:00" : "%Y-%m-%d";
	ob_start();
?>
<input type="text" name="<?php echo htmlspecialchars($p_fieldName); ?>"
    value="<?php echo htmlspecialchars($p_defaultValue); ?>"
    id="<?php echo htmlspecialchars($p_fieldName); ?>"
    size="<?php echo $size; ?>" maxlength="<?php echo $size; ?>"
    <?php echo $p_htmlCode; ?> />
<img src="<?php echo htmlspecialchars($websiteURL); ?>/css/calendar.gif"
    id="<?php echo htmlspecialchars($buttonName); ?>"
    style="cursor: pointer; border: 1px solid red;"
    title="Date selector"
    onmouseover="this.style.background='red';"
    onmouseout="this.style.background=''" />
<script type="text/javascript">
    Calendar.setup({
        inputField:"<?php echo htmlspecialchars($p_fieldName); ?>",
        ifFormat:"<?php echo $format; ?>",
        displayArea:"<?php echo htmlspecialchars($p_fieldName); ?>",
        daFormat:"<?php echo $format; ?>",
        showsTime:<?php echo $showTime; ?>,
        showOthers:true,
        weekNumbers:false,
        range:new Array(1990, 2020),
        button:"<?php echo htmlspecialchars($buttonName); ?>"
    });
</script>
<?php
    return ob_get_clean();
}


function camp_set_author(ArticleTypeField $p_sourceField, &$p_errors)
{
	$p_errors = array();
	$articles = Article::GetArticlesOfType($p_sourceField->getArticleType());
	foreach ($articles as $article) {
		$articleData = $article->getArticleData();
		$authorName = trim($articleData->getFieldValue($p_sourceField->getPrintName()));
		if (empty($authorName)) {
			continue;
		}
		$author = new Author($authorName);
		if (!$author->exists()) {
			if (!$author->create()) {
				$p_errors[] = getGS('Unable to create author "$1" for article no. $2 ("$3") of type $4.',
				                    $author->getName(), $article->getArticleNumber(),
				                    $article->getName(), $article->getType());
				continue;
			}
		}
		if (!$article->setAuthorId($author->getId())) {
			$p_errors[] = getGS('Error setting the author "$1" for article no. $2 ("$3") of type $4.',
                                $author->getName(), $article->getArticleNumber(),
                                $article->getName(), $article->getType());
			continue;
		}
	}
	return count($p_errors);
}

?>
