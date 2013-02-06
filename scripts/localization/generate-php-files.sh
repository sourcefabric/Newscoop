#!/bin/bash

# Script to generate Newscoop translations from .po files.

# Set the temporary directory to use for converting the files
BUILDPATH=/tmp

# Set the locales you want to convert 
LOCALES="ar at be bn cs de el es fr he hr it ka ko ku nl pl pt pt_BR ro ru sh sq sr sv uk zh zh_TW"

# Begin function that converts the files

function convert2php {
  localization=$1

cd ${BUILDPATH}/po/

echo "Changing the ${localization} files to Newscoop style..."

sed -i 's/# Newscoop translation file/<?php /g' ${localization}/*.po
sed -i ':q;N;s/\n/ /g;t q' ${localization}/*.po
sed -i 's/ msgid "/\nregGS("/g' ${localization}/*.po
sed -i 's/ msgstr "/, "/g' ${localization}/*.po
sed -i '/<?php/!s/[ ]*$/);/g' ${localization}/*.po
sed -i '$ a\?>' ${localization}/*.po

# Strip the final newline from the file 

for i in ${localization}/*.po; do
 awk '{q=p;p=$0}NR>1{print q}END{ORS = ""; print p}' $i > $i.tmp
 rm $i
done

# Give the changed files a .php extension

for i in ${localization}/*.po.tmp; do
 mv "$i" "${i/.po.tmp}".php
done

}

# Run the function for the specified locales

for localization in ${LOCALES}; do
 convert2php ${localization}
done

echo "Size of the output files is:"

du -h ${BUILDPATH}/po/*
