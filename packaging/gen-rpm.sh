#/bin/sh
# run  gen-debian-package.sh first!
# edit rpm/newscoop.spec file -> set version number
#

RPMVERS=$(awk '/Version:/{print $2;}' rpm/newscoop.spec)
RPMRELEASE=$(awk '/Release:/{printf "-%s",$2;}' rpm/newscoop.spec)
VERSION=$(echo $RPMVERS | sed 's/-.*$//g')
TMP=/tmp
echo "rpm-vers: $RPMVERS"
echo "rpm-release: $RPMRELEASE"
echo "version:  $VERSION"
echo "base-dir: ${TMP}/newscoop-${VERSION}"

if [ ! -d ${TMP}/newscoop-${VERSION}/debian ]; then
	echo "debian pre-build dir not found. run gen-debian-package.sh first."
	exit 1
fi

echo -n "OK? [enter|CTRL-C]" ; read

cp -r ./rpm /tmp/newscoop-${VERSION}/
cd ${TMP}/

# workarounds for Newscoop 4.2 spaces in filenames, symbolic links etc.
mv newscoop-${VERSION}/newscoop/vendor/symfony/symfony/src/Symfony/Component/Finder/Tests/Fixtures/with\ space/ newscoop-${VERSION}/newscoop/vendor/symfony/symfony/src/Symfony/Component/Finder/Tests/Fixtures/with_space/
mv newscoop-${VERSION}/newscoop/vendor/smarty/smarty/development/lexer/Lempar\ Original.php newscoop-${VERSION}/newscoop/vendor/smarty/smarty/development/lexer/Lempar_Original.php

cd /tmp/newscoop-${VERSION}/newscoop/admin-files/lang/
rm by
cp -r be by

rm cz
cp -r cs cz

rm ge
cp -r ka ge

rm kr
cp -r ko kr

cd /tmp/newscoop-${VERSION}/newscoop/plugins/poll/admin-files/lang/
rm by
cp -r be by

rm cz
cp -r cs cz

rm ge
cp -r ka ge

rm kr
cp -r ko kr

cd /tmp/newscoop-${VERSION}/newscoop/plugins/recaptcha/admin-files/lang/
rm by
cp -r be by

rm cz
cp -r cs cz

rm ge
cp -r ka ge

rm kr
cp -r ko kr

cd /tmp

# end workarounds

tar czf /tmp/rpm_newscoop-${VERSION}.tar.gz newscoop-${VERSION}/
cd /tmp/newscoop-${VERSION}/

mv -vi /tmp/rpm_newscoop-${VERSION}.tar.gz ${HOME}/rpmbuild/SOURCES/newscoop-${VERSION}.tar.gz
rpmbuild -bb --sign rpm/newscoop.spec || exit 1

ls -l ${HOME}/rpmbuild/RPMS/*/newscoop-${RPMVERS}${RPMRELEASE}*.rpm
#ls -l ${HOME}/rpmbuild/SRPMS/newscoop-${RPMVERS}${RPMRELEASE}*.src.rpm

if [ `hostname` != "soyuz" ]; then
	exit
fi

echo -n "UPLOAD? [enter|CTRL-C]" ; read

YUMSIG=$(grep -v "^#" ~/.rpmmacros  | grep "_gpg_name  Sourcefabric")

if [ -z "$YUMSIG" ]; then
	YUMHOST=rg42.org
	YUMPATH=/var/www/yum
else
	YUMHOST=yum.sourcefabric.org
	YUMPATH=/home/rgareus/yum
fi

rsync -P --bwlimit=70 ${HOME}/rpmbuild/RPMS/noarch/newscoop-${RPMVERS}${RPMRELEASE}.noarch.rpm ${YUMHOST}:${YUMPATH}/18/i386/ || exit
rsync -P --bwlimit=70 ${HOME}/rpmbuild/SRPMS/newscoop-${RPMVERS}${RPMRELEASE}.src.rpm ${YUMHOST}:${YUMPATH}/18/source/ || exit

ssh ${YUMHOST} << EOF
cd ${YUMPATH}/18/x86_64/
ln ../i386/newscoop-${RPMVERS}${RPMRELEASE}.noarch.rpm

createrepo ${YUMPATH}/18/source/
createrepo ${YUMPATH}/18/i386/
createrepo ${YUMPATH}/18/x86_64/
EOF
