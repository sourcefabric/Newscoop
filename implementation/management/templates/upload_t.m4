#!/bin/sh

echo "Content-type: text/html"
echo "Location: X_ROOT/templates/process_t.xql?Id=$UNIQUE_ID"
echo 

cat - >/tmp/tpl-"$UNIQUE_ID"
