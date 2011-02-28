# not for myself:
# http://fedoraproject.org/wiki/PackageMaintainers/CreatingPackageHowTo
#
%define manifest %{_builddir}/%{name}-%{version}-%{release}.manifest

Summary:        The open content management system for professional journalists
Name:           newscoop
Version:        3.5.0
Release:        1
License:        GPL
Packager:       Robin Gareus <robin@gareus.org>

# TODO: This group does not seem right.
# but the closest found in /usr/share/doc/rpm-*/GROUPS 
Group:          Applications/Publishing

# TODO: use upstream tar ball for version > 3.5.2-rc2
# we can not do that ATM because upstream has various issues.
# see ../gen-debian-package.sh 
#http://downloads.sourceforge.net/project/newscoop/$UPSTREAMDIST/newscoop-$UPSTREAMVERSION.tar.gz
Source0:        %{name}-%{version}.tar.gz
URL:            http://www.sourcefabric.org/en/products/newscoop_overview/
BuildRoot:      %{_tmppath}/%{name}-%{version}
BuildArch:      noarch

Requires: httpd 
Requires: php 
Requires: php-cli
Requires: php-gd
Requires: php-mysql
Requires: curl
Requires: mysql
Requires: ImageMagick
# TODO: find MTA package
#Requires: mail-server
#Requires: sendmail

# These are actually recommends only:
#Requires: mysql-server
#Requires:: php-apc | php5-xcache

%description
Newscoop is the open content management system for professional journalists.
Features for the modern newsroom include multiple author management,
issue-and-section based publishing, geolocation and multilingual content 
management. The enterprise-standard journalist’s dashboard and a templating
engine supporting anything from HTML5 to mobile complete this fast production
and publishing system. 

%prep
%setup -q
%build

%install
rm -rf %{buildroot}
mkdir -p %{buildroot}/var/lib/
cp -a newscoop %{buildroot}/var/lib/

# TODO: create config-files - debian/ folder will not be present in tar-ball
mkdir -p %{buildroot}/etc/newscoop/3.5/
cp debian/etc/newscoop.ini %{buildroot}/etc/newscoop/3.5/
cp debian/etc/apache.conf %{buildroot}/etc/newscoop/3.5/
cp debian/etc/apache.vhost.tpl %{buildroot}/etc/newscoop/3.5/
cp debian/etc/newscoop.cron.tpl %{buildroot}/etc/newscoop/3.5/

cd $RPM_BUILD_ROOT
rm -f %{manifest}
find ./var/ -type d \
        | sed '1,2d;s,^\.,\%attr(-\,apache\,apache) \%dir ,' >> %{manifest}
find ./var/ -type f \
        | sed 's,^\.,\%attr(-\,apache\,apache) ,' >> %{manifest}
find ./var/ -type l \
        | sed 's,^\.,\%attr(-\,apache\,apache) ,' >> %{manifest}

%clean
rm -f %{manifest}
rm -rf %{buildroot}

%files -f %{manifest}
%defattr(-,root,root)
%doc ChangeLog  COPYING  CREDITS README  UPGRADE
%config /etc/newscoop/3.5/apache.conf
%config /etc/newscoop/3.5/newscoop.ini
%config /etc/newscoop/3.5/apache.vhost.tpl
%config /etc/newscoop/3.5/newscoop.cron.tpl

%post
# symlink config files
configdir="/etc/newscoop/3.5"
includefile="${configdir}/apache.conf"
phpinifile="${configdir}/newscoop.ini"
webserver="httpd"
php="php5"
dohtaccess="/newscoop"

if [ ! -d /etc/$webserver/conf.d/ ]; then
		install -d -m755 /etc/$webserver/conf.d/
fi
if [ ! -e /etc/$webserver/conf.d/newscoop.conf ]; then
	ln -s ${includefile} /etc/$webserver/conf.d/newscoop.conf
fi

if [ ! -d /etc/$php/conf.d/ ]; then
	install -d -m755 /etc/php.d/
fi
if [ ! -e /etc/php.d/newscoop.ini ]; then
	ln -s ${phpinifile} /etc/php.d/newscoop.ini
fi

# .htaccess file
echo -ne "/RewriteBase/d\nwq\n\n" \
| ed /var/lib/newscoop/.htaccess &>/dev/null || true

if [ -n "${dohtaccess}" ]; then
	echo -ne "/RewriteEngine/\n+1i\n    RewriteBase ${dohtaccess}\n.\nwq\n" \
	| ed /var/lib/newscoop/.htaccess &>/dev/null || true
fi

# XXX: restart apache - check if this is the recommended way
/etc/init.d/httpd restart

## CRON JOB
#CU=apache
#CE=root@localhost
#sed -e "s/__CRON_EMAIL__/${CE}/;s/__WWW_USER__/${CU}/" \
#	${crontplfile} > ${cronincfile}
#if [ ! -d /etc/cron.d/ ]; then
#	install -d -m755 /etc/cron.d/
#fi
#if [ ! -e /etc/cron.d/newscoop ]; then
#	ln -s ${cronincfile} /etc/cron.d/newscoop
#fi

%postun
webserver="httpd"
if [ -L /etc/$webserver/conf.d/newscoop.conf ]; then
	rm -f /etc/$webserver/conf.d/newscoop.conf || true
fi
		
if [ -L /etc/php.d/newscoop.ini ]; then
	rm -f /etc/php.d/newscoop.ini || true
fi

if [ -L /etc/cron.d/newscoop ]; then
	rm -f /etc/cron.d/newscoop || true
fi
# delete generated templates and user-installed plugins
rm -rf /var/lib/newscoop || true
rm -f /etc/newscoop/3.5/newscoop.cron || true
rmdir /etc/newscoop/3.5 || true
rmdir /etc/newscoop/ || true
		
# XXX: restart apache - check if this is the recommended way
/etc/init.d/httpd restart


%changelog
* Wed Jan 26 2011 Robin Gareus <robin@gareus.org>
- Initial Version
