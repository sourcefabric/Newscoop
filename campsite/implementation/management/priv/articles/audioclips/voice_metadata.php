<!-- START VOICE DATA //-->
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
        <INPUT TYPE="TEXT" NAME="f_Voice_dc_rights" VALUE="<?php p($mData['rights']) ; ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
    </TD>
</TR>
</TABLE>
<P>
<!-- END VOICE DATA //-->
