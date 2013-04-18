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
rm newscoop/vendor/bombayworks/zendframework1/LICENSE.txt
rm newscoop/vendor/guzzle/guzzle/LICENSE
rm newscoop/vendor/symfony/event-dispatcher/Symfony/Component/EventDispatcher/LICENSE

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

### fixes for 4.1.0 ###
if test "${UPSTREAMVERSION}" == "4.1.0"; then

chmod +x newscoop/vendor/bombayworks/zendframework1/bin/zf.sh
chmod +x newscoop/vendor/doctrine/dbal/bin/doctrine-dbal
chmod +x newscoop/vendor/doctrine/dbal/run-all.sh
chmod +x newscoop/vendor/doctrine/orm/bin/doctrine
chmod +x newscoop/vendor/doctrine/orm/run-all.sh
chmod +x newscoop/vendor/doctrine/orm/tools/sandbox/doctrine

chmod -x newscoop/plugins/debate/smarty_camp_plugins/block.list_debateanswer_attachments.php
chmod -x newscoop/plugins/soundcloud/smarty_camp_plugins/block.list_soundcloud_tracks.php
chmod -x newscoop/install/sql/upgrade/4.0.x/2012-12-18/tables.sql
chmod -x newscoop/plugins/poll/smarty_camp_plugins/function.pollanswer_edit.php
chmod -x newscoop/plugins/debate/smarty_camp_plugins/block.debate_form.php
chmod -x newscoop/plugins/poll/smarty_camp_plugins/block.list_pollanswer_attachments.php
chmod -x newscoop/template_engine/metaclasses/MetaDbObject.php
chmod -x newscoop/plugins/poll/smarty_camp_plugins/block.poll_form.php
chmod -x newscoop/template_engine/metaclasses/MetaAttachment.php
chmod -x newscoop/library/Newscoop/Services/ListUserService.php
chmod -x newscoop/library/Newscoop/Entity/Repository/CommentRepository.php
chmod -x newscoop/plugins/debate/smarty_camp_plugins/function.debateanswer_edit.php
chmod -x newscoop/template_engine/classes/UsersList.php
chmod -x newscoop/plugins/debate/template_engine/classes/DebateIssue.php
chmod -x newscoop/library/Newscoop/ListResult.php
chmod -x newscoop/library/Newscoop/ValueObject.php
chmod -x newscoop/library/Newscoop/User/UserCriteria.php
chmod -x newscoop/plugins/debate/smarty_camp_plugins/function.debatevotes.php
chmod -x newscoop/template_engine/metaclasses/MetaUser.php
chmod -x newscoop/plugins/poll/smarty_camp_plugins/block.list_poll_answers.php
chmod -x newscoop/admin-files/articles/comments/show_comments.php
chmod -x newscoop/plugins/debate/smarty_camp_plugins/block.list_debate_days.php
chmod -x newscoop/library/Newscoop/Entity/Comment.php
chmod -x newscoop/application/modules/admin/controllers/CommentController.php
chmod -x newscoop/plugins/poll/smarty_camp_plugins/block.pollanswer_ajax.php
chmod -x newscoop/plugins/poll/classes/PollIssue.php
chmod -x newscoop/plugins/debate/classes/DebateAnswer.php
chmod -x newscoop/plugins/poll/smarty_camp_plugins/block.list_polls.php
chmod -x newscoop/plugins/debate/smarty_camp_plugins/block.list_debate_votes.php
chmod -x newscoop/plugins/soundcloud/smarty_camp_plugins/function.assign_soundcloud_tracks.php
chmod -x newscoop/library/Newscoop/Entity/Repository/UserRepository.php
chmod -x newscoop/plugins/debate/smarty_camp_plugins/block.list_debates.php

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
