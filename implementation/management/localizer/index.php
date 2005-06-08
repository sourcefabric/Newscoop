<?php
require_once($_SERVER['DOCUMENT_ROOT']."/configuration.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/CampsiteInterface.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Input.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/common.php");
load_common_include_files("$ADMIN_DIR/localizer");
require_once('Localizer.php');

global $g_translationStrings;
global $g_localizerConfig;

$action = Input::Get('action', 'string', 'translate', true);
$base = Input::Get('base', 'string', $g_localizerConfig['FILENAME_PREFIX'], true);
$directory = Input::Get('dir', 'string', $g_localizerConfig['BASE_DIR'], true);
if ($directory == '/globals') {
	$directory = '/';
	$base = 'globals';
}

//echo "<pre>";
//print_r($g_translationStrings);
//print_r($_REQUEST);
//echo "</pre>";
?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
<TR>
    <TD class="page_title">
        <?php  putGS("Localizer"); ?>
    </TD>
</TR>       
</TABLE>

<?php
//echo "Action: $action<br>";
switch ($action) {
	case 'translate':
	    require_once("translate.php");    
	    translationForm($_REQUEST);
		break;
	
	case 'save_translation':
	    $targetLanguageId = Input::Get('localizer_target_language');
	    $data = Input::Get('data', 'array');
	    Localizer::ModifyStrings($base, $directory, $targetLanguageId, $data);
	    // Localizer strings are changed -> reload files
	    Localizer::LoadLanguageFiles('/', 'globals');
	    Localizer::LoadLanguageFiles('/localizer', 'locals'); 
	    require_once("translate.php");    
	    translationForm($_REQUEST);
		break;
		
	case 'remove_string':
	    $deleteMe = Input::Get('string', 'string');
	    Localizer::RemoveString($base, $directory, $deleteMe);
	    require_once("translate.php");    
	    translationForm($_REQUEST);
		break;
	
	case 'move_string':
		$pos1 = Input::Get('pos1', 'int');
		$pos2 = Input::Get('pos2', 'int');
	    Localizer::MoveString($base, $directory, $pos1, $pos2);
	    require_once("translate.php");    
	    translationForm($_REQUEST);
		break;
	
	case 'add_missing_translation_strings':
		$missingStrings = Localizer::FindMissingStrings($directory);
	    if (count($missingStrings) > 0) {
	        Localizer::AddStringAtPosition($base, $directory, 0, $missingStrings);
	    }
	    require_once("translate.php");    
	    translationForm($_REQUEST);
		break;
		
	case 'delete_unused_translation_strings':
		$unusedStrings = Localizer::FindUnusedStrings($directory);
	    if (count($unusedStrings) > 0) {
	       	Localizer::RemoveString($base, $directory, $unusedStrings);
	    }
	    require_once("translate.php");    
	    translationForm($_REQUEST);
		break;
	
	//case 'add_string':
	//	$pos = Input::Get('pos');
	//	if ($pos == 'begin') {
	//		$pos = 0;
	//	}
	//	elseif ($pos == 'end') {
	//		$pos = null;
	//	}
	//
	//    $msg = Localizer::CompareKeys($directory, $_REQUEST['newKey']);
	//    if (count($msg) > 0) {
	//        foreach ($msg as $val => $err) {
	//            while ($key = array_search($val, $_REQUEST['newKey'])) {
	//                unset($_REQUEST['newKey'][$key]);
	//            }
	//        }
	//    }
	//	// skip if all was unset above
	//    if (count($_REQUEST['newKey'])) {  
	//        Localizer::AddStringAtPosition($base, $directory, $pos, $_REQUEST['newKey']);
	//    }
	//
	//    require_once("translate.php");
	//    translationForm($_REQUEST);
	//	break;

} // switch

?>
</body>
</html>