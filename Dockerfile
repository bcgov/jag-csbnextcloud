FROM nextcloud:latest
VOLUME /var/www/html
RUN chmod -R 0770 /var/www/html

# add internal crons to systemd
RUN apt-get update && apt-get install -y \
    supervisor \
  && rm -rf /var/lib/apt/lists/* \
  && mkdir /var/log/supervisord /var/run/supervisord
  
RUN chmod -R 0770 /var/log/supervisord
COPY supervisord.conf /

ENV NEXTCLOUD_UPDATE=1

CMD ["/usr/bin/supervisord", "-c", "/supervisord.conf"]

# Nextcloud Plugins

# Security Enhancements
