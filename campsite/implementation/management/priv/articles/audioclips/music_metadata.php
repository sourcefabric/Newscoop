<!-- START MUSIC DATA //-->
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Edit Audioclip Metadata"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
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
