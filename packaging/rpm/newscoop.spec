# how-to:
# http://fedoraproject.org/wiki/PackageMaintainers/CreatingPackageHowTo
#
%define manifest %{_builddir}/%{name}-%{version}-%{release}.manifest

Summary:        The open content management system for professional journalists
Name:           newscoop
Version:        4.2.0
Release:        1
License:        GPLv3
Packager:       Daniel James <daniel@64studio.com>

# List found in /usr/share/doc/rpm-*/GROUPS 
Group:          Applications/Publishing

# TODO: use upstream tar ball for version > 3.5.2-rc2
# we can not do that ATM because upstream has various issues.
# see ../gen-debian-package.sh 
# http://downloads.sourceforge.net/project/newscoop/$UPSTREAMDIST/newscoop-$UPSTREAMVERSION.tar.gz
Source0:        %{name}-%{version}.tar.gz
URL:            http://www.sourcefabric.org/en/newscoop/
BuildRoot:      %{_tmppath}/%{name}-%{version}
BuildArch:      noarch

Requires: httpd 
Requires: php 
Requires: php-cli
Requires: php-gd
Requires: php-mysql
Requires: php-process
Requires: php-xml
Requires: curl
Requires: mysql
Requires: ImageMagick

#These are recommends, rpm does not fully support the Suggests tag though:
#Suggests: mysql-server
#Suggests: postfix

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

# Copy config file
mkdir -p %{buildroot}/etc/newscoop/4.2/
cp rpm/newscoop.ini %{buildroot}/etc/newscoop/4.2/
cp rpm/apache.conf %{buildroot}/etc/newscoop/4.2/

cd $RPM_BUILD_ROOT
rm -f %{manifest}
find ./var/lib/ -type d \
        | sed '1d;s,^\.,\%attr(-\,apache\,apache) \%dir ,' >> %{manifest}
find ./var/lib/ -type f \
        | sed 's,^\.,\%attr(-\,apache\,apache) ,' >> %{manifest}
find ./var/lib/ -type l \
        | sed 's,^\.,\%attr(-\,apache\,apache) ,' >> %{manifest}

%clean
rm -f %{manifest}
rm -rf %{buildroot}

%files -f %{manifest}
%defattr(-,root,root)
%doc newscoop/changelog newscoop/CREDITS newscoop/README.md newscoop/UPGRADE.md
%config /etc/newscoop/4.2/apache.conf
%config /etc/newscoop/4.2/newscoop.ini
#%config /etc/newscoop/4.2/apache.vhost.tpl
#%config /etc/newscoop/4.2/newscoop.cron.tpl

%post
# symlink config files
configdir="/etc/newscoop/4.2"
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
#echo -ne "/RewriteBase/d\nwq\n\n" \
#| ed /var/lib/newscoop/.htaccess &>/dev/null || true
#
#if [ -n "${dohtaccess}" ]; then
#	echo -ne "/RewriteEngine/\n+1i\n    RewriteBase ${dohtaccess}\n.\nwq\n" \
#	| ed /var/lib/newscoop/.htaccess &>/dev/null || true
#fi

# Fix SELinux
chcon -R -t httpd_cache_t /var/lib/newscoop

# restart Apache
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

# delete Newscoop files, but only on uninstallation
if [ "$1" = "0" ]; then

 if [ -L /etc/$webserver/conf.d/newscoop.conf ]; then
	rm -f /etc/$webserver/conf.d/newscoop.conf || true
 fi
		
 if [ -L /etc/php.d/newscoop.ini ]; then
	rm -f /etc/php.d/newscoop.ini || true
 fi

 if [ -L /etc/cron.d/newscoop ]; then
	rm -f /etc/cron.d/newscoop || true
 fi

 rm -rf /var/lib/newscoop/ || true
 rm -rf /etc/newscoop/ || true

fi
		
# restart Apache
/etc/init.d/httpd restart


%changelog
* Wed Jun 5 2013 Daniel James <daniel@64studio.com>
- Update for Newscoop 4.2.0

* Tue Apr 30 2013 Daniel James <daniel@64studio.com>
- Update for Newscoop 4.1.2

* Thu Apr 18 2013 Daniel James <daniel@64studio.com>
- Update for Newscoop 4.1.1

* Mon Jan 28 2013 Daniel James <daniel@64studio.com>
- Update for Newscoop 4.1.0

* Mon Jan 7 2013 Daniel James <daniel@64studio.com>
- Update for Newscoop 4.0.4

* Wed Dec 13 2012 Daniel James <daniel@64studio.com>
- Update for Newscoop 4.0.3

* Wed Jul 18 2012 Daniel James <daniel@64studio.com>
- Update for Newscoop 4.0.2

* Wed Jun 20 2012 Daniel James <daniel@64studio.com>
- Test for upgrade or uninstall before deleting files

* Tue Jun 19 2012 Daniel James <daniel@64studio.com>
- Update for Newscoop 4.0.1

* Tue May 8 2012 Daniel James <daniel@64studio.com>
- Put DocumentRoot in /var/lib as /usr may be read-only

* Mon Apr 30 2012 Daniel James <daniel@64studio.com>
- Update for Newscoop 4.0.0

* Wed Jan 26 2011 Robin Gareus <robin@gareus.org>
- Initial Version
