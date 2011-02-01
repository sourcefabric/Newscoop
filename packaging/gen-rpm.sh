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

cd ${TMP}/
tar czf /tmp/newscoop-${VERSION}.tar.gz newscoop-${VERSION}/
cd -
mv -vi /tmp/newscoop-${VERSION}.tar.gz ${HOME}/rpmbuild/SOURCES/newscoop-${VERSION}.tar.gz
rpmbuild -ba --sign rpm/newscoop.spec

ls -l ${HOME}/rpmbuild/RPMS/*/newscoop-${VERSION}*.rpm
ls -l ${HOME}/rpmbuild/SRPMS/newscoop-${VERSION}*.src.rpm

if [ `hostname` != "soyuz" ]; then
	exit
fi

echo -n "UPLOAD? [enter|CTRL-C]" ; read

rsync -P -bwlimit=70 ${HOME}/rpmbuild/RPMS/noarch/newscoop-${RPMVERS}${RPMRELEASE}.noarch.rpm rg42.org:/var/www/yum/14/i386/ || exit
rsync -P -bwlimit=70 ${HOME}/rpmbuild/SRPMS/newscoop-${RPMVERS}${RPMRELEASE}.src.rpm rg42.org:/var/www/yum/14/source/ || exit

ssh rg42.org << EOF
cd /var/www/yum/14/x86_64/
ln ../i386/newscoop-${RPMVERS}${RPMRELEASE}.noarch.rpm

createrepo /var/www/yum/14/source/
createrepo /var/www/yum/14/i386/
createrepo /var/www/yum/14/x86_64/
EOF
