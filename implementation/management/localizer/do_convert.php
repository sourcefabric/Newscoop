<?PHP
$languages = Localizer::GetLanguages('php');

//echo "<pre>";
//print_r($languages);
//echo "</pre>";

// recursive convert GS-files to XML-files on filesystem
$startdir = LOCALIZER_BASE_DIR.LOCALIZER_ADMIN_DIR;
$pattern  = '/^(locals|globals)\.[a-z]{2,2}\.php$/';
$sep = "|";
$list = Localizer::SearchFilesRecursive($startdir, $pattern, $sep);
$list = explode($sep, $list);
?>
<center>
<?php putGS("Converting..."); ?><br>
<div style="width: 700px; height: 400px; overflow: auto; border: 1px solid black;">
<table width="100%" cellpadding="3" cellspacing="0">
<?php
$count = 0;
foreach ($list as $pathname) {
    if ($pathname) {
        $pathname = str_replace($startdir, '', $pathname);
        $filenameParts = explode('.', basename($pathname));
        $base = $filenameParts[0];
		$directory = dirname($pathname);
        foreach ($languages as $lang) {
            if ($lang['Code'] == $filenameParts[1]) {
                $languageCode = $lang['Id'];
                $sourceFile =& new LocalizerLanguage($base, $directory, $languageCode);
                $sourceFile->loadGsFile();
                $origFile = $sourceFile->getSourceFile();
                $sourceFile->saveAsXml();
                
                // Verify that the saved file is the same as the original.
                $copyLanguage =& new LocalizerLanguage($base, $directory, $languageCode);
                $loadSuccess = $copyLanguage->loadXmlFile();

                if ($count++ % 2 == 0) {
                	$cssClass = "list_row_even";
                }
                else {
                	$cssClass = "list_row_odd";
                }
                echo '<tr><td align="left" style="border-top: 1px solid black; border-left: 1px solid black;" class="'.$cssClass.'">'.$pathname."</td>";
				echo '<td style="border-top: 1px solid black; border-right: 1px solid black;" class="'.$cssClass.'">';
                if (!$copyLanguage->equal($sourceFile) || ($copyLanguage->getNumStrings() <= 0) || !$loadSuccess) {
                	echo "<font color='red'>";
                	putGS("fail"); 
                	echo "</font>";                    	
                }
                else {
                	echo "<font color='green'>";
                	putGS("success");
                	echo "</font>";
                	@unlink($origFile);
                }
                Localizer::FixPositions($file['base'], $file['dir']);
                echo "</td></tr>";
            }
        }
    }
}
?>
</table>
</div>
<br>
<form>
<INPUT type="button" value="<?php putGS("OK"); ?>" onclick="this.form.submit();">
</form>
