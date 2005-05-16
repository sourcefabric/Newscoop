<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');

function writeFile($newfile, $fen, &$for, &$en)
{
	$s = "<?php \n";
	while(is_array($en) && list($key, $value) = each($en)) {
		if (isset($for["$key"])) {
			$newval = $for["$key"];
		} else {
			$newval = '';
		}
		$key = str_replace('"', '\"', stripslashes($key));
		$newval = str_replace('"', '\"', stripslashes($newval));
		$s .= "regGS(\"$key\",\"$newval\");\n";
	}
	$s .= "\n?>";
//	echo "Writing to $fen: $s\n";
	$fh = fopen($newfile, 'w');
	fwrite($fh, $s);
	fclose($fh);
}

function createArrays($fen, $dirname, $newlang)
{
	global $gs;

	$gs = array();
	include("$dirname/$fen"."en.php");
	$en = $gs;

	$gs = array();
	$foreignfile = "$dirname/$fen$newlang.php";
	include("$foreignfile");
	$for = $gs;

	writeFile($foreignfile, $fen, $for, $en);
}

function verifyFile($fen,$dirname,$newlang)
{
	$check = "$dirname/$fen$newlang.php";
	$fh = @fopen($check,'r');
	if ($fh != null) {
		fclose($fh);
	} else {
//		echo "Creating empty $fen\n";
		$fh = fopen($check, 'w');
		fwrite($fh, "<?php \n?>");
		fclose($fh);
	}
	createArrays($fen, $dirname, $newlang);
}

function parseFolder($dirname, $depth)
{
	global $createnew, $newlang, $langarray, $Campsite, $ADMIN;

	$full_dir = $Campsite['HTML_COMMON_DIR'] . "/priv/$dirname";
	$handle = opendir($full_dir);
	$space = 3;
	$files = array();
	$dirs = array();
	while (($file = readdir($handle))!==false) {
		$fullname = "$full_dir/$file";
		if (is_dir($fullname) && $file != "." && $file != "..") {
			$dirs[] = $file; // filling the directories array
		}
		if (is_file($fullname)
			&& (strpos($file, 'locals') === 0 || strpos($file, 'globals') === 0)
			&& substr($file, strlen($file) - 4) === '.php') {
			$files[] = $file; // filling the files array
		}
	}
	sort($files);
	sort($dirs);
	if (sizeof($files) > 0) {
		if ($depth > 0)
			echo substr(strtoupper($dirname), 1)."\n";
//			print str_repeat(' ',($depth-1)*$space).strtoupper($dirname)."\n";
		foreach ($files as $index=>$filen) {

			if ((strpos($filen, 'locals') === 0 || strpos($filen, 'globals') === 0)
				&& substr($filen, strlen($filen) - 6) == 'en.php') {
				$filesinen[] = $filen;
			}

			if ((strpos($filen, 'locals') === 0 || strpos($filen, 'globals') === 0)
				&& substr($filen, strlen($filen) - 4) === '.php'
				&& (substr($filen, strlen($filen) - 6) != 'en.php'
					|| substr($dirname, 1, 7) == 'modules')
				//enable editing english files for modules
				) {
				if (strpos($filen, 'globals') === 0) {
					$langarray[] = substr($filen,8,2);
				}

				print str_repeat(' ', $depth*$space)."<a href='/$ADMIN/localizer/?display=on&file=$filen&dir=$dirname'>$filen</a>\n";
				usleep (0); // usleep(0) seems to be senseless, but needed because
					        // of a strange behavior of PHP in combination with IE
			}
		} //for

		if (isset($filesinen) && ($createnew)) {
			foreach($filesinen as $fen) {
				verifyFile(substr($fen, 0, strlen($fen) - 6), $full_dir, $newlang);
			}
		}
	}
	if (isset($dirs) && is_array($dirs)) {
		for($i = 0; $i < count($dirs); $i++) {
			parseFolder("$dirname/".$dirs[$i], $depth + 1);
		}
	}
}


if (isset($_REQUEST['newlang'])) {
	$newlang = trim($_REQUEST['newlang']);
}

if ( (isset($newlang)) && ($newlang!='') && (strlen($newlang)>1) &&($newlang!='en') ) {
	$createnew=true;
//	echo "creating files for: $newlang";
	$langarray[]=$newlang;
}else{
	$createnew=false;
}
$langarray[]='en';
$langfile='';

$langfile.="<?php \n\n";
$langfile.='function registerLanguage($p_name, $p_code, $p_charset, $p_origName = null){'."\n\n";
$langfile.="\t".'global $languages;'."\n";
$langfile.="\t".'$languages["$p_code"]=array("name"=>$p_name,"charset"=>$p_charset,"orig_name"=>$p_origName);'."\n";
$langfile.='}'."\n";


print '<PRE>';
parseFolder("", 0);
print '</PRE>';

?>
<form action="index.php" method="post">

<SELECT NAME="newlang" class="input_select">
<?php

$Languages=mysql_query ("SELECT Id, Name, OrigName, CodePage, Code FROM Languages ORDER BY Name");
$NUM_ROWS=mysql_num_rows($Languages);
if ($NUM_ROWS) {
	$nr= $NUM_ROWS;
	for($loop=0;$loop<$nr;$loop++) {
		$arr=mysql_fetch_array($Languages,MYSQL_ASSOC);
		if ($arr['Code']!='en') {
			print '<OPTION VALUE="'.$arr['Code'].'">'.$arr['OrigName'].'('.$arr['Code'].")\n";
		}
		if (in_array($arr['Code'],$langarray)) {
			$langfile.='registerLanguage(\''. $arr['Name']."', '".$arr['Code']."', '".$arr['CodePage']."', '".$arr['OrigName']."');\n";
		}
	}
}
$langfile.="\n".'?>';

?>
</select>
<p><input type=submit value='create language files' class="button"></p>
</form>

<?php
if ($createnew) {
	$langf = fopen($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/languages.php", 'w');
	fwrite($langf,$langfile);
	fclose($langf);
}
if ($createnew && false){ ?>
    <SCRIPT>
 document.location.href='index.php';
    </SCRIPT>
<?php
}
?>