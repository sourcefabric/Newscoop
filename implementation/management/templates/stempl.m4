#!/bin/sh

PATH=/bin:/usr/bin

echo '<TABLE BORDER="0" CELLSPACING="2" CELLPADDING="0" WIDTH="100%">'
echo '<TR BGCOLOR="WHITE"><TD WIDTH="30%" VALIGN="TOP">'
echo 'B_LIST'
echo 'B_LIST_HEADER'
echo 'X_LIST_TH({Folders})'
echo 'E_LIST_HEADER'
c=""
x="0"
v=0
tmpfile="/tmp/ls_url-$$"
X_SCRIPT_BIN/ls_url d "$DOCUMENT_ROOT" "$1" > $tmpfile
echo "EOF" >> $tmpfile
i=""
while [ "$i" != "EOF" ]; do
	read i
	if [ "$x" = "0" ]; then
		j=$i
		x=1
	else
		if [ "$c" = "#D0D0D0" ]; then
			c="#D0D0B0"
		else
			c="#D0D0D0"
		fi
		echo '<TR BGCOLOR="'$c'"><TD><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><TD><IMG SRC="X_ROOT/img/icon/dir.gif" BORDER="0"></TD><TD><A HREF="'$j'/?'$2'">'$j'</A></TD></TR></TABLE></TD>'
		x=0
	fi
	v=1
done < $tmpfile
rm -f $tmpfile
if [ "$v" = "0" ] ; then echo '<TR><TD>No folders.</TD></TR>' ; fi
echo 'E_LIST'
echo '</TD><TD WIDTH="60%" VALIGN="TOP">'
echo 'B_LIST'
echo 'B_LIST_HEADER'
echo 'X_LIST_TH({Files})'
echo 'X_LIST_TH({Select}, {1%})'
echo 'E_LIST_HEADER'
c=""
x="0"
v=0
X_SCRIPT_BIN/ls_url f "$DOCUMENT_ROOT" "$1" > $tmpfile
echo "EOF" >> $tmpfile
i=""
while [ "$i" != "EOF" ]; do
	read i
	if [ "$x" = "0" ]; then
		j=$i
		x=1
	else
		if [ "$c" = "#D0D0D0" ]; then
			c="#D0D0B0"
		else
			c="#D0D0D0"
		fi
		echo '<TR BGCOLOR="'$c'"><TD><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><TD><IMG SRC="X_ROOT/img/icon/generic.gif" BORDER="0"></TD><TD>'$j'</TD></TR></TABLE></TD>'
		echo '<TD ALIGN="CENTER">X_BUTTON({Set template}, {icon/image.gif}, {pub/issues/set.xql?'$2'&Path='$1$i'})</TD></TR>'
		x=0
	fi
	v=1
done < $tmpfile
rm -f $tmpfile
if [ "$v" = "0" ] ; then echo '<TR><TD COLSPAN="2">No templates.</TD></TR>' ; fi
echo 'E_LIST'
echo '</TD></TR>'
echo '</TABLE>'
