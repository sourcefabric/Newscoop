<?php
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];

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
 * Create an HTML OPTION element.
 *
 * @param string $p_value
 * @param string $p_selectedValue
 * @param string $p_printValue
 * @return void
 */
function pcomboVar($p_value, $p_selectedValue, $p_printValue) 
{
	print '<OPTION VALUE="'.htmlspecialchars($p_value, ENT_QUOTES).'"';
	if (!strcmp($p_value, $p_selectedValue)) {
		print ' SELECTED';
	}
	print '>'.htmlspecialchars($p_printValue);
} // fn pcombovar


/** 
 * An alias for "print()".
 * @param string $p_string
 * @return void
 */
function p($p_string) 
{
    print $p_string;
} // fn p


/**
 * Load the global and local language files.
 * @param string $p_path
 * @param string $p_name
 * @return void
 */
function selectLanguageFile($p_name) 
{
    require_once('localizer/Localizer.php');
    $langCode = null;
     if (isset($_REQUEST['TOL_Language'])) {
         $langCode = $_REQUEST['TOL_Language'];
    }
    Localizer::LoadLanguageFiles($p_name, $langCode);
} // fn selectLanguageFile


function limitchars($text, $lim, $break, $tail) 
{
	//  $text = split("$break", $text);
	// If you want this function case insensitive
	// replace above line with these two lines
	$text=preg_replace("/$break/i", strtolower($break), $text);
	$text = split(strtolower("$break"), $text);
	if (strlen(implode("$break", $text)) >= $lim) {
		$i = 0;
		$add_str = "";
		while($i <= count($text)) {
			$add_str = $text[$i];
			$out[] = $add_str;
			if(strlen(implode("$break", $out)) >= $lim - strlen($break) - strlen($add_str)) {
				break;
			}
			$add_str = "";
			$i++;
		}
		$text = implode("$break", $out);
		if (substr($text, 0, -strlen($break)) == $break) {
			$text = substr($text, 0, -strlen($break));
		}
		$text = "$text$tail";
	} 
	else {
		$text=implode("$break", $text);
	}
	return $text;
} // fn limitchars


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


if (file_exists($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/modules/admin/priv_functions.php")) {
	include ($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/modules/admin/priv_functions.php");
}

?>