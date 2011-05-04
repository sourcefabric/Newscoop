<?php

require_once('localizer/Localizer.php');

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
    $langCode = null;
    if (!is_null($p_langCode)) {
        $langCode = $p_langCode;
    } elseif (isset($_REQUEST['TOL_Language'])) {
        $langCode = $_REQUEST['TOL_Language'];
    } elseif (isset($_COOKIE['TOL_Language'])) {
        $langCode = $_COOKIE['TOL_Language'];
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
    global $Campsite;

    $calendarPath = $GLOBALS['Campsite']['CAMPSITE_DIR'] . '/js/jquery/';
    $calendarLocalization = "i18n/jquery.ui.datepicker-$p_languageCode.js";
    if (!file_exists("$calendarPath/$calendarLocalization")) {
        $codeParts = explode('_', $p_languageCode);
        if (count($codeParts) > 1) {
            $p_languageCode = $codeParts[0];
            $calendarLocalization = "i18n/jquery.ui.datepicker-$p_languageCode.js";
            if (!file_exists("$calendarPath/$calendarLocalization")) {
                $p_languageCode = 'en';
                $calendarLocalization = "i18n/jquery.ui.datepicker-$p_languageCode.js";
            }
        } else {
            $p_languageCode = 'en';
            $calendarLocalization = "i18n/jquery.ui.datepicker-$p_languageCode.js";
        }
    }

    $websiteURL = $GLOBALS['Campsite']["WEBSITE_URL"];
    $calendarURL = "$websiteURL/js/jquery";
    ob_start();
?>

<style type="text/css">@import url('<?php echo $Campsite['WEBSITE_URL']; ?>/admin-style/jquery-ui-1.8.6.datepicker.css');</style>
<script type="text/javascript" src="<?php echo htmlspecialchars($calendarURL); ?>/jquery-ui-1.8.6.custom.min.js"></script>
<script type="text/javascript" src="<?php echo htmlspecialchars($calendarURL); ?>/jquery-ui-timepicker-addon.min.js"></script>
<?php if (file_exists(dirname(__FILE__) . '/../js/jquery/' . $calendarLocalization)) { ?>
<script type="text/javascript" src="<?php echo htmlspecialchars($calendarURL); ?>/<?php echo $calendarLocalization; ?>"></script>
<script type="text/javascript"><!--
    $(document).ready(function() {
        $.datepicker.setDefaults( $.datepicker.regional['<?php echo $p_languageCode; ?>'] );
    });
//--></script>
<?php } ?>

<?php
	return ob_get_clean();
}


function camp_get_calendar_field($p_fieldName, $p_defaultValue = null,
                                 $p_showTime = false, $p_htmlCode = null)
{
	$showTime = $p_showTime ? 'true' : 'false';
	$size = $p_showTime ? 19 : 10;
	$format = $p_showTime ? "%Y-%m-%d %H:%M:00" : "%Y-%m-%d";
	ob_start();
?>
<input type="text" name="<?php echo htmlspecialchars($p_fieldName); ?>"
    value="<?php echo htmlspecialchars($p_defaultValue); ?>"
    id="<?php echo htmlspecialchars($p_fieldName); ?>"
    size="<?php echo $size; ?>" maxlength="<?php echo $size; ?>"
    <?php echo $p_htmlCode; ?> />
    <script type="text/javascript"><!--
        $('#<?php echo htmlspecialchars($p_fieldName); ?>').each(function() {
            var settings = {
                minDate: 1990,
                maxDate: 2020,
                dateFormat: 'yy-mm-dd',
                timeFormat: 'hh:mm:ss',
            };
            <?php if ($p_showTime) { ?>
            $(this).datetimepicker(settings);
            <?php } else { ?>
            $(this).datepicker(settings);
            <?php } ?>
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

/**
 * Internal cron task scheduler.
 */
function camp_cron() {
    require_once(CS_PATH_SITE.DIR_SEP.'classes'.DIR_SEP.'Cron.php');

    $fileName = CS_INSTALL_DIR.DIR_SEP.'cron_jobs'.DIR_SEP.'all_at_once';
    $cronTasks = null;
    if (is_readable($fileName)) {
        $cronTasks = @file_get_contents($fileName);
    }
    if (preg_match_all('/^(.+)([^*]+)$/mU', $cronTasks, $aMatches)) {
        foreach ($aMatches[1] as $key => $schedule) {
            $task = trim($aMatches[2][$key]);
            $taskName = basename($task);
            $fileName = CS_INSTALL_DIR.DIR_SEP.'cron_jobs'.DIR_SEP.'lastrun-'.$taskName;
            $taskLastRun = null;
            if (is_readable($fileName)) {
                $taskLastRun = @file_get_contents($fileName);
            }
            $currentTime = time();
            if (!$taskLastRun || Cron::due((int)$taskLastRun, (int)$currentTime, $schedule)) {
                if ($fp = fopen($fileName, 'w')) {
                    if (flock($fp, LOCK_EX)) {
                        fwrite($fp, (string)$currentTime);
                        flock($fp, LOCK_UN);
                        if (substr(strtolower(PHP_OS), 0, 3) != 'win') {
                            exec($task . ' > /dev/null &');
                        }
                    }
                    fclose($fp);
                }
            }
        }
    }
}

function camp_display_message($p_message)
{
    $params = array('context' => null,
                'template' => CS_SYS_TEMPLATES_DIR.DIR_SEP.'_campsite_message.tpl',
                'templates_dir' => CS_TEMPLATES_DIR,
                'info_message' => $p_message
    );
    $document = CampSite::GetHTMLDocumentInstance();
    $document->render($params);
}
?>
