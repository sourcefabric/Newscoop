#!/bin/sh

if [ "$3" = "0" ] ;
then
	msg_ok="The folder has been deleted."
	msg_fail="<BR>The folder could not be deleted."
else
	msg_ok="The template has been deleted."
	msg_fail="<BR>The template could not be deleted."
fi

/bin/rm -r "$DOCUMENT_ROOT$1$2"
if [ "$?" = "0" ]
then
	echo "$msg_ok"
else
	echo "$msg_fail"
fi
