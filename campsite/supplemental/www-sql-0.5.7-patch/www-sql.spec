Name: www-sql
Version: 0.5.7
Release: 1
Group: Applications/Databases
Summary: www-sql allows commands to be embedded in html to allow reports
Copyright: GPL
Source: ftp://ftp.daa.com.au/pub/james/www-sql/www-sql-0.5.6.tar.gz
Requires: apache

%description
www-sql is a small scripting language that allows you to embed commands in
HTML documents that are evaluated when the page is accessed (via the www-sql
CGI program), allowing information from a database to be inserted into the
document.  The program is also flexible enough to allow the insertion of new
data and updating of current data in the tables.

The idea is similar to PHP, but www-sql is easier to learn, and easier to
set up.

%prep
%setup

# guess what database system is in use ...
if which mysql > /dev/null; then
  TARGET=mysql
elif which psql > /dev/null; then
  TARGET=pgsql
else
  TARGET=mysql
fi
CFLAGS="$RPM_OPT_FLAGS" YACC=yacc ./configure --with-database="$TARGET" --enable-apache-action-check

%build
make

%install
make install CGI_DIR=/home/httpd/cgi-bin

%post
if grep 'Action www-sql' /etc/httpd/conf/srm.conf>/dev/null; then
  :
else
  echo 'Creating association between .sql files and www-sql...'
  echo 'Action www-sql /cgi-bin/www-sql' >> /etc/httpd/conf/srm.conf
  echo 'AddHandler www-sql sql' >> /etc/httpd/conf/srm.conf
  echo 'Restarting apache...'
  /etc/rc.d/init.d/httpd* stop ; /etc/rc.d/init.d/httpd* start
fi
echo "Consider creating a user nobody for your databases"

%files
/home/httpd/cgi-bin/www-sql
%doc example.sql
%doc COPYING Changelog README www-sql.html





