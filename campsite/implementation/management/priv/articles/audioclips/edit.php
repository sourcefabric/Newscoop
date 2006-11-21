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

$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);
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
     }
     $aclipObj =& new Audioclip();
     $audioFile = $aclipObj->OnFileUpload($_FILES['f_media_file']);
     if (PEAR::isError($audioFile)) {
         camp_html_display_error(getGS('Audio file could not be stored locally'));
     }
     $id3Data = $aclipObj->analyzeFile($audioFile);
     $s = $id3Data['playtime_seconds'];
     $mData['title'] = $id3Data['filename'];
     $mData['extent'] = date('H:i:s', floor($s)-date('Z')).substr(number_format($s, 6), strpos(number_format($s, 6), '.'));
     $mData['bitrate'] = $id3Data['audio']['bitrate'];
     $mData['sample_rate'] = $id3Data['audio']['sample_rate'];
     break;
 case 'edit':
     // We retrieve all metadata by using xr_getAudioClip() to edit
     break;
}

include_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/javascript_common.php");

camp_html_display_msgs();

?>

<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/campsite.js"></script>

<P>
<FORM NAME="audioclip_metadata" METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/articles/audioclips/do_<?php echo $f_action; ?>.php" ENCTYPE="multipart/form-data">
<!-- START GENERAL DATA  //-->
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" class="table_input">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Add Audioclip Metadata"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT"><?php putGS("Title"); ?>:</TD>
	<TD>
        <INPUT TYPE="TEXT" NAME="f_Main_dc_title" VALUE="<?php p($mData['title']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
	</TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Creator"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Main_dc_creator" VALUE="<?php p($mData['creator']; ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Genre"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Main_dc_type" VALUE="<?php p($mData['type']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("File format"); ?>:</TD>
    <TD>
        <SELECT NAME="f_Main_dc_format" DISABLED="ON">
        <OPTION VALUE="File" selected>Audioclip</OPTION>
        </SELECT>
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Length"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Main_dcterms_extent" VALUE="<?php p($mData['extent']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
    </TD>
</TR>
</TABLE>
<P>
<!-- END GENERAL DATA //-->
<!-- START MUSIC DATA //-->
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" class="table_input">
<TR>
	<TD ALIGN="RIGHT"><?php putGS("Title"); ?>:</TD>
	<TD>
        <INPUT TYPE="TEXT" NAME="f_Music_dc_title" VALUE="<?php p($mData['title']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
	</TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Creator"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Music_dc_creator" VALUE="<?php p($mData['creator']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
    </TD>
</TR>
<TR>
	<TD ALIGN="RIGHT"><?php putGS("Album"); ?>:</TD>
	<TD>
        <INPUT TYPE="TEXT" NAME="f_Music_dc_source" VALUE="<?php p($mData['source']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
	</TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Year"); ?>:</TD>
    <TD>
        <SELECT name="f_Music_ls_year"></SELECT>
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Genre"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Music_dc_type" VALUE="<?php p($mData['type']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Description"); ?>:</TD>
    <TD>
        <TEXTAREA name="f_Music_dc_description"><?php p($mData['description']); ?></TEXTAREA>
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Format"); ?>:</TD>
    <TD>
        <SELECT name="f_Music_dc_format"></SELECT>
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("BPM"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Music_ls_bpm" VALUE="<?php p($mData['bpm']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Rating"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Music_ls_rating" VALUE="<?php p($mData['rating']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Length"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Music_dcterms_extent" VALUE="<?php p($mData['extent']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Encoded by"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Music_ls_encoded_by" VALUE="<?php p($mData['encoded_by']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Track number"); ?>:</TD>
    <TD>
        <SELECT name="f_Music_ls_track_num"></SELECT>
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Disc number"); ?>:</TD>
    <TD>
        <SELECT name="f_Music_ls_disc_num"></SELECT>
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Mood"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Music_ls_mood" VALUE="<?php p($mData['mood']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Label"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Music_dc_publisher" VALUE="<?php p($mData['publisher']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Composer"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Music_ls_composer" VALUE="<?php p($mData['composer']); ?>" SIZE="50" MAXLENGHT="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Bitrate"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Music_ls_bitrate" VALUE="<?php p($mData['bitrate']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Channels"); ?>:</TD>
    <TD>
        <SELECT NAME="f_Music_ls_channels"></SELECT>
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Sample rate"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Music_ls_samplerate" VALUE="<?php p($mData['sample_rate']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Encoder software used"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Music_ls_encoder" VALUE="<?php p($mData['encoder']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Checksum"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Music_ls_crc" VALUE="<?php p($mData['crc']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Lyrics"); ?>:</TD>
    <TD>
        <SELECT NAME="f_Music_ls_lyrics"></SELECT>
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Orchestra or band"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Music_ls_orchestra" VALUE="<?php p($mData['orchestra']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Conductor"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Music_ls_conductor" VALUE="<?php p($mData['conductor']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Lyricist"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Music_ls_lyricist" VALUE="<?php p($mData['lyricist']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Original lyricist"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Music_ls_originallyricist" VALUE="<?php p($mData['originallyricist']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Radio station name"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Music_ls_radiostationname" VALUE="<?php p($mData['radiostationname']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Audio file information web page"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Music_ls_audiofileinfourl" VALUE="<?php p($mData['audiofileinfourl']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Artist web page"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Music_ls_artisturl" VALUE="<?php p($mData['artisturl']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Audio source web page"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Music_ls_audiosourceurl" VALUE="<?php p($mData['audiosourceurl']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Radio station web page"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Music_ls_radiostationurl" VALUE="<?php p($mData['radiostationurl']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Buy CD web page"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Music_ls_buycdurl" VALUE="<?php p($mData['buycdurl']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("ISRC number"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Music_dc_isrcnumber" VALUE="<?php p($mData['isrcnumber']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Catalog number"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Music_ls_catalognumber" VALUE="<?php p($mData['catalognumber']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Original artist"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Music_ls_originalartist" VALUE="<?php p($mData['originalartist']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Copyright"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Music_dc_rights" VALUE="<?php p($mData['rights']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
    </TD>
</TR>
</TABLE>
<P>
<!-- END MUSIC DATA //-->
<!-- START VOICE DATA //-->
<!-- START MUSIC DATA //-->
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" class="table_input">
<TR>
	<TD ALIGN="RIGHT"><?php putGS("Title"); ?>:</TD>
	<TD>
        <INPUT TYPE="TEXT" NAME="f_Voice_dc_title" VALUE="<?php p($mData['title']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT"><?php putGS("Report date/time"); ?>:</TD>
	<TD>
        <INPUT TYPE="TEXT" NAME="f_Voice_dcterms_temporal" VALUE="<?php p($mData['temporal']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
	</TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Report location"); ?>:</TD>
    <TD>
        <TEXTAREA NAME="f_Voice_dcterms_spatial"><?php p($mData['spatial']); ?></TEXTAREA>
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Report organizations"); ?>:</TD>
    <TD>
        <TEXTAREA NAME="f_Voice_dcterms_entity"><?php p($mData['entity']); ?></TEXTAREA>
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Description"); ?>:</TD>
    <TD>
        <TEXTAREA NAME="f_Voice_dc_description"><?php p($mData['description']); ?></TEXTAREA>
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Creator"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Voice_dc_creator" VALUE="<?php p($mData['creator']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Subject"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Voice_dc_subject" VALUE="<?php p($mData['subject']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Genre"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Voice_dc_type" VALUE="<?php p($mData['type']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Format"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Voice_dc_format" VALUE="<?php p($mData['format']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Contributor"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Voice_dc_contributor" VALUE="<?php p($mData['contributor']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Language"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Voice_dc_language" VALUE="<?php p($mData['language']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Copyright"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Voice_dc_rights" VALUE="<?php p($mData['rights']) ; ?>" SIZE="50" MAXLENGTH="255" />
    </TD>
</TR>
</TABLE>
<P>
<!-- END VOICE DATA //-->
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" class="table_input">
<TR>
    <TD COLSPAN="2">
        <DIV ALIGN="CENTER">
        <INPUT TYPE="HIDDEN" NAME="f_publication_id" VALUE="<?php  p($f_publication_id); ?>">
        <INPUT TYPE="HIDDEN" NAME="f_issue_number" VALUE="<?php  p($f_issue_number); ?>">
        <INPUT TYPE="HIDDEN" NAME="f_section_number" VALUE="<?php  p($f_section_number); ?>">
        <INPUT TYPE="HIDDEN" NAME="f_article_number" VALUE="<?php  p($f_article_number); ?>">
        <INPUT TYPE="HIDDEN" NAME="f_language_id" VALUE="<?php  p($f_language_id); ?>">
        <INPUT TYPE="HIDDEN" NAME="f_language_selected" VALUE="<?php  p($f_language_selected); ?>">
        <INPUT TYPE="HIDDEN" NAME="BackLink" VALUE="<?php  p($_SERVER['REQUEST_URI']); ?>">
        <INPUT TYPE="HIDDEN" NAME="f_audiofile" VALUE="<?php p($audioFile); ?>" />
        <INPUT TYPE="submit" NAME="Save" VALUE="<?php putGS('Save'); ?>" class="button" />
        </DIV>
    </TD>
</TR>
</TABLE>
</FORM>

</P>
