<?php
camp_load_translation_strings("article_audioclips");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Audioclip.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Language.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/include/getid3/getid3.php');

if (!$g_user->hasPermission('AddAudioclip')) {
	camp_html_display_error(getGS('You do not have the right to add audioclips.' ), null, true);
	exit;
}

$f_publication_id = Input::Get('f_publication_id', 'int', 0, true);
$f_issue_number = Input::Get('f_issue_number', 'int', 0, true);
$f_section_number = Input::Get('f_section_number', 'int', 0, true);
$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_audioclip_gunid = Input::Get('f_audioclip_gunid', 'string', null, true);
$f_action = Input::Get('f_action', 'string', null, true);

$BackLink = Input::Get('BackLink', 'string', null, true);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), null, true);
	exit;
}

switch($f_action) {
    case 'add':
        if (empty($_FILES['f_media_file'])) {
            camp_html_display_error(getGS('Invalid file parameter'));
            exit;
        }
        $aClipObj =& new Audioclip();
        $audioFile = $aClipObj->onFileUpload($_FILES['f_media_file']);
        if (PEAR::isError($audioFile)) {
            camp_html_display_error(getGS('Audio file could not be stored locally'));
            exit;
        }
        $id3Data = Audioclip::AnalyzeFile($audioFile);
        $s = $id3Data['playtime_seconds'];
        $mData['title'] = $id3Data['filename'];
        $mData['creator'] = $id3Data['id3v1']['artist'];
        $mData['type'] = $id3Data['id3v1']['genre'];
        $mData['extent'] = date('H:i:s', floor($s)-date('Z')).substr(number_format($s, 6), strpos(number_format($s, 6), '.'));
        $mData['bitrate'] = $id3Data['audio']['bitrate'];
        $mData['sample_rate'] = $id3Data['audio']['sample_rate'];
        break;
    case 'edit':
        $audioClip =& new Audioclip($f_audioclip_gunid);
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
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">
    <?php include_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/javascript_common.php"); ?>
	<title><?php putGS("Edit Audioclip Metadata"); ?></title>
</head>
<body>
<?php camp_html_display_msgs(); ?>

<P>
<FORM NAME="audioclip_metadata" METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/articles/audioclips/do_<?php echo $f_action; ?>.php" ENCTYPE="multipart/form-data">
<TABLE style="margin-top: 10px; margin-left: 5px;" cellpadding="0" cellspacing="0">
<TR>
	<?php if ($g_user->hasPermission('AddAudioclip')) { ?>
	<TD id="link1" class="tab_current"><a href="#" onclick="javascript:selectTab('1', '3');"><img src="<?php p($Campsite['ADMIN_IMAGE_BASE_URL']); ?>/add.png" border="0"><b><?php putGS("Main"); ?></b></a></td>
	<?php } ?>

	<TD id="link2" class="tab_normal"><a href="javascript:selectTab('2', '3');"><img src="<?php p($Campsite['ADMIN_IMAGE_BASE_URL']); ?>/add.png" border="0"><b><?php putGS("Music"); ?></b></a></td>

    <TD id="link3" class="tab_normal" style="border-right: 1px solid #8baed1;"><a href="#" onclick="javascript:selectTab('3', '3');"><img src="<?php p($Campsite['ADMIN_IMAGE_BASE_URL']); ?>/add.png" border="0"><b><?php putGS("Voice"); ?></b></a></td>
</TR>
<TR>
    <TD colspan="3" style="background-color: #EEE; padding-top: 5px; border-bottom: 1px solid #8baed1; border-right: 1px solid #8baed1; border-left: 1px solid #8baed1;">
        <BR />
        <TABLE cellpadding="5" cellspacing="0" class="table_input">
        <TR id="tab1">
            <TD>
                <?php include('main_metadata.php'); ?>
            </TD>
        </TR>
        <TR id="tab2" style="display: none;">
            <TD>
                <?php include('music_metadata.php'); ?>
            </TD>
        </TR>
        <TR id="tab3" style="display: none;">
            <TD>
                <?php include('voice_metadata.php'); ?>
            </TD>
        </TR>
        <TR>
            <TD>
                <DIV align="center">
                    <INPUT type="hidden" name="f_publication_id" value="<?php  p($f_publication_id); ?>">
                    <INPUT type="hidden" name="f_issue_number" value="<?php  p($f_issue_number); ?>">
                    <INPUT type="hidden" name="f_section_number" value="<?php  p($f_section_number); ?>">
                    <INPUT type="hidden" name="f_article_number" value="<?php  p($f_article_number); ?>">
                    <INPUT type="hidden" name="f_language_id" value="<?php  p($f_language_id); ?>">
                    <INPUT type="hidden" name="f_language_selected" value="<?php  p($f_language_selected); ?>">
                    <INPUT type="hidden" name="BackLink" value="<?php  p($_SERVER['REQUEST_URI']); ?>">
                <?php if ($f_action == 'add') { ?>
                    <INPUT type="hidden" name="f_audiofile" value="<?php p($audioFile); ?>" />
                <?php } elseif ($f_action == 'edit') { ?>
                    <INPUT type="hidden" name="f_audioclip_gunid" value="<?php p($f_audioclip_gunid); ?>" />
                <?php } ?>
                    <INPUT type="submit" name="Save" value="<?php putGS('Save'); ?>" class="button" />
                <?php if ($f_action == 'edit') { ?>
                    <INPUT type="button" name="Cancel" value="<?php putGS('Cancel'); ?>" class="button" onclick="window.close();">
                <?php } ?>
                </DIV>
            </TD>
        </TR>
        </TABLE>
        <BR />
    </TD>
</TR>
</TABLE>
</FORM>

</P>

</body>
</html>