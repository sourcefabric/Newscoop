# not for myself:
# http://fedoraproject.org/wiki/PackageMaintainers/CreatingPackageHowTo
#
%define manifest %{_builddir}/%{name}-%{version}-%{release}.manifest

Summary:        The open content management system for professional journalists
Name:           newscoop
Version:        3.5.0.rc2
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
Requires: curl
Requires: mysql
Requires: GraphicsMagick
#Requires: mail-server
Requires: sendmail

# TODO: - look up package names for F14 and RHEL
# apache2 | httpd,
# libapache2-mod-php5 | php5,
# php5-mysql, php5-cli, php5-gd,
# mysql-client | virtual-mysql-client,
# graphicsmagick | imagemagick,
# default-mta | mail-transport-agent,
# curl

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

cd $RPM_BUILD_ROOT
rm -f %{manifest}
find ./var/ -type d \
        | sed '1,2d;s,^\.,\%attr(-\,www-data\,www-data) \%dir ,' >> %{manifest}
find ./var/ -type f \
        | sed 's,^\.,\%attr(-\,www-data\,www-data) ,' >> %{manifest}
find ./var/ -type l \
        | sed 's,^\.,\%attr(-\,www-data\,www-data) ,' >> %{manifest}

%clean
rm -f %{manifest}
rm -rf %{buildroot}

%files -f %{manifest}
%defattr(-,root,root)
%doc ChangeLog  COPYING  CREDITS README  UPGRADE
%config /etc/newscoop/3.5/apache.conf
%config /etc/newscoop/3.5/newscoop.ini

%post
# symlink config files
configdir="/etc/newscoop/3.5"
includefile="${configdir}/apache.conf"
phpinifile="${configdir}/newscoop.ini"
webserver="httpd"
php="php5"

if [ ! -d /etc/$webserver/conf.d/ ]; then
		install -d -m755 /etc/$webserver/conf.d/
fi
if [ ! -e /etc/$webserver/conf.d/newscoop.conf ]; then
	ln -s ${includefile} /etc/$webserver/conf.d/newscoop.conf
	a2enmod rewrite &>/dev/null || true
fi

if [ ! -d /etc/$php/conf.d/ ]; then
	install -d -m755 /etc/$php/conf.d/
fi
if [ ! -e /etc/$php/conf.d/newscoop.ini ]; then
	ln -s ${phpinifile} /etc/$php/conf.d/newscoop.ini
fi

# XXX: restart apache - check if this is the recommended way
/etc/init.d/httpd restart


%postun
webserver="httpd"
php="php5"
if [ -L /etc/$webserver/conf.d/newscoop.conf ]; then
	rm -f /etc/$webserver/conf.d/newscoop.conf || true
fi
		
if [ -L /etc/$php/conf.d/newscoop.ini ]; then
	rm -f /etc/$php/conf.f/newscoop.ini || true
fi
		
# XXX: restart apache - check if this is the recommended way
/etc/init.d/httpd restart


%changelog
* Wed Jan 26 2011 Robin Gareus <robin@gareus.org>
- Initial Version
