#!/bin/sh

rm /tmp/blob?NrArticle?Number

echo "Expires: now"
echo "Location: X_ROOT/pub/issues/sections/articles/images/?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Article=<!sql print #Article>&Language=<!sql print #Language>&sLanguage=<!sql print #sLanguage>&Section=<!sql print #Section>"
echo 
