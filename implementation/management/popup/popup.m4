define(<*B_PBODY1*>, <*<BODY BGCOLOR="#D0D0A0" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE">*>)dnl
define(<*B_PBODY2*>, <*<BODY BGCOLOR="#D0D0D0" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE">*>)dnl
define(<*E_PBODY*>, <*</BODY>*>)dnl
define(<*B_PBAR*>, <*<TABLE BORDER="0" CELLPADDING="0" CELLSPACING="0" WIDTH="100%">
<TR>
	<TD>
		<TABLE BORDER="0" CELLPADDING="2" CELLSPACING="0">
		<TR>*>)dnl
define(<*X_PBUTTON*>, <*			<TD NOWRAP><A HREF="$1" TARGET="fmain"><IMG SRC="X_ROOT/img/tol.gif" BORDER="0"></A></TD>
			<TD NOWRAP><A HREF="$1" TARGET="fmain"><B><? putGS("$2"); ?></B></A></TD>*>)dnl
define(<*X_PSEP*>, <*	</TR>
		</TABLE>
	</TD>
	<TD ALIGN="RIGHT">
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="2">
		<TR>
*>)dnl
define(<*X_PSEP2*>, <*	</TR>
		</TABLE>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
*>)dnl
define(<*X_PLABEL1*>, <*			<TD BGCOLOR="#D0D0D0" NOWRAP>$1</TD>*>)dnl
define(<*X_PLABEL2*>, <*			<TD BGCOLOR="#D0D0A0" NOWRAP>$1</TD>*>)dnl
define(<*X_ABUTTON1*>, <*			<TD BGCOLOR="#D0D0D0" NOWRAP><A HREF="$1" TARGET="fmain"><IMG SRC="X_ROOT/img/tol.gif" BORDER="0"></A></TD>
				<TD BGCOLOR="#D0D0D0" NOWRAP><A HREF="$1" TARGET="fmain"><B>$2</B></A></TD>*>)dnl
define(<*X_ABUTTON2*>, <*			<TD BGCOLOR="#D0D0A0" NOWRAP><A HREF="$1" ifelse(<*$3*>, <**>, <**>, <*ONCLICK="$3"*>) TARGET="fmain"><IMG SRC="X_ROOT/img/tol.gif" BORDER="0"></A></TD>
				<TD BGCOLOR="#D0D0A0" NOWRAP><A HREF="$1" ifelse(<*$3*>, <**>, <**>, <*ONCLICK="$3"*>) TARGET="fmain"><B><? putGS("$2"); ?></B></A></TD>*>)dnl
define(<*E_PBAR*>, <*	</TD>
</TR>
</TABLE>*>)dnl
