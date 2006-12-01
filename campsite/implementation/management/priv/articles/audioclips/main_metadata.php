<!-- START GENERAL DATA  //-->
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
        <INPUT type="text" name="f_Main_dc_title" value="<?php p($mData['title']); ?>" onchange="spread(this, 'dc_title')" size="50" maxlength="255" class="input_text" alt="blank" emsg="<?php putGS("Please enter a title for the audioclip."); ?>" />
	</TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Creator"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Main_dc_creator" value="<?php p($mData['creator']); ?>" onchange="spread(this, 'dc_creator')" size="50" maxlength="255" class="input_text" alt="blank" emsg="<?php putGS("Please enter a creator for the audioclip."); ?>" />
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Genre"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Main_dc_type" value="<?php p($mData['type']); ?>" onchange="spread(this, 'dc_type')" size="50" maxlength="255" class="input_text" alt="blank" emsg="<?php putGS("Please enter a genre for the audioclip."); ?>" />
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("File format"); ?>:</TD>
    <TD>
        <SELECT name="f_Main_dc_format" onchange="spread(this, 'dc_format')" disabled="on">
            <OPTION value="File" selected>Audioclip</OPTION>
        </SELECT>
    </TD>
</TR>
<TR>
    <TD align="right"><?php putGS("Length"); ?>:</TD>
    <TD>
        <INPUT type="text" name="f_Main_dcterms_extent" value="<?php p($mData['extent']); ?>" onchange="spread(this, 'dcterms_extent')" size="50" maxlength="255" class="input_text" alt="blank" emsg="<?php putGS("Please enter a length value for the audioclip."); ?>" />
    </TD>
</TR>
</TABLE>
<P>
<!-- END GENERAL DATA //-->
