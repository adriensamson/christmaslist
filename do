#!/usr/bin/env sh

cd $(dirname $0)

build () {
    docker build -t christmaslist-front docker/php-server
    docker build -t christmaslist-cli docker/php-cli
}

buildprod () {
    build
    startmysql

    docker rm christmaslist-code-front
    docker run --name christmaslist-code-front christmaslist-front true

    docker run --rm --volumes-from christmaslist-code-front -v $PWD:/workdir christmaslist-front cp -rf /workdir/app /workdir/src /workdir/web /workdir/composer.json /workdir/composer.lock /srv
    docker run --rm --volumes-from christmaslist-code-front christmaslist-front rm -rf /srv/app/cache /srv/app/logs
    docker run --rm --volumes-from christmaslist-code-front christmaslist-front mkdir -p /srv/app/cache /srv/app/logs

    docker run -it --rm --volumes-from christmaslist-code-front --link christmaslist-mysql:mysql christmaslist-cli /usr/local/bin/composer install
    docker run -it --rm --volumes-from christmaslist-code-front --link christmaslist-mysql:mysql christmaslist-cli app/console cache:clear --env=prod

    docker run --rm --volumes-from christmaslist-code-front christmaslist-front chgrp -R www-data /srv/app/cache /srv/app/logs
    docker run --rm --volumes-from christmaslist-code-front christmaslist-front chmod -R g+w /srv/app/cache /srv/app/logs
    docker run --rm --volumes-from christmaslist-code-front christmaslist-front rm /srv/web/app_dev.php
}

exportprod () {
    docker run --rm --volumes-from christmaslist-code-front -v $PWD:/backup christmaslist-front tar -czPf /backup/christmaslist-code-front.tar.gz /srv
}

importprod () {
    docker rm christmaslist-code-front
    docker run --name christmaslist-code-front christmaslist-front true
    docker run --rm --volumes-from christmaslist-code-front -v $PWD:/backup christmaslist-front tar -xzPf /backup/christmaslist-code-front.tar.gz
}

startmysql () {
    if ! docker inspect christmaslist-data-mysql 1>/dev/null 2>&1
    then
        docker run --name christmaslist-data-mysql adriensamson/mysql /srv/setup.sh
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
        docker run -itd -p 8880:80 -v $PWD:/srv --name christmaslist-front --link christmaslist-mysql:mysql christmaslist-front
    fi
}

startprod () {
    startmysql

    docker run -itd --volumes-from christmaslist-code-front --name christmaslist-front-prod --link christmaslist-mysql:mysql christmaslist-front
    docker inspect -f '{{ .NetworkSettings.IPAddress }}' christmaslist-front-prod
}

stop () {
    docker stop christmaslist-front christmaslist-mysql
}
rm () {
    docker rm -f christmaslist-front christmaslist-mysql
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
