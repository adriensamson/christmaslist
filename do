#!/usr/bin/env sh

cd $(dirname $0)

build () {
    docker build -t christmaslist-front docker/php-server
    docker build -t christmaslist-cli docker/php-cli
}

buildprod () {
    build

    docker rm christmaslist-code-front
    docker run --name christmaslist-code-front christmaslist-front true

    docker run --rm --volumes-from christmaslist-code-front -v $PWD:/workdir christmaslist-front cp -rf /workdir/app /workdir/src /workdir/web /workdir/composer.json /workdir/composer.lock /srv
    docker run --rm --volumes-from christmaslist-code-front christmaslist-front rm -rf /srv/app/cache /srv/app/logs
    docker run --rm --volumes-from christmaslist-code-front christmaslist-front mkdir -p /srv/app/cache /srv/app/logs

    docker run -it --rm --volumes-from christmaslist-code-front christmaslist-cli /usr/local/bin/composer install
    docker run -it --rm --volumes-from christmaslist-code-front christmaslist-cli app/console cache:clear --env=prod

    docker run --rm --volumes-from christmaslist-code-front christmaslist-front chgrp -R www-data /srv/app/cache /srv/app/logs
    docker run --rm --volumes-from christmaslist-code-front christmaslist-front chmod -R g+w /srv/app/cache /srv/app/logs
    docker run --rm --volumes-from christmaslist-code-front christmaslist-front rm /srv/web/app_dev.php
}

startmysql () {
    if ! docker inspect christmaslist-data-mysql 1>/dev/null 2>&1
    then
        docker run --name christmaslist-data-mysql adriensamson/mysql true
    fi

    if docker inspect christmaslist-mysql 1>/dev/null 2>&1
    then
        if [ "$(docker inspect -f '{{ .State.Running }}' christmaslist-mysql)" != "true" ]
        then
            docker restart christmaslist-mysql
        fi
    else
        docker run -itd --volumes-from christmaslist-data-mysql --name christmaslist-mysql adriensamson/mysql
    fi
}

start () {
    startmysql

    if [ "$(docker inspect -f '{{ .State.Running }}' christmaslist-front 2>/dev/null)" != "<no value>" ]
    then
        if [ "$(docker inspect -f '{{ .State.Running }}' christmaslist-front)" != "true" ]
        then
            docker restart christmaslist-front
        fi
    else
        docker run -itd -v $PWD:/srv --name christmaslist-front --link christmaslist-mysql:mysql christmaslist-front
    fi

    docker inspect -f '{{ .NetworkSettings.IPAddress }}' christmaslist-front
}

startprod () {
    startmysql

    docker run -itd --volumes-from christmaslist-code-front --name christmaslist-front-prod --link christmaslist-mysql:mysql christmaslist-front
    docker inspect -f '{{ .NetworkSettings.IPAddress }}' christmaslist-front-prod
}

stop () {
    docker stop christmaslist-front christmaslist-mongo
    docker rm christmaslist-front christmaslist-mongo
}

composer () {
    startmysql
    docker run -it --rm -u $(id -u):$(id -g) -v $PWD:/srv --link christmaslist-mysql:mysql christmaslist-cli /usr/local/bin/composer $@
}

sf () {
    startmysql
    docker run -it --rm -u $(id -u):$(id -g) -v $PWD:/srv --link christmaslist-mysql:mysql christmaslist-cli app/console $@
}

if [ $# -eq 0 ]
then
    start
    exit
fi

$@
