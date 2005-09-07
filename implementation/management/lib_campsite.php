<?php
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];

/**
 * Query the database with the given string and assign the result 
 * to a global variable.  Use fetchRow() to get the results.
 *
 * @param string $p_queryString
 *		The database query.
 *
 * @param string $p_globalVar
 *		The name of the global variable to assign the result to.
 *
 * @param boolean $p_setvars
 *
 * @return void
 */
function query($p_queryString, $p_globalVar='', $p_setvars = true) 
{
	$queryResult = mysql_query($p_queryString);
	$queryParts = explode(" ", $p_queryString);
	//$queryType=strtoupper(substr(trim($p_queryString),0,6));
	$queryType = strtoupper($queryParts[0]);
	$GLOBALS['NUM_ROWS']=0;
	$GLOBALS['AFFECTED_ROWS']=0;
	if ($p_setvars && $queryResult) {
		if (($queryType=='SELECT') || ($queryType == 'SHOW')) {
			$GLOBALS['NUM_ROWS'] = mysql_num_rows($queryResult);
		}
		else {
			$GLOBALS['AFFECTED_ROWS'] = mysql_affected_rows();
		}
	}
	if ($p_globalVar!='') {
		$GLOBALS[$p_globalVar]=$queryResult;
	}
	if (isset($GLOBALS['debug'])) {
		print $p_queryString.$queryResult;
	}
} // fn query



function encURL($s) 
{
    return rawurlencode($s);
}

function decURL($s) 
{
    return rawurldecode($s);
}

function pencURL($s) 
{
    print rawurlencode($s);
}

function pdecURL($s) 
{
    print rawurldecode($s);
}

function encHTML($s) 
{
    $res_str = str_replace("&", "&amp;", $s);
    $res_str = str_replace("<", "&lt;", $res_str);
    $res_str = str_replace(">", "&gt;", $res_str);
    $res_str = str_replace("\"", "&quot;", $res_str);
    $res_str = str_replace("\r\n", "<BR>\r", $res_str);
    return $res_str;
//    return htmlentities($s);
}


function pencHTML($s) 
{
    print encHTML($s);
}


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
 * Get a value from the request variable and store it in a global variable.
 * If the value is not set in the request, assign the default value to the
 * global variable.
 *
 * @return void
 */
function todef($p_varName, $p_defaultValue='') 
{
	if (!isset($_REQUEST[$p_varName])) {
		$GLOBALS[$p_varName] = $p_defaultValue;
	}
	else {
		$GLOBALS[$p_varName] = $_REQUEST[$p_varName];
	}
	
	if (!get_magic_quotes_gpc()) {
		$GLOBALS[$p_varName] = mysql_escape_string($GLOBALS[$p_varName]);
	}
} // fn todef


function encSQL($s) 
{
	if (!get_magic_quotes_gpc()) {
		return mysql_escape_string($s);
	}
	return $s;
}


/**
 * Define a global variable from a request variable of the same name.
 * If the request variable does not exist, assign the default value.
 *
 * @return void
 */
function todefnum($p_varName, $p_defaultValue=0) 
{
	if (!isset($_REQUEST[$p_varName])) {
		$GLOBALS[$p_varName] = (int)$p_defaultValue;
	}
	else {
		$GLOBALS[$p_varName] = (int)$_REQUEST[$p_varName];
	}
} // fn todefnum


/**
 * Fetch a row from the mysql result and store it in a global variable.
 * Use getVar() to retrieve values.
 *
 * @param object $p_mysqlResult
 * 		The result of a mysql query.
 *
 * @return void
 */
function fetchRow($p_mysqlResult) 
{
    $GLOBALS['fetch_'.$p_mysqlResult]=mysql_fetch_array($p_mysqlResult,MYSQL_ASSOC);
} // fn fetchRow


function fetchRowNum($q) 
{
    $GLOBALS['fetch_num_'.$q]=mysql_fetch_array($q,MYSQL_NUM);
}


/**
 * Get a column value from the given query result.
 *
 * @return string
 */
function getVar($p_queryResult, $p_columnName) 
{
    $arr=$GLOBALS['fetch_'.$p_queryResult];
    return $arr[$p_columnName];
} // fn getVar


function getNumVar($q, $n=0) 
{
    $arr=$GLOBALS['fetch_num_'.$q];
    return $arr[$n];
}

function pgetNumVar($q,$n=0) 
{
    $arr=$GLOBALS['fetch_num_'.$q];
    print $arr[$n];
}

function getHVar($q,$s) 
{
    return encHTML(getVar($q,$s));
}


/**
 * Get the variable and run HTMLSPECIALCHARS on it.
 * @return string
 */
function pgetHVar($q,$s) 
{
    print getHVar($q, $s);
} // fn pgetHVar


function getUVar($q, $s) 
{
    return encURL(getVar($q,$s));
}

function getSVar($q, $s) 
{
    return addslashes(getVar($q,$s));
}

/**
 * URL encode the fetched value.
 * @return string
 */
function pgetUVar($q,$s) 
{
    print rawurlencode(getVar($q,$s));
} // fn pgetUVar

function pgetVar($q,$s) 
{
    print getVar($q,$s);
}

function todefradio($s) 
{
	if (!isset($GLOBALS[$s])) {
		$GLOBALS[$s]='';
	}
	if (isset($_REQUEST[$s]) && ($_REQUEST[$s]=='on')) {
		$GLOBALS[$s]='Y';
	}
	else {
		$GLOBALS[$s]='N';
	}
}

function checkedIfY($qh,$field) 
{
	if (getVar($qh,$field) == 'Y') {
		print " CHECKED";
	}
}


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
	print '<OPTION VALUE="'.encHTML($p_value).'"';
	if (!strcmp($p_value, $p_selectedValue)) {
		print ' SELECTED';
	}
	print '>'.encHTML($p_printValue);
} // fn pcombovar


function encS($s) 
{
    return addslashes($s);
}

function decS($s) 
{
    return stripslashes($s);
}


function dSystem($s) 
{
//    print ("<BR>Executing <BR>$s<BR>");
    system($s);
}


/** 
 * An alias for "print()".
 * @param string $p_string
 * @return void
 */
function p($p_string) 
{
    print $p_string;
} // fn p


function ifTrueThenChecked($v) 
{
	if ($v) {
		echo ' CHECKED';
	}
}


function ifYthenCHECKED($q,$f) 
{
	if (getVar($q,$f)=='Y') {
		echo ' CHECKED';
	}
}


/**
 * Load the global and local language files.
 * @param string $p_path
 * @param string $p_name
 * @return void
 */
function selectLanguageFile($p_path, $p_name) 
{
    require_once('localizer/Localizer.php');
    Localizer::LoadLanguageFilesAbs($p_path, $p_name);
} // fn selectLanguageFile


function decSlashes($s) 
{
    return str_replace("%2F", "/", $s);
}

function encParam($s) 
{
    return str_replace("\"", "%22", str_replace("&", "%26", str_replace(";", "%3B", str_replace("%", "%25", $s))));
}

function printSelected($var_value, $current_value) 
{
	if ($var_value == $current_value) {
   		echo " selected";
	}
}

function pencParam($s) 
{
    print encParam($s);
}

function add_subs_section($publication_id, $section_nr) 
{
	// retrieve the default trial and paid time of the subscriptions
	$dd_query = "select TimeUnit, TrialTime, PaidTime from Publications where Id = "
	   . $publication_id;
	query($dd_query, 'dd');
	if ($GLOBALS['NUM_ROWS'] < 0) {
		return -1;
	}
	if ($GLOBALS['NUM_ROWS'] == 0) {
		return 0;
	}
	fetchRowNum($GLOBALS['dd']);
	$time_unit = getNumVar($GLOBALS['dd'], 0);
	$trial_time = getNumVar($GLOBALS['dd'], 1);
	$paid_time = getNumVar($GLOBALS['dd'], 2);
	
	switch($time_unit){
	case 'D':
		$trial_days = $trial_time;
		$paid_days = $paid_time;
		break;
	case 'W':
		$trial_days = $trial_time * 7;
		$paid_days = $paid_time * 7;
		break;
	case 'M':
		$trial_days = $trial_time * 30;
		$paid_days = $paid_time * 30;
		break;
	case 'Y':
		$trial_days = $trial_time * 365;
		$paid_days = $paid_time * 365;
		break;
	}
	
	$default_days['T'] = $default_paid_days['T'] = $trial_days;
	$default_days['P'] = $default_paid_days['P'] = $paid_days;
	
	// read active subscriptions to the given publication
	$subs_query = "select subs.Id, subs.Type, sect.StartDate, sect.Days, sect.PaidDays, "
	     . "abs(sect.SectionNumber - " . $section_nr . ") as sect_diff from "
	     . "Subscriptions as subs left join SubsSections as sect on subs.Id = "
	     . "sect.IdSubscription where subs.IdPublication = " . $publication_id
	     . " and subs.Active = 'Y' " . "order by subs.Id asc, sect_diff asc";
	query($subs_query, 'subs');
	$subs_nr = $GLOBALS['NUM_ROWS'];
	$subs_id = -1;
	$subs_type = "";
	$start_date = "";
	$days = -1;
	$paid_days = -1;
	$sect_diff = -1;
	$subs_count = 0;
	for ($index = 0; $index < $subs_nr; $index++) {
		fetchRowNum($GLOBALS['subs']);
		$n_subs_id = getNumVar($GLOBALS['subs'], 0);
		$n_subs_type = getNumVar($GLOBALS['subs'], 1);
		$n_start_date = getNumVar($GLOBALS['subs'], 2);
		$n_days = getNumVar($GLOBALS['subs'], 3);
		$n_paid_days = getNumVar($GLOBALS['subs'], 4);
		$n_sect_diff = getNumVar($GLOBALS['subs'], 5);
		
		if (($n_subs_id != $subs_id && $subs_id != -1) || $index == ($subs_nr - 1)) {
			if ($start_date == "") {
				$start_date = "now()";
				$days = $default_days[$subs_type];
				$paid_days = $default_paid_days[$subs_type];
			}
			else {
				$start_date = "'" . $start_date . "'";
			}
			$insert_query = "insert into SubsSections set IdSubscription = " . $subs_id
			         . ", SectionNumber = " . $section_nr . ", StartDate = "
			         . $start_date . ", Days = " . $days . ", PaidDays = " . $paid_days;
			query($insert_query);
			$subs_count ++;
			
			$sect_diff = 1;
			$subs_id = $n_subs_id;
			$subs_type = $n_subs_type;
			$start_date = $n_start_date;
			$days = $n_days;
			$paid_days = $n_paid_days;
			continue;
		}
	
		if ($n_sect_diff < $sect_diff || $sect_diff == -1) {
			$start_date = $n_start_date;
			$days = $n_days;
			$paid_days = $n_paid_days;
		}
		
		$subs_id = $n_subs_id;
		$subs_type = $n_subs_type;
		$sect_diff = $n_sect_diff;
	} // for
	
	return $subs_count;
} // fn add_subs_section

function del_subs_section($publication_id, $section_nr) 
{
	$subs_query = "select Id from Subscriptions where IdPublication = " . $publication_id;
	query($subs_query, 'subs');
	if ($GLOBALS['NUM_ROWS'] < 0) {
		return -1;
	}
	if ($GLOBALS['NUM_ROWS'] == 0) {
		return 0;
	}
	$subs_nr = $GLOBALS['NUM_ROWS'];
	for ($index = 0; $index < $subs_nr; $index++) {
		fetchRowNum($GLOBALS['subs']);
		$subs_id = getNumVar($GLOBALS['subs'], 0);
		$del_query = "delete from SubsSections where IdSubscription = " . $subs_id
		     . " and SectionNumber = " . $section_nr;
		query($del_query);
	}
	return $index;
}

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


function valid_field_name($name) 
{
	if (strlen($name) == 0) {
		return false;
	}
	for ($i = 0; $i < strlen($name); $i++) {
		$c = $name[$i];
		$ok = ($c >= 'A' && $c <= 'Z') || ($c >= 'a' && $c <= 'z') || $c == '_';
		if (!$ok) {
		  return false;
		}
	}
	return true;
}

function valid_short_name($name) 
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
}

global $cache_type_all, $cache_type_publications, $cache_type_topics, $cache_type_article_types;
global $operation_attr, $operation_create, $operation_delete, $operation_modify;

$cache_type_all = 'all';
$cache_type_publications = 'publications';
$cache_type_topics = 'topics';
$cache_type_article_types = 'article_types';

$operation_attr = 'operation';
$operation_create = 'create';
$operation_delete = 'delete';
$operation_modify = 'modify';

function build_reset_cache_msg($type, $parameters)
{
	$msg = "<CampsiteMessage MessageType=\"ResetCache\">\n";
	$msg .= "\t<CacheType>" . htmlspecialchars($type) . "</CacheType>\n";
	$msg .= "\t<Parameters>\n";
	if (is_array($parameters))
		foreach($parameters as $name=>$value)
			$msg .= "\t\t<Parameter Name=\"" . htmlspecialchars($name) . "\">" 
			     . htmlspecialchars($value) . "</Parameter>\n";
	$msg .= "\t</Parameters>\n";
	$msg .= "</CampsiteMessage>\n";
	$size = sprintf("%04x", strlen($msg));
	$msg = "0002 " . $size . " " . $msg;
//	echo "<pre>sending ".htmlentities($msg)."</pre>";
	return $msg;
}

function build_restart_server_msg()
{
	$msg = "<CampsiteMessage MessageType=\"RestartServer\">\n</CampsiteMessage>\n";
	$size = sprintf("%04x", strlen($msg));
	$msg = "0003 " . $size . " " . $msg;
	return $msg;
}

function server_port()
{
	global $Campsite;

	return $Campsite['PARSER_PORT'];
}

function send_message($address, $server_port, $msg, &$err_msg, $socket = false, $close_socket = true)
{
	if (!$socket) {
		@$socket = fsockopen($address, $server_port, $errno, $errstr, 30);
		if (!$socket) {
			$err_msg = "Unable to connect to server: " . $errstr . " (" . $errno . ")";
			return false;
		}
	}

	fwrite($socket, $msg);
	fflush($socket);
	if ($close_socket) {
		fclose($socket);
		return true;
	}
	return $socket;
}

function verify_templates($templates_dir, &$missing_templates, &$deleted_templates, &$errors)
{
	$templates_dir = trim($templates_dir);
	if (!is_dir($templates_dir))
		return $templates_dir . "is not a valid directory";
	if ($templates_dir[strlen($templates_dir) - 1] != '/')
		$templates_dir .= '/';
	$templates_dir = str_replace("//", "/", $templates_dir);

	$sql = "select * from Templates order by Level, Name";
	if (!($res = mysql_query($sql))) {
		$errors[] = "Unable to read templates from the database";
		return false;
	}
	while ($row = mysql_fetch_array($res)) {
		$id = 0 + $row['Id'];
		$name = $row['Name'];

		$file_path = $templates_dir . $name;
		if (!is_file($file_path))
		{
			$used = template_is_used($name);
			if ($used) {
				$missing_templates[$id] = $name;
			} else {
				$sql = "delete from Templates where Id = " . $id;
				if (!mysql_query($sql)) {
					$errors = "Unable to delete template " . $name . " from the database";
					continue;
				}
				$deleted_templates[$id] = $name;
			}
		}
	}
	return true;
}

function register_templates($dir, &$errors, $root_dir = "", $level = 0)
{
	if ($root_dir == "")
		$root_dir = $dir;

	$dir = trim($dir);
	$root_dir = trim($root_dir);
	$dir = str_replace("//", "/", $dir);
	$root_dir = str_replace("//", "/", $root_dir);
	if ($dir[strlen($dir)-1] == '/')
		$dir = substr($dir, 0, strlen($dir) - 1);
	if ($root_dir[strlen($root_dir)-1] == '/')
		$root_dir = substr($root_dir, 0, strlen($root_dir) - 1);
	if (!$dh = @opendir($dir)) {
		$errors[] = "Unable to open directory " . $dir;
		return -1;
	}

	$count = 0;
	while ($file = readdir($dh)) {
		if ($file == "." || $file == "..")
			continue;

		$full_path = $dir . "/" . $file;
		$filetype = filetype($full_path);
		if ($filetype == "dir") {
			$count += register_templates($full_path, $errors, $root_dir, $level + 1);
			continue;
		}

		if ($filetype != "file") // ignore special files and links
			continue;
		$ending = substr($file, strlen($file) - 4);
		if ($ending != ".tpl") // ignore files that are not templates (end in .tpl)
			continue;

		$rel_path = substr($full_path, strlen($root_dir) + 1);
		$sql = "select count(*) as nr from Templates where Name = '" . $rel_path . "'";
		if (!($res = mysql_query($sql))) {
			$errors[] = "Unable to read from the database";
			continue;
		}
		$row = mysql_fetch_array($res);
		if ($row['nr'] > 0)
			continue;
		$sql = "insert ignore into Templates (Name, Level) values('"
		     . $rel_path . "', " . $level . ")";
		if (!mysql_query($sql))
			$errors[] = "Unable to insert template " . $rel_path;
		$count++;
	}
	return $count;
}

function template_path($path, $name)
{
	$look_dir = "/look";

	$path = str_replace("//", "/", $path);
	$path = strstr($path, $look_dir);
	if (strncmp($path, $look_dir, strlen($look_dir)) == 0)
		$path = substr($path, strlen($look_dir));
	if ($path[0] == '/')
		$path = substr($path, 1);
	if ($path[strlen($path) - 1] == '/')
		$path = substr($path, 0, strlen($path) - 1);

	$name = str_replace("//", "/", $name);
	if ($name[0] == '/')
		$name = substr($name, 1);

	if ($path != "")
		$template_path = $path . "/" . $name;
	else
		$template_path = $name;
	return $template_path;
}

function template_is_used($template_name)
{
	$sql = "select * from Templates where Name = '" . $template_name . "'";
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	if (!$row)
		return false;
	$id = $row['Id'];

	$sql = "select count(*) as used_count from Issues where IssueTplId = " . $id
	     . " or SectionTplId = " . $id . " or ArticleTplId = " . $id;
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	if ($row['used_count'] > 0)
		return true;

	$sql = "select count(*) as used_count from Sections where SectionTplId = " . $id
	     . " or ArticleTplId = " . $id;
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	if ($row['used_count'] > 0)
		return true;

	return false;
}


if (file_exists($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/modules/admin/priv_functions.php")) {
	include ($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/modules/admin/priv_functions.php");
}

?>