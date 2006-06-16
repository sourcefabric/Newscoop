<?php
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];

/**
 * Check if user has access to the admin.
 * @param array $p_request
 * @return array
 */
function camp_check_admin_access($p_request)
{
	global $ADMIN;
	global $g_ado_db;
	require_once($_SERVER['DOCUMENT_ROOT'].'/classes/User.php');
	$access = false;
	$XPerm = array();
	$user = array();

	// Check for required info.
	if (!isset($p_request['LoginUserId']) || !isset($p_request['LoginUserKey'])
	 	|| !is_numeric($p_request['LoginUserId']) || !is_numeric($p_request['LoginUserKey'])) {
		return array($access, $user, $XPerm);
	}

	// Check if user exists in the table.
	$queryStr = 'SELECT * FROM Users '
				.' WHERE Id='.$p_request['LoginUserId']
				." AND Reader='N'";
	$row = $g_ado_db->GetRow($queryStr);
	if ($row && $row['KeyId'] == $p_request['LoginUserKey']) {
		// User exists.
		$access = true;
		$user =& new User();
		$user->fetch($row);
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
 * Load the language files for the given prefix.
 *
 * @param string $p_prefix
 * @return void
 */
function camp_load_translation_strings($p_prefix)
{
    require_once('localizer/Localizer.php');
    $langCode = null;
     if (isset($_REQUEST['TOL_Language'])) {
         $langCode = $_REQUEST['TOL_Language'];
    }
    Localizer::LoadLanguageFiles($p_prefix, $langCode);
} // fn camp_load_translation_strings


/**
 * Split the given text into something.
 * @return string
 */
//function camp_limit_chars($p_text, $p_limit, $p_break, $p_tail)
//{
//	$p_text = preg_replace("/$p_break/i", strtolower($p_break), $p_text);
//	$p_text = split(strtolower("$p_break"), $p_text);
//	if (strlen(implode("$p_break", $p_text)) >= $p_limit) {
//		$i = 0;
//		$add_str = "";
//		while($i <= count($p_text)) {
//			$add_str = $p_text[$i];
//			$out[] = $add_str;
//			if(strlen(implode("$p_break", $out)) >= $p_limit - strlen($p_break) - strlen($add_str)) {
//				break;
//			}
//			$add_str = "";
//			$i++;
//		}
//		$p_text = implode("$p_break", $out);
//		if (substr($p_text, 0, -strlen($p_break)) == $p_break) {
//			$p_text = substr($p_text, 0, -strlen($p_break));
//		}
//		$p_text = "$p_text$p_tail";
//	}
//	else {
//		$p_text=implode("$p_break", $p_text);
//	}
//	return $p_text;
//} // fn camp_limit_chars


function camp_is_valid_url_name($name)
{
	if (strlen($name) == 0) {
		return false;
	}
	for ($i = 0; $i < strlen($name); $i++) {
		$c = $name[$i];
		$ok = ($c >= 'A' && $c <= 'Z') || ($c >= 'a' && $c <= 'z') || $c == '_' || ($c >= '0' && $c <= '9');
		if (!$ok) {
			return false;
		}
	}
	return true;
} // fn camp_is_valid_url_name


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
 * 		Can be:
 * 		CAMP_FILE_NOT_WRITABLE
 * 		CAMP_DIR_NOT_WRITABLE
 * @param unknown_type $p_arg1
 */
function camp_get_error_message($p_errorCode, $p_arg1 = null)
{
	global $Campsite;
	switch ($p_errorCode) {
	case CAMP_ERROR_CREATE_FILE:
		return getGS("The system was unable to create the file '$1'.", $p_arg1).' '.getGS("Please check if the user '$1' has permission to write to the directory '$2'.", $Campsite['APACHE_USER'], dirname($p_arg1));
		break;
	case CAMP_ERROR_WRITE_FILE:
		return getGS("The system was unable to write to the file '$1'.", $p_arg1).' '.getGS("Please check if the user '$1' has permission to write to this file.", $Campsite['APACHE_USER']);
		break;
	case CAMP_ERROR_DELETE_FILE:
		return getGS("The system was unable to delete the file '$1'.", $p_arg1).' '.getGS("Please check if the user '$1' has permission to write to this file.", $Campsite['APACHE_USER']);
		break;
	case CAMP_ERROR_MKDIR:
		return getGS("The system was unable to create the directory '$1'.", $p_arg1).' '.getGS("Please check if the user '$1' has permission to write to the directory '$2'.", $Campsite['APACHE_USER'], dirname($p_arg1));
		break;
	}
	return "";
} // fn camp_get_error_message

?>