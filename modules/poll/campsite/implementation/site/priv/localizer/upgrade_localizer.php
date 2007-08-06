<?PHP
require_once('../../management/localizer/Localizer.php');

global $g_localizerConfig;
$languages = Localizer::GetAllLanguages('gs');
//echo "<pre>";
//print_r($languages);
//echo "</pre>";

// recursive convert GS-files to XML-files on filesystem
$startdir = $g_localizerConfig['BASE_DIR'];
$pattern  = '/^(locals|globals)\.[a-z]{2,2}\.php$/';
$sep = "|";
$list = Localizer::SearchFilesRecursive($startdir, $pattern, $sep);
$list = explode($sep, $list);

echo "Converting translation files...";
$count = 0;
$allLanguageIds = array();

// Go through all the files
foreach ($list as $pathname) {
    if ($pathname) {
        // Get the relative path name from server_root
        $pathname = str_replace($startdir, '', $pathname);
        $filenameParts = explode('.', basename($pathname));
        // prefix will be 'locals' or 'globals'
        $prefix = $filenameParts[0];
        $directory = dirname($pathname);
        
        // Find the language that matches this file.
        foreach ($languages as $lang) {
            if ($lang['Code'] == $filenameParts[1]) {
                $twoLetterCode = $lang['Code'];
                $sourceFile =& new LocalizerLanguage($prefix, $twoLetterCode);
                $sourceFile->loadGsFile();
                switch ($twoLetterCode) {
                case 'en':
                    $languageCode = 'en_US';
                    break;
                case 'cz':
                    $languageCode = 'cs_CZ';
                    break;
                case 'at':
                    $languageCode = 'de_AT';
                    break;
                case 'sh':
                    // Serbian is for yugoslavia
                    // See: http://www.niso.org/standards/resources/3166.html#serbia
                    $languageCode = 'sh_YU';
                    break;
                case 'he':
                    // Hebrew was completely wrong.
                    $languageCode = 'iw_IL';
                    break;
                case 'zh':
                    $languageCode = 'zh_CN';
                    break;
                default:
                    // For de, pt, fr, es, it, ro, hr, ru.
                    $languageCode = strtolower($twoLetterCode).'_'.strtoupper($twoLetterCode);                
                }
                // Keep a list of all the language codes.
                $allLanguageIds[$languageCode] = $languageCode;
                
                // Save the name of the loaded file so we can delete it later.
                $origFile = $sourceFile->getSourceFile();
                
                // Change the language code to the new format.
                $sourceFile->setLanguageId($languageCode);
                
                // Save in the new format.
                $sourceFile->saveAsXml();
                
                // Verify that the saved file is the same as the original.
                $copyLanguage =& new LocalizerLanguage($prefix, $languageCode);
                $loadSuccess = $copyLanguage->loadXmlFile();

                echo $pathname."\n";
                if (!$copyLanguage->equal($sourceFile) || ($copyLanguage->getNumStrings() <= 0) || !$loadSuccess) {
                    echo "FAIL"; 
                }
                else {
                    echo "SUCCESS";
                    @unlink($origFile);
                }
            }
        }
    }
}

// Save the languages to the languages.xml file.
//$xmlSerializer =& new XML_Serializer();
//$xmlSerializer->serialize($allLanguageIds);
//$data = $xmlSerializer->getSerializedData();
//$handle = fopen(LOCALIZER_BASE_DIR.'/'.LOCALIZER_LANGUAGE_METADATA_FILENAME, "w");
//fwrite($handle, $data);
//fclose($handle);
//
//clearstatcache();
//Localizer::FixPositions($file['base'], $file['dir']);
//
?>