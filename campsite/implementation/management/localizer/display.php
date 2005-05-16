<?php
require_once($_SERVER['DOCUMENT_ROOT']."/configuration.php");
require_once($Campsite['HTML_DIR'] . "/$ADMIN_DIR/languages.php");

foreach ($_REQUEST as $key=>$val)
	$GLOBALS[$key] = $val;

$langshort=substr($file,strlen($file)-6,2);

//if the language is not registered, set the charset to english
if (!isset($languages[$langshort])) {
	$langshort='en';
}

$fn = $Campsite['HTML_DIR'] . "/$ADMIN_DIR/$dir/$file";
$enfn = substr($fn,0,strlen($fn)-6).'en.php';
echo "<b>" . substr($dir, 1) . "/" . $file . "</b>\n<HR>\n";
echo "<FORM method=post action=index.php>\n";

$gs = array();
$lang='base';
include("$enfn");
$base = $gs;
$gs = array();
$lang='trans';
include($fn);
$trans = $gs;

$nr=0;
while (list ($key,$value)=each($base)) {
	if (isset($trans[$key]) && (trim($trans[$key])!='')) {
		$insval=$trans[$key];
		$pre='';
		$post='';
	} else {
		$insval='';
		$pre="<FONT COLOR=red>";
		$post="</FONT>";
	}

	if ($key!='') {
		print "$pre$value$post<BR>\n";
		$key = htmlspecialchars($key);
		$insval = htmlspecialchars($insval);
		print "<input name=base$nr type=hidden value=\"$key\">\n";
		print "<input name=translation$nr type=text class=\"input_text\" size=50 value=\"$insval\"><BR><HR>\n";
		$nr++;
	}
}

print "<INPUT TYPE=hidden name=pieces value=$nr>";
print "<INPUT TYPE=hidden name=destfile value='$fn'>";

?>

<input type="submit" name="save" value="save" class="button">
</form>
