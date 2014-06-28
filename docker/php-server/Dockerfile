FROM stackbrew/debian:wheezy

ENV DEBIAN_FRONTEND noninteractive

RUN echo "deb http://http.debian.net/debian wheezy-backports main" >/etc/apt/sources.list.d/backports.list
RUN apt-get update

RUN apt-get install -y nginx php5-fpm php5-mongo php5-mcrypt php5-intl php5-curl

#RUN echo "daemon off;" >> /etc/nginx/nginx.conf

RUN sed -i 's|;cgi.fix_pathinfo=0|cgi.fix_pathinfo=0|g' /etc/php5/fpm/pool.d/www.conf
RUN sed -i 's|;date.timezone =|date.timezone = "Europe/Paris"|g' /etc/php5/fpm/php.ini

ADD default /etc/nginx/sites-enabled/default

EXPOSE 8000
CMD php5-fpm -R && nginx && /bin/bash

