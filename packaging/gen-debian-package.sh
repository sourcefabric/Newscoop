#!/bin/sh

DEBVERSION="3.5.0~rc2"
UPSTREAMDIST="3.5"
UPSTREAMVERSION="3.5.0-rc2"

DEBPATH=`pwd`/debian

rm -rf /tmp/newscoop-$DEBVERSION
mkdir  /tmp/newscoop-$DEBVERSION
cd     /tmp/newscoop-$DEBVERSION

echo -n " +++ building newscoop-${DEBVERSION}.deb in: "
pwd
echo " +++ downloading upstream release.."

curl -L http://downloads.sourceforge.net/project/newscoop/$UPSTREAMDIST/newscoop-$UPSTREAMVERSION.tar.gz | tar xzf -

# done in README.debian
rm newscoop/INSTALL
# documentation for /usr/share/doc/newscoop
for file in ChangeLog CREDITS COPYING README UPGRADE; do
  mv -vi newscoop/$file ./
done

# fixes for 3.5.0-rc2
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

mv -vi newscoop/htaccess newscoop/.htaccess

############################

cp -avi $DEBPATH ./
debuild $@

ls -l /tmp/newscoop*deb
ls -l /tmp/newscoop*changes
