#!/bin/sh

echo "Expires: now"
echo "Location: X_ROOT/pub/issues/sections/articles/images/do_add.php?Id=$UNIQUE_ID"
echo 

cat - > X_TMP/img-"$UNIQUE_ID"
