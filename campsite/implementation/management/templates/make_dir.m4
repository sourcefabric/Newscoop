#!/bin/sh

dir=$(echo "$DOCUMENT_ROOT"/"$1"/"$2" | tr " ?~#%\*\&\|\"" "_________")

if [ ! -d "$dir" ] && mkdir -p "$dir" ; then
	echo "<LI>The folder <B>$2</B> has been created.</LI>"
else
	echo "<LI>The folder <B>$2</B> could not be created.</LI>"
fi
