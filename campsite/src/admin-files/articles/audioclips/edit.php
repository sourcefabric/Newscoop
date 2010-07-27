<?php
camp_load_translation_strings("article_audioclips");
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Audioclip.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Language.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/include/getid3/getid3.php');

if (SystemPref::Get("UseCampcasterAudioclips") != 'Y') {
    camp_html_display_error(getGS("Campcaster integration is disabled"), null, true);
    exit;
}

if (!$g_user->hasPermission('AddAudioclip')) {
	camp_html_display_error(getGS('You do not have the right to add audioclips.' ), null, true);
	exit;
}

$f_publication_id = Input::Get('f_publication_id', 'int', 0, true);
$f_issue_number = Input::Get('f_issue_number', 'int', 0, true);
$f_section_number = Input::Get('f_section_number', 'int', 0, true);
$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_language_specific = Input::Get('f_language_specific', 'string', null, true);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_audioclip_id = Input::Get('f_audioclip_id', 'string', null, true);
$f_action = Input::Get('f_action', 'string', null, true);

$BackLink = Input::Get('BackLink', 'string', null, true);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), null, true);
	exit;
}

switch($f_action) {
    case 'add':
    	if (!SecurityToken::isValid()) {
    		camp_html_display_error(getGS('Invalid security token!'));
    		exit;
    	}

    	if (empty($_FILES['f_media_file']) || !isset($_FILES['f_media_file']['name'])
        		|| !isset($_FILES['f_media_file']['tmp_name'])
        		|| $_FILES['f_media_file']['name'] == ''
        		|| $_FILES['f_media_file']['tmp_name'] == '') {
            camp_html_display_error(getGS('Invalid file parameter'), null, true);
            exit;
        }
        $aClipObj = new Audioclip();
        $audioFile = $aClipObj->onFileUpload($_FILES['f_media_file']);
        if (PEAR::isError($audioFile)) {
            camp_html_display_error(getGS('Audio file could not be stored locally'), null, true);
            exit;
        }
        $id3Data = Audioclip::AnalyzeFile($audioFile);
        $s = $id3Data['playtime_seconds'];
        if (isset($id3Data['id3v1']['title']) && !empty($id3Data['id3v1']['title'])) {
            $mData['title'] = $id3Data['id3v1']['title'];
        } else {
            $mData['title'] = $id3Data['filename'];
        }
        $mData['creator'] = $id3Data['id3v1']['artist'];
        $mData['source'] = $id3Data['id3v1']['album'];
        $mData['type'] = $id3Data['id3v1']['genre'];
        $mData['extent'] = date('H:i:s', floor($s)-date('Z')).substr(number_format($s, 6), strpos(number_format($s, 6), '.'));
        $mData['bitrate'] = $id3Data['audio']['bitrate'];
        $mData['sample_rate'] = $id3Data['audio']['sample_rate'];
        break;
    case 'edit':
        $audioClip = new Audioclip($f_audioclip_id);
        $aClipMetaTags = $audioClip->getAvailableMetaTags();
        foreach ($aClipMetaTags as $metaTag) {
            list($nameSpace, $localPart) = explode(':', strtolower($metaTag));
            $mData[$localPart] = $audioClip->getMetatagValue($localPart);
        }
        break;
}

?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="Expires" content="now" />
	<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css" />
    <?php include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php"); ?>
    <script language="JavaScript">
    function spread(element, name)
    {
        if (document.forms['audioclip_metadata'].elements['f_Main_'+name]) document.forms['audioclip_metadata'].elements['f_Main_'+name].value = element.value;
        if (document.forms['audioclip_metadata'].elements['f_Music_'+name]) document.forms['audioclip_metadata'].elements['f_Music_'+name].value = element.value;
        if (document.forms['audioclip_metadata'].elements['f_Voice_'+name]) document.forms['audioclip_metadata'].elements['f_Voice_'+name].value = element.value;
    } // fn spread
    </script>
	<title><?php putGS("Edit Audioclip Metadata"); ?></title>
</head>
<body>
<?php camp_html_display_msgs(); ?>

<form name="audioclip_metadata" id="audioclip_metadata" method="POST" action="/<?php echo $ADMIN; ?>/articles/audioclips/do_<?php echo $f_action; ?>.php" enctype="multipart/form-data" onsubmit="return validateForm(this, 0, 1, 0, 0, 8);">
<?php echo SecurityToken::FormParameter(); ?>
<p>
<table style="margin-top: 10px; margin-left: 5px; margin-right: 5px;" cellpadding="0" cellspacing="0">
<tr>
	<?php if ($g_user->hasPermission('AddAudioclip')) { ?>
	<td id="link1" class="tab_current"><a href="#" onclick="javascript:selectTab('1', '3');"><img src="<?php p($Campsite['ADMIN_IMAGE_BASE_URL']); ?>/add.png" border="0"><b><?php putGS("Main"); ?></b></a></td>
	<?php } ?>

	<td id="link2" class="tab_normal"><a href="#" onclick="javascript:selectTab('2', '3');"><img src="<?php p($Campsite['ADMIN_IMAGE_BASE_URL']); ?>/add.png" border="0"><b><?php putGS("Music"); ?></b></a></td>

    <td id="link3" class="tab_normal" style="border-right: 1px solid #8baed1;"><a href="#" onclick="javascript:selectTab('3', '3');"><img src="<?php p($Campsite['ADMIN_IMAGE_BASE_URL']); ?>/add.png" border="0"><b><?php putGS("Voice"); ?></b></a></td>
</tr>
<tr>
    <td colspan="3" style="background-color: #EEE; padding-top: 5px; border-bottom: 1px solid #8baed1; border-right: 1px solid #8baed1; border-left: 1px solid #8baed1;">
        <br />
        <table cellpadding="5" cellspacing="0" class="table_input">
        <tr id="tab1">
            <td>
                <?php include('main_metadata.php'); ?>
            </td>
        </tr>
        <tr id="tab2" style="display: none;">
            <td>
                <?php include('music_metadata.php'); ?>
            </td>
        </tr>
        <tr id="tab3" style="display: none;">
            <td>
                <?php include('voice_metadata.php'); ?>
            </td>
        </tr>
        <tr>
            <td>
                <div align="center">
                    <input type="hidden" name="f_publication_id" value="<?php p($f_publication_id); ?>" />
                    <input type="hidden" name="f_issue_number" value="<?php p($f_issue_number); ?>" />
                    <input type="hidden" name="f_section_number" value="<?php p($f_section_number); ?>" />
                    <input type="hidden" name="f_article_number" value="<?php p($f_article_number); ?>" />
                    <input type="hidden" name="f_language_id" value="<?php p($f_language_id); ?>" />
                    <input type="hidden" name="f_language_selected" value="<?php p($f_language_selected); ?>" />
                    <input type="hidden" name="f_language_specific" value="<?php p($f_language_specific); ?>" />
                    <input type="hidden" name="BackLink" value="<?php p($_SERVER['REQUEST_URI']); ?>" />
                <?php if ($f_action == 'add') { ?>
                    <input type="hidden" name="f_audiofile" value="<?php p($audioFile); ?>" />
                <?php } elseif ($f_action == 'edit') { ?>
                    <input type="hidden" name="f_audioclip_id" value="<?php p($f_audioclip_id); ?>" />
                <?php } ?>
                    <input type="submit" name="Save" value="<?php putGS('Save'); ?>" class="button" />
                <?php if ($f_action == 'edit') { ?>
                    <input type="button" name="Cancel" value="<?php putGS('Cancel'); ?>" class="button" onclick="window.close();" />
                <?php } ?>
                </div>
            </td>
        </tr>
        </table>
        <br />
    </td>
</tr>
</table>

</form>

</body>
</html>