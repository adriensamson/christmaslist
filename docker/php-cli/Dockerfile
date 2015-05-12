FROM stackbrew/debian:wheezy

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update && apt-get install -y php5-cli php5-mysql php5-mcrypt php5-intl php5-curl curl git

RUN sed -i 's|;date.timezone =|date.timezone = "Europe/Paris"|g' /etc/php5/cli/php.ini

RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

VOLUME /srv
WORKDIR /srv
CMD php
