# Start with fresh Unbuntu
FROM ubuntu:14.04

# Install dependencies
RUN apt-get update && \
DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
git \
apache2 \
php5-cli php5-curl php5-mysql php5-gd php5-intl \
libapache2-mod-php5 \
imagemagick \
curl

EXPOSE 80

WORKDIR /usr/share/newscoop

#VOLUME /var/www

# copy virtual host config and source code
ADD docker/newscoop.conf /etc/apache2/sites-available/newscoop.conf
ADD docker/newscoop-dev.conf /etc/apache2/sites-available/newscoop-dev.conf
ADD newscoop /var/www/newscoop

# Enable production env
RUN a2ensite newscoop

# add management scripts
ADD docker /usr/share/newscoop

# update permissions
# 1000 needs to be a dynamic var for the userid of the files
# on the host
RUN usermod -u 1000 www-data

# turn on mod_rewrite, update php config
#RUN a2ensite newscoop.conf
RUN a2enmod rewrite php5
RUN /bin/echo 'date.timezone = "Europe/Berlin"' >> /etc/php5/apache2/php.ini

ENTRYPOINT ["/usr/share/newscoop/docker-entrypoint.sh"]
CMD ["/usr/sbin/apachectl", "-D", "FOREGROUND"]
