<?PHP
$startdir = LOCALIZER_BASE_DIR.LOCALIZER_ADMIN_DIR;
$pattern  = '/^(locals|globals)\.[a-z]{2,2}\.php$/';
$sep = "|";
$list = Localizer::SearchFilesRecursive($startdir, $pattern, $sep);
$list = explode($sep, $list);

?>
<center>
<b>Before using the new localizer, you must first convert the language file format.<b><br>
The following files will be converted:<br>
<div style="width: 700px; height: 400px; overflow: auto; border: 1px solid black;">
<table>
<?php
foreach($list as $pathname) {
	echo "<tr><td>$pathname</td></tr>";
}
?>
</table>
</div>
<br>
<form>
<input type="hidden" name="action" value="convert_confirm">
<input type="submit" name="submit_button" value="<?php putGS("Click Here to Convert"); ?>" class="button">
</form>
</center>
