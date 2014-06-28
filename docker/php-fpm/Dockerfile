FROM stackbrew/debian:wheezy

ENV DEBIAN_FRONTEND noninteractive

RUN echo "deb http://http.debian.net/debian wheezy-backports main" >/etc/apt/sources.list.d/backports.list
RUN apt-get update

RUN apt-get install -y php5-fpm php5-cli php5-mongo php5-mcrypt php5-curl curl

RUN sed -i 's|listen = /var/run/php5-fpm.sock|listen = 9009|g' /etc/php5/fpm/pool.d/www.conf
RUN sed -i 's|;cgi.fix_pathinfo=0|cgi.fix_pathinfo=0|g' /etc/php5/fpm/pool.d/www.conf
RUN sed -i 's|;date.timezone =|date.timezone = "Europe/Paris"|g' /etc/php5/fpm/php.ini

EXPOSE 9009
CMD php5-fpm -R && /bin/bash
