FROM nextcloud:latest
VOLUME /var/www/html
RUN chmod -R 0770 /var/www/html

ENV NEXTCLOUD_UPDATE=1
ADD www.conf /usr/local/etc/php-fpm.d/www.conf
# Nextcloud Plugins

# Security Enhancements
