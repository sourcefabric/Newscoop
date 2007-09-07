<!-- START VOICE DATA //-->
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
        <INPUT type="text" name="f_Voice_dc_title" value="<?php p($mData['title']); ?>" onchange="spread(this, 'dc_title')" size="50" maxlength="255" class="input_text" alt="blank" emsg="<?php putGS("Please enter a title for the audioclip."); ?>" />
	</TD>
</TR>
<TR>
	<TD align="right"><?php putGS("Report date/time"); ?>:</TD>
	<TD>
        <INPUT type="text" name="f_Voice_dcterms_temporal" value="<?php p($mData['temporal']); ?>" size="50" maxlength="255" class="input_text" />
	</TD>
</TR>
<TR>
    <TD align="right" valign="top"><?php putGS("Report location"); ?>:</TD>
    <TD>
        <TEXTAREA name="f_Voice_dcterms_spatial" rows="5" cols="40" class="input_text"><?php p($mData['spatial']); ?></TEXTAREA>
    </TD>
</TR>
<TR>
    <TD align="right" valign="top"><?php putGS("Report organizations"); ?>:</TD>
    <TD>
        <TEXTAREA name="f_Voice_dcterms_entity" rows="5" cols="40" class="input_text"><?php p($mData['entity']); ?></TEXTAREA>
    </TD>
</TR>
<TR>
    <TD align="right" valign="top"><?php putGS("Description"); ?>:</TD>
    <TD>
        <TEXTAREA name="f_Voice_dc_description" rows="5" cols="40" class="input_text" onchange="spread(this, 'dc_description')"><?php p($mData['description']); ?></TEXTAREA>
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Creator"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Voice_dc_creator" value="<?php p($mData['creator']); ?>" onchange="spread(this, 'dc_creator')" size="50" maxlength="255" class="input_text" alt="blank" emsg="<?php putGS("Please enter a creator for the audioclip."); ?>" />
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Subject"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Voice_dc_subject" value="<?php p($mData['subject']); ?>" onchange="spread(this, 'dc_subject')" size="50" maxlength="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Genre"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Voice_dc_type" value="<?php p($mData['type']); ?>" onchange="spread(this, 'dc_type')" size="50" maxlength="255" class="input_text" alt="blank" emsg="<?php putGS("Please enter a genre for the audioclip."); ?>" />
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
    <TD align="right"><?php putGS("Contributor"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Voice_dc_contributor" value="<?php p($mData['contributor']); ?>" size="50" maxlength="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Language"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Voice_dc_language" value="<?php p($mData['language']); ?>" size="50" maxlength="255" class="input_text" />
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Copyright"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Voice_dc_rights" value="<?php p($mData['rights']) ; ?>" onchange="spread(this, 'dc_rights')" size="50" maxlength="255" class="input_text" />
    </TD>
</TR>
</TABLE>
<P>
<!-- END VOICE DATA //-->
