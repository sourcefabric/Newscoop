<?php
require_once($_SERVER['DOCUMENT_ROOT']."/configuration.php");
require_once($Campsite['HTML_DIR'] . "/$ADMIN_DIR/languages.php");

foreach ($_REQUEST as $key=>$val)
  $GLOBALS[$key] = $val;

// function regGS($key,$value){
//     global $lang,$base,$trans;
//     if ($lang=='base'){
//         $base[$key]=$value;
//     }
//     else{
//  $trans[$key]=$value;
//     }
// }


    $langshort=substr($file,strlen($file)-6,2);

    //if the language is not registered, set the charset to english
    if (!isset($languages[$langshort])){
 $langshort='en';
    }


$fn=$Campsite['HTML_DIR'] . "/$ADMIN_DIR/$dir/$file";
$enfn=substr($fn,0,strlen($fn)-6).'en.php';
print "$dir / $file<HR><FORM method=post action=save.php>";


$gs = array();
$lang='base';
include("$enfn");
$base = $gs;
$gs = array();
$lang='trans';
include($fn);
$trans = $gs;

echo "<p>enfn: $enfn</p>\n";
echo "<p>fn: $fn</p>\n";

$nr=0;
while (list ($key,$value)=each($base)){
    if (isset($trans[$key]) && (trim($trans[$key])!='')){
 $insval=$trans[$key];
 $pre='';
 $post='';
    }
    else{
 $insval='';
 $pre="<FONT COLOR=red>";
 $post="</FONT>";
    }

    if ($key!=''){
        print "$pre$value$post<BR>\n";
        print "<input name=base$nr type=hidden value=\"$key\">\n";
 print "<input name=translation$nr type=text size=50 value=\"$insval\"><BR><HR>\n";
        $nr++;
    }
}

print "<INPUT TYPE=hidden name=pieces value=$nr>";
print "<INPUT TYPE=hidden name=destfile value='$fn'>";

?>

<input type=submit Value='save'>
</form>
