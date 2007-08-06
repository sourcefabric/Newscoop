<!-- START MUSIC DATA //-->
<TABLE border="0" cellspacing="0" cellpadding="6">
<TR>
    <TD colspan="2">
        <B><?php  putGS("Edit Audioclip Metadata"); ?></B>
        <HR noshade size="1" color="black">
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Title"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Music_dc_title" value="<?php p($mData['title']); ?>" onchange="spread(this, 'dc_title')" size="50" maxlength="255" class="input_text" alt="blank" emsg="<?php putGS("Please enter a title for the audioclip."); ?>" />
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Creator"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Music_dc_creator" value="<?php p($mData['creator']); ?>" onchange="spread(this, 'dc_creator')" size="50" maxlength="255" class="input_text" alt="blank" emsg="<?php putGS("Please enter a creator for the audioclip."); ?>" />
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Album"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Music_dc_source" value="<?php p($mData['source']); ?>" onchange="spread(this, 'dc_source')" size="50" maxlength="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Year"); ?>:</TD>
    <TD>
        <SELECT name="f_Music_ls_year" class="input_select">
        <?php
        for ($year = 1900; $year <= 2011; $year++) {
            camp_html_select_option($year, $mData['year'], $year);
        }
        ?>
        </SELECT>
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Genre"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Music_dc_type" value="<?php p($mData['type']); ?>" onchange="spread(this, 'dc_type')" size="50" maxlength="255" class="input_text" alt="blank" emsg="<?php putGS("Please enter a genre for the audioclip."); ?>" />
    </TD>
</TR>
<TR>
    <TD align="right" valign="top"><?php putGS("Description"); ?>:</TD>
    <TD>
        <TEXTAREA name="f_Music_dc_description" rows="5" cols="40" class="input_text" onchange="spread(this, 'dc_description')"><?php p($mData['description']); ?></TEXTAREA>
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Format"); ?>:</TD>
    <TD>
        <SELECT name="f_Music_dc_format" class="input_select" onchange="spread(this, 'dc_format')" disabled="on">
            <OPTION value="File">Audioclip</OPTION>
        </SELECT>
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("BPM"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Music_ls_bpm" value="<?php p($mData['bpm']); ?>" size="50" maxlength="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Rating"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Music_ls_rating" value="<?php p($mData['rating']); ?>" size="50" maxlength="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Length"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Music_dcterms_extent" value="<?php p($mData['extent']); ?>" onchange="spread(this, 'dcterms_extent')" size="50" maxlength="255" class="input_text" alt="blank" emsg="<?php putGS("Please enter a length value for the audioclip."); ?>" />
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Encoded by"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Music_ls_encoded_by" value="<?php p($mData['encoded_by']); ?>" size="50" maxlength="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Track number"); ?>:</TD>
    <TD>
        <SELECT name="f_Music_ls_track_num" class="input_select">
        <?php
        for ($track = 0; $track <= 99; $track++) {
            camp_html_select_option($track, $mData['track_num'], $track);
        }
        ?>
        </SELECT>
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Disc number"); ?>:</TD>
    <TD>
        <SELECT name="f_Music_ls_disc_num" class="input_select">
        <?php
        for ($dnum = 0; $dnum <= 20; $dnum++) {
            camp_html_select_option($dnum, $mData['disc_num'], $dnum);
        }
        ?>
        </SELECT>
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Mood"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Music_ls_mood" value="<?php p($mData['mood']); ?>" size="50" maxlength="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Label"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Music_dc_publisher" value="<?php p($mData['publisher']); ?>" size="50" maxlength="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Composer"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Music_ls_composer" value="<?php p($mData['composer']); ?>" size="50" maxlength="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Bitrate"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Music_ls_bitrate" value="<?php p($mData['bitrate']); ?>" size="50" maxlength="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Channels"); ?>:</TD>
    <TD>
        <SELECT name="f_Music_ls_channels" class="input_select">
        <?php
        camp_html_select_option("", $mData['channels'], "");
        camp_html_select_option("1", $mData['channels'], getGS("Mono"));
        camp_html_select_option("2", $mData['channels'], getGS("Stereo"));
        camp_html_select_option("6", $mData['channels'], getGS("5.1"));
        ?>
        </SELECT>
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Sample rate"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Music_ls_samplerate" value="<?php p($mData['sample_rate']); ?>" size="50" maxlength="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Encoder software used"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Music_ls_encoder" value="<?php p($mData['encoder']); ?>" size="50" maxlength="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Checksum"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Music_ls_crc" value="<?php p($mData['crc']); ?>" size="50" maxlength="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD align="right" valign="top"><?php putGS("Lyrics"); ?>:</TD>
    <TD>
        <TEXTAREA name="f_Music_ls_lyrics" rows="5" cols="40" class="input_text" onchange="spread(this, 'ls_lyrics')"><?php p($mData['lyrics']); ?></TEXTAREA>
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Orchestra or band"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Music_ls_orchestra" value="<?php p($mData['orchestra']); ?>" size="50" maxlength="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Conductor"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Music_ls_conductor" value="<?php p($mData['conductor']); ?>" size="50" maxlength="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Lyricist"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Music_ls_lyricist" value="<?php p($mData['lyricist']); ?>" size="50" maxlength="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Original lyricist"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Music_ls_originallyricist" value="<?php p($mData['originallyricist']); ?>" size="50" maxlength="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Radio station name"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Music_ls_radiostationname" value="<?php p($mData['radiostationname']); ?>" size="50" maxlength="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Audio file information web page"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Music_ls_audiofileinfourl" value="<?php p($mData['audiofileinfourl']); ?>" size="50" maxlength="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Artist web page"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Music_ls_artisturl" value="<?php p($mData['artisturl']); ?>" size="50" maxlength="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Audio source web page"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Music_ls_audiosourceurl" value="<?php p($mData['audiosourceurl']); ?>" size="50" maxlength="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Radio station web page"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Music_ls_radiostationurl" value="<?php p($mData['radiostationurl']); ?>" size="50" maxlength="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Buy CD web page"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Music_ls_buycdurl" value="<?php p($mData['buycdurl']); ?>" size="50" maxlength="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("ISRC number"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Music_dc_isrcnumber" value="<?php p($mData['isrcnumber']); ?>" size="50" maxlength="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Catalog number"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Music_ls_catalognumber" value="<?php p($mData['catalognumber']); ?>" size="50" maxlength="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Original artist"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Music_ls_originalartist" value="<?php p($mData['originalartist']); ?>" size="50" maxlength="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Copyright"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Music_dc_rights" value="<?php p($mData['rights']); ?>" onchange="spread(this, 'dc_rights')" size="50" maxlength="255" class="input_text" />
    </TD>
</TR>
</TABLE>
<P>
<!-- END MUSIC DATA //-->
