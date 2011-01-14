<?php
global $Campsite;
$tmpLanguages = $g_ado_db->GetAll('SELECT Code, Name, CodePage, OrigName FROM Languages');
$languages = array();
foreach ($tmpLanguages as $tmpLanguage) {
    $languages[$tmpLanguage['Code']] = array ('name' => $tmpLanguage['Name'],
        'charset' => $tmpLanguage['CodePage'], 'orig_name' => $tmpLanguage['OrigName']);
}
unset($tmpLanguages);
unset($tmpLanguage);
?>