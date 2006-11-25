<!-- START GENERAL DATA  //-->
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
        <INPUT TYPE="TEXT" NAME="f_Main_dc_title" VALUE="<?php p($mData['title']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
	</TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS("Creator"); ?>:</TD>
    <TD>
        <INPUT TYPE="TEXT" NAME="f_Main_dc_creator" VALUE="<?php p($mData['creator']); ?>" SIZE="50" MAXLENGTH="255" class="input_text" />
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
