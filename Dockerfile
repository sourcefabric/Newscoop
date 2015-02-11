# Start with fresh Unbuntu
FROM ubuntu:14.04

# Install dependencies
RUN apt-get update && \
DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
supervisor \
git \
apache2 \
mysql-server \
php5-cli php5-curl php5-mysql php5-gd php5-intl \
libapache2-mod-php5 \
imagemagick \
curl

EXPOSE 80 3306

# copy virtual host config and source code
ADD newscoop.conf /etc/apache2/sites-enabled/newscoop.conf
RUN mkdir /var/www/newscoop
ADD newscoop /var/www/newscoop


# install composer and install PHP dependencies
RUN cd /var/www/newscoop && curl -s https://getcomposer.org/installer | php
RUN cd /var/www/newscoop && /usr/bin/php composer.phar install --no-dev

# update permissions
RUN chown -R www-data:www-data /var/www/newscoop

# turn on mod_rewrite, update php config
#RUN a2ensite newscoop.conf
RUN a2enmod rewrite php5
RUN /bin/echo 'date.timezone = "Europe/Berlin"' >> /etc/php5/apache2/php.ini

ADD start-apache2.sh /start-apache2.sh
ADD start-mysqld.sh /start-mysqld.sh
ADD start-newscoop.sh /start-newscoop.sh
ADD supervisord-apache2.conf /etc/supervisor/conf.d/supervisord-apache2.conf
ADD supervisord-mysqld.conf /etc/supervisor/conf.d/supervisord-mysqld.conf
RUN chmod 755 /*.sh

CMD ["/bin/bash", "/start-newscoop.sh"]
