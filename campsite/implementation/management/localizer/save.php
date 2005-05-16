<?php 

foreach (array_merge ($_POST, $_GET) as $key=>$val)
	$GLOBALS[$key] = $val;

$sb = '';
for ($i = 0; $i < $pieces; $i++) {
	$var1 = "base$i";
	$var2 = "translation$i";
	$var1 = str_replace('"', '\"', stripslashes($$var1));
	$var2 = str_replace('"', '\"', stripslashes($$var2));
	$sb .= "regGS(\"$var1\",\"$var2\");\n";
}

$fh = fopen($destfile, 'w');
fputs($fh, "<?php \n\n$sb\n\n?>");
fclose($fh);

?>
