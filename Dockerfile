FROM nextcloud:latest
VOLUME /var/www/html
RUN chmod -R 0770 /var/www/html

ENV NEXTCLOUD_UPDATE=1
ADD www.conf /usr/local/etc/php-fpm.d/www.conf

ADD move-app /apps/move

# Nextcloud Plugins

# Security Enhancements

# Bug Fixes
RUN touch /usr/local/etc/php/conf.d/redis-session.ini && chmod 666 /usr/local/etc/php/conf.d/redis-session.ini # https://github.com/nextcloud/docker/issues/763#issuecomment-1751694237
