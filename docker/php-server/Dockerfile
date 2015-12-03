FROM stackbrew/debian:jessie

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update && apt-get install -y nginx php5-fpm php5-mysql php5-mcrypt php5-intl php5-curl supervisor

RUN echo "daemon off;" >> /etc/nginx/nginx.conf

RUN sed -i 's|;cgi.fix_pathinfo=0|cgi.fix_pathinfo=0|g' /etc/php5/fpm/pool.d/www.conf
RUN sed -i 's|;date.timezone =|date.timezone = "Europe/Paris"|g' /etc/php5/fpm/php.ini

COPY default /etc/nginx/sites-enabled/default
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

VOLUME /srv
WORKDIR /srv
EXPOSE 80
CMD ["supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]
