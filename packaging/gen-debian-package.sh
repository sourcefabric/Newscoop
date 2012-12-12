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

### fixes for 4.0.3 ###
if test "${UPSTREAMVERSION}" == "4.0.3"; then

	rm newscoop/install/sample_templates/rockstar/templates/set_rockstar/assets/.DS_Store
	rm newscoop/install/sample_templates/rockstar/templates/set_rockstar/assets/css/.DS_Store
	rm newscoop/install/sample_templates/rockstar/templates/set_rockstar/assets/fonts/.DS_Store
	rm newscoop/install/sample_templates/rockstar/templates/set_rockstar/assets/js/libs/.DS_Store
	rm newscoop/install/sample_templates/rockstar/templates/set_rockstar/pictures/.DS_Store
	rm newscoop/themes/unassigned/set_rockstar/assets/.DS_Store
	rm newscoop/themes/unassigned/set_rockstar/assets/css/.DS_Store
	rm newscoop/themes/unassigned/set_rockstar/assets/fonts/.DS_Store
	rm newscoop/themes/unassigned/set_rockstar/assets/js/libs/.DS_Store
	rm newscoop/themes/unassigned/set_rockstar/pictures/.DS_Store

	chmod +x newscoop/vendor/doctrine/dbal/bin/doctrine-dbal
	chmod +x newscoop/vendor/doctrine/dbal/run-all.sh
	chmod +x newscoop/vendor/doctrine/orm/bin/doctrine
	chmod +x newscoop/vendor/doctrine/orm/run-all.sh
	chmod +x newscoop/vendor/doctrine/orm/tools/sandbox/doctrine

	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/PhotoQuery.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Exif/Entry.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Exif/Extension/Tags.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/AlbumEntry.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/DublinCore.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/CommentEntry.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/Extension/Weight.php
	chmod -x newscoop/library/Newscoop/Services/Ingest/PublisherService.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Cloud/QueueService/Adapter/ZendQueue.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Health.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/Extension/User.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/Extension/Client.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/Extension/CommentingEnabled.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Config/Yaml.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Docs/DocumentListEntry.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Cloud/DocumentService/Adapter/WindowsAzure/Query.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Health/ProfileFeed.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Service/Amazon/S3.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/Extension/Position.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Media/Extension/MediaDescription.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/Extension/NumPhotosRemaining.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/Extension/Access.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Cloud/QueueService/Message.php
	chmod -x newscoop/scripts/newscoop.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/Extension/Checksum.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Health/ProfileListFeed.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Exif/Extension/Exposure.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Books.php
	chmod -x newscoop/library/Newscoop/Tools/Console/Command/UpdateIngestCommand.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Cloud/DocumentService/Adapter/WindowsAzure.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/YouTube/Extension/Control.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/UserQuery.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Media/Extension/MediaGroup.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Docs.php
	chmod -x newscoop/library/Newscoop/Ingest/Parser/SwisstxtParser.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Amf/Parse/Resource/MysqlResult.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/Extension/NumPhotos.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/UserFeed.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Exif/Extension/FStop.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Exif/Extension/Distance.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Exif/Extension/Flash.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Media/Extension/MediaPlayer.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Cache/Backend/ZendServer/ShMem.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/Extension/Timestamp.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Exif/Feed.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/YouTube/Extension/Private.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Service/Amazon/Exception.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Cloud/AbstractFactory.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Geo/Extension/GmlPos.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Media/Entry.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/YouTube/Extension/CountHint.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Exif/Extension/Model.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Docs/DocumentListFeed.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/Extension/BytesUsed.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Amf/Adobe/DbInspector.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Books/VolumeQuery.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Docs/Query.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/AlbumFeed.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/YouTube/Extension/Token.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Health/Query.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Media/Extension/MediaText.php
	chmod -x newscoop/library/Newscoop/Services/IngestService.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Media/Extension/MediaTitle.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Amf/Auth/Abstract.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/UserEntry.php
	chmod -x newscoop/application/configs/application.ini-dist
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Config/Json.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Exif.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/YouTube/Extension/MediaRating.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/Extension/PhotoId.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Cache/Backend/ZendServer/Disk.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/Extension/Width.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Geo.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/YouTube/Extension/MediaContent.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Service/Twitter.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Amf/Adobe/Auth.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/Extension/Size.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Exif/Extension/Iso.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Service/Amazon/S3/Exception.php
	chmod -x newscoop/composer.json
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/Extension/AlbumId.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Media/Feed.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/App/Feed.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Geo/Extension/GmlPoint.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Media/Extension/MediaCategory.php
	chmod -x newscoop/vendor/zendframework/zendframework1/bin/zf.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Health/ProfileListEntry.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/Extension/Id.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Media/Extension/MediaCopyright.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/YouTube/Extension/MediaGroup.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/App/FeedEntryParent.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/YouTube/MediaEntry.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Cloud/QueueService/Adapter/WindowsAzure.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/Extension/MaxPhotosPerAlbum.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/Extension/Name.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/TagEntry.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Media/Extension/MediaThumbnail.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/Extension/Rotation.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Media/Extension/MediaRestriction.php
	chmod -x newscoop/application.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Captcha/Exception.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/Extension/Nickname.php
	chmod -x newscoop/application/Bootstrap.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Config/Writer/Json.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Media/Extension/MediaKeywords.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Http/Response/Stream.php
	chmod -x newscoop/admin-files/plugins/manage.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Geo/Entry.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Media.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Health/Extension/Ccr.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Amf/Value/Messaging/ArrayCollection.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Health/ProfileEntry.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/Extension/QuotaLimit.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/Extension/Version.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Exif/Extension/Make.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/AlbumQuery.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Http/Client/Adapter/Stream.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Media/Extension/MediaContent.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/Extension/CommentCount.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Amf/Parse/Resource/Stream.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/PhotoFeed.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Media/Extension/MediaRating.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Media/Extension/MediaHash.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/PhotoEntry.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Config/Writer/Yaml.php
	chmod -x newscoop/library/Newscoop/Tools/Console/Command/UpdateAutoloadCommand.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Amf/Adobe/Introspector.php
	chmod -x newscoop/vendor/zendframework/zendframework1/bin/zf.bat
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/Extension/Location.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/Extension/QuotaCurrent.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Service/Twitter/Exception.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Exif/Extension/Time.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Exif/Extension/ImageUniqueId.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Geo/Extension/GeoRssWhere.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/Extension/Thumbnail.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Cache/Backend/ZendServer.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Geo/Feed.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/YouTube/Extension/Link.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Media/Extension/MediaCredit.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Photos/Extension/Height.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Service/Amazon/S3/Stream.php
	chmod -x newscoop/vendor/zendframework/zendframework1/library/Zend/Gdata/Exif/Extension/FocalLength.php

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
