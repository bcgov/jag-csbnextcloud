FROM nextcloud:latest
VOLUME /var/www/html
RUN chmod -R 0770 /var/www/html

ENV NEXTCLOUD_UPDATE=1

# Nextcloud Plugins

# Security Enhancements
