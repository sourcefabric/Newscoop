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
SFOCUSTOM=""
DEBPATH=`pwd`/debian # TODO check dirname $0
MIRRORPATH=/tmp
BUILDDEST=/tmp/newscoop-${DEBVERSION}/

#if test "${UPSTREAMDIST}" == "3.5"; then
#	CUSTOMURL=http://www.sourcefabric.org/attachment2/000000024.gz
#fi

if test ! -d ${DEBPATH}; then
  echo "can not find debian/ folder. Please 'cd <newscoop-git>/packaging/'"
  exit;
fi

echo "Debian Release:   ${DEBRELEASE}"
echo "Upstream Version: ${UPSTREAMVERSION}${SFOCUSTOM}"
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
elif [ -f ${MIRRORPATH}/newscoop-$UPSTREAMDIST$SFOCUSTOM.tar.gz ]; then
	echo "using local file ${MIRRORPATH}/newscoop-$UPSTREAMDIST$SFOCUSTOM.tar.gz"
  tar xzf ${MIRRORPATH}/newscoop-$UPSTREAMDIST$SFOCUSTOM.tar.gz
elif [ -n "$CUSTOMURL" ]; then
	echo "download ${CUSTOMURL}"
	curl -L ${CUSTOMURL} \
		| tee ${MIRRORPATH}/newscoop-$UPSTREAMDIST.tar.gz \
		| tar xzf - || exit
else
	echo "download from sourceforge."
  #curl -L http://downloads.sourceforge.net/project/newscoop/$UPSTREAMDIST/newscoop-$UPSTREAMVERSION.tar.gz | tar xzf -
  curl -L http://downloads.sourceforge.net/project/newscoop/$UPSTREAMVERSION/newscoop-$UPSTREAMVERSION.tar.gz \
		| tee ${MIRRORPATH}/newscoop-$UPSTREAMDIST$SFOCUSTOM.tar.gz \
		| tar xzf - || exit
fi

mv ${MIRRORPATH}/newscoop-$UPSTREAMVERSION/newscoop-$UPSTREAMVERSION/ ${MIRRORPATH}/newscoop-$UPSTREAMVERSION/newscoop/

# done in README.Debian
rm newscoop/INSTALL.txt

# Sourcefabric licenses covered by debian/copyright
rm newscoop/COPYING.txt
rm newscoop/LICENSE_3RD_PARTY.txt

# third party licences covered by debian/copyright
rm newscoop/include/html2pdf/_tcpdf_5.0.002/LICENSE.TXT
rm newscoop/js/domTT/LICENSE
rm newscoop/js/flowplayer/LICENSE.txt
rm newscoop/js/geocoding/openlayers/license.txt
rm newscoop/js/plupload/license.txt
rm newscoop/js/tinymce/license.txt
rm newscoop/library/Nette/license.txt
rm newscoop/library/fabpot-dependency-injection-07ff9ba/LICENSE
rm newscoop/library/fabpot-event-dispatcher-782a5ef/LICENSE
rm newscoop/include/html2pdf/_tcpdf_5.0.002/fonts/dejavu-fonts-ttf-2.30/LICENSE
rm newscoop/include/html2pdf/_tcpdf_5.0.002/fonts/freefont-20090104/COPYING
rm newscoop/js/tapmodo-Jcrop-5e58bc9/MIT-LICENSE.txt
rm newscoop/js/tapmodo-Jcrop-5e58bc9/build/LICENSE

rm newscoop/vendor/doctrine/common/LICENSE
rm newscoop/vendor/doctrine/dbal/LICENSE
rm newscoop/vendor/doctrine/orm/LICENSE
rm newscoop/vendor/symfony/console/Symfony/Component/Console/LICENSE
rm newscoop/vendor/symfony/yaml/Symfony/Component/Yaml/LICENSE
rm newscoop/vendor/zendframework/zendframework1/LICENSE.txt

# remove documentation under Creative Commons licenses
rm -r newscoop/library/fabpot-dependency-injection-07ff9ba/doc/
rm -r newscoop/library/fabpot-event-dispatcher-782a5ef/doc/

# remove fonts installed as a package dependency
rm -r newscoop/include/captcha/fonts/

# fix the font path for captcha
sed -i "5s:('fonts/VeraBd.ttf', 'fonts/VeraIt.ttf', 'fonts/Vera.ttf'):('/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans-Bold.ttf', '/usr/share/fonts/truetype/ttf-dejavu/DejaVuSansMono.ttf', '/usr/share/fonts/truetype/ttf-dejavu/DejaVuSerif.ttf'):g" newscoop/include/captcha/image.php

# documentation for /usr/share/doc/newscoop
for file in ChangeLog CREDITS README UPGRADE; do
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

### fixes for 3.5.1 ###
if test "${UPSTREAMVERSION}" == "3.5.1"; then
	chmod -x newscoop/install/sample_templates/the_journal/templates/system_templates/img/newscoop_logo_big.png
	rm newscoop/javascript/editarea/edit_area/plugins/test/images/Thumbs.db
	rm newscoop/javascript/tinymce/plugins/codehighlighting/img/Thumbs.db
fi

### fixes for 4.0.4 ###
if test "${UPSTREAMVERSION}" == "4.0.4"; then

	chmod -x newscoop/install/sql/upgrade/4.0.x/2012-12-18/tables.sql
        chmod -x newscoop/application/modules/admin/controllers/CommentController.php
        chmod -x newscoop/library/Newscoop/Entity/Repository/CommentRepository.php
fi

############################

cd ../
tar czf newscoop_${UPSTREAMVERSION}.orig.tar.gz  newscoop-${DEBVERSION}/newscoop/
cd ${BUILDDEST} || exit

cp -avi $DEBPATH ./ || exit
debuild -k174C1854 $@ || exit

ls -l /tmp/newscoop*deb
ls -l /tmp/newscoop*changes

lintian -i --pedantic /tmp/newscoop_${DEBRELEASE}_*.changes | tee /tmp/newscoop-${DEBRELEASE}.issues

#echo -n "UPLOAD? [enter|CTRL-C]" ; read

#dput sfo /tmp/newscoop_${DEBRELEASE}_*.changes
