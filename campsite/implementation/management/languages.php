<?php 
global $Campsite;
$tmpLanguages = $Campsite['db']->GetAll('SELECT Code, Name, CodePage, OrigName FROM Languages');
$languages = array();
foreach ($tmpLanguages as $tmpLanguage) {
    $languages[$tmpLanguage['Code']] = array ('name' => $tmpLanguage['Name'], 
        'charset' => $tmpLanguage['CodePage'], 'orig_name' => $tmpLanguage['OrigName']);
}
unset($tmpLanguages);
unset($tmpLanguage);

//function registerLanguage($p_name, $p_code, $p_charset, $p_origName = null){
//
//	global $languages;
//	$languages["$p_code"]=array("name"=>$p_name,"charset"=>$p_charset,"orig_name"=>$p_origName);
//}
//registerLanguage('Austrian', 'at', 'IS0_8859-1', 'Deutsch (Österreich)');
//registerLanguage('Croatian', 'hr', 'ISO_8859-2', 'Hrvatski');
//registerLanguage('Czech', 'cz', 'ISO_8859-2', 'Český');
//registerLanguage('English', 'en', 'ISO_8859-1', 'English');
//registerLanguage('German', 'de', 'ISO_8859-1', 'Deutsch');
//registerLanguage('Portuguese', 'pt', 'ISO_8859-1', 'Português');
//registerLanguage('Romanian', 'ro', 'ISO_8859-2', 'Română');
//registerLanguage('Russian', 'ru', 'ISO_8859-5', 'Русский');
//registerLanguage('Sebian (Cyrillic)', 'sr', 'ISO_8859-5', 'Српски (Ћирилица)');
//registerLanguage('Serbo-Croatian', 'sh', 'ISO_8859-2', 'Srpskohrvatski');
//registerLanguage('Spanish', 'es', 'ISO_8859-1', 'Español');

?>