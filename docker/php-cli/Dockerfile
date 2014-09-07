FROM stackbrew/debian:wheezy

ENV DEBIAN_FRONTEND noninteractive

RUN echo "deb http://http.debian.net/debian wheezy-backports main" >/etc/apt/sources.list.d/backports.list
RUN apt-get update

RUN apt-get install -y php5-cli php5-mongo php5-mcrypt php5-intl php5-curl curl git

RUN sed -i 's|;date.timezone =|date.timezone = "Europe/Paris"|g' /etc/php5/cli/php.ini

RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

WORKDIR /var/www
CMD php
