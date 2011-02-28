#!/bin/bash
# usage from git-repo: 
#  ./gen-debian-package.sh -rfakeroot -sa

#
# Note to self:
# git archive --format tar release-3.5.0-GA newscoop/ | gzip -9 > /tmp/newscoop-3.5.0.tar.gz

DEBRELEASE=$(head -n1 debian/changelog | cut -d ' ' -f 2 | sed 's/[()]*//g')
DEBVERSION=$(echo $DEBRELEASE | sed 's/-.*$//g;s/~test[0-9]*//g')
UPSTREAMVERSION=$(echo $DEBVERSION | sed 's/~/-/g')
UPSTREAMDIST=$(echo $UPSTREAMVERSION | sed 's/^\([0-9]*\.[0-9]*\).*$/\1/')
DEBPATH=`pwd`/debian # TODO check dirname $0
MIRRORPATH=/tmp

if test "${UPSTREAMDIST}" == "3.5"; then
	CUSTOMURL=http://www.sourcefabric.org/attachment2/000000024.gz
fi

if test ! -d ${DEBPATH}; then
  echo "can not find debian/ folder. Please 'cd <newscoop-git>/packaging/'"
  exit;
fi

echo "Debian Release:   ${DEBRELEASE}"
echo "Upstream Version: ${UPSTREAMVERSION}"
echo "Major:            ${UPSTREAMDIST}"
echo "debuild opts:     $@"
echo "build folder:     /tmp/newscoop-$DEBVERSION"

echo -n "OK? [enter|CTRL-C]" ; read


rm -rf /tmp/newscoop-$DEBVERSION
mkdir  /tmp/newscoop-$DEBVERSION
cd     /tmp/newscoop-$DEBVERSION

echo -n " +++ building newscoop-${DEBVERSION}.deb in: "
pwd
echo " +++ downloading upstream release.."

if [ -f ${MIRRORPATH}/newscoop-$UPSTREAMVERSION.tar.gz ]; then
	echo "using local file ${MIRRORPATH}/newscoop-$UPSTREAMVERSION.tar.gz"
  tar xzf ${MIRRORPATH}/newscoop-$UPSTREAMVERSION.tar.gz
elif [ -f ${MIRRORPATH}/newscoop-$UPSTREAMDIST.tar.gz ]; then
	echo "using local file ${MIRRORPATH}/newscoop-$UPSTREAMDIST.tar.gz"
  tar xzf ${MIRRORPATH}/newscoop-$UPSTREAMDIST.tar.gz
elif [ -n "$CUSTOMURL" ]; then
	echo "download ${CUSTOMURL}"
	curl -L ${CUSTOMURL} \
		| tee ${MIRRORPATH}/newscoop-$UPSTREAMDIST.tar.gz \
		| tar xzf - || exit
else
	echo "download from sourceforge."
  curl -L http://downloads.sourceforge.net/project/newscoop/$UPSTREAMDIST/newscoop-$UPSTREAMVERSION.tar.gz | tar xzf -
fi

# done in README.debian
rm newscoop/INSTALL.txt
# documentation for /usr/share/doc/newscoop
for file in ChangeLog CREDITS COPYING README UPGRADE; do
  mv -vi newscoop/${file}.txt ./${file}
done
cp -vi newscoop/htaccess newscoop/.htaccess

### fixes for 3.5.0-rc2 ###
if test "${UPSTREAMVERSION}" == "3.5.0-rc2"; then
  echo " +++ applying fixes for 3.5.0-rc2"
  rm newscoop/admin-style/fg-menu/theme/.DS_Store
  chmod +x newscoop/include/pear/DB/tests/driver/run.cvs
  chmod +x newscoop/include/pear/DB/tests/run.cvs
  chmod -x newscoop/admin-style/ColVis.css
  chmod -x newscoop/include/pear/Event/Notification.php
  chmod -x newscoop/javascript/syntaxhighlighter/styles/shThemeMDUltra.css
  chmod -x newscoop/templates/system_templates/img/newscoop_logo_big.png
  chmod -x newscoop/admin-style/images/newscoop_logo_big.png
  chmod -x newscoop/include/pear/Event/Dispatcher.php
  chmod -x newscoop/javascript/geocoding/openlayers/img/cloud-popup-relative.png
  rm "newscoop/install/sample_templates/classic/templates/classic/tpl/banner/bannerleftcol.tpl .tpl"
fi


############################

cp -avi $DEBPATH ./ || exit
debuild $@ || exit

ls -l /tmp/newscoop*deb
ls -l /tmp/newscoop*changes

lintian -i --pedantic /tmp/newscoop_${DEBRELEASE}_*.changes | tee /tmp/newscoop-${DEBRELEASE}.issues

echo -n "UPLOAD? [enter|CTRL-C]" ; read

dput rg42 /tmp/newscoop_${DEBRELEASE}_*.changes 
