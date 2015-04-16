#!/usr/bin/env sh

cd $(dirname $0)

build () {
    docker build -t christmaslist-front docker/php-server
    docker build -t christmaslist-cli docker/php-cli
}

buildprod () {
    build

    docker rm christmaslist-code-front
    docker run --name christmaslist-code-front -v /var/www christmaslist-front true

    docker run --rm --volumes-from christmaslist-code-front -v $PWD:/workdir christmaslist-front cp -rf /workdir/app /workdir/src /workdir/web /workdir/composer.json /workdir/composer.lock /var/www
    docker run -it --rm --volumes-from christmaslist-code-front christmaslist-cli /usr/local/bin/composer install
    docker run -it --rm --volumes-from christmaslist-code-front christmaslist-cli app/console cache:clear --env=prod

    docker run --rm --volumes-from christmaslist-code-front christmaslist-front mkdir -p /var/www/app/cache /var/www/app/logs
    docker run --rm --volumes-from christmaslist-code-front christmaslist-front chgrp -R www-data /var/www/app/cache /var/www/app/logs
    docker run --rm --volumes-from christmaslist-code-front christmaslist-front chmod -R g+w /var/www/app/cache /var/www/app/logs
    docker run --rm --volumes-from christmaslist-code-front christmaslist-front rm /var/www/web/app_dev.php
}

startmongo () {
    if ! docker inspect christmaslist-data-mongo 1>/dev/null 2>&1
    then
        docker run --name christmaslist-data-mongo mongo true
    fi

    if docker inspect christmaslist-mongo 1>/dev/null 2>&1
    then
        if [ "$(docker inspect -f '{{ .State.Running }}' christmaslist-mongo)" != "true" ]
        then
            docker restart christmaslist-mongo
        fi
    else
        docker run -itd --volumes-from christmaslist-data-mongo --name christmaslist-mongo mongo
    fi
}

start () {
    startmongo

    if [ "$(docker inspect -f '{{ .State.Running }}' christmaslist-front 2>/dev/null)" != "<no value>" ]
    then
        if [ "$(docker inspect -f '{{ .State.Running }}' christmaslist-front)" != "true" ]
        then
            docker restart christmaslist-front
        fi
    else
        docker run -itd -v $PWD:/var/www --name christmaslist-front --link christmaslist-mongo:mongodb christmaslist-front
    fi

    docker inspect -f '{{ .NetworkSettings.IPAddress }}' christmaslist-front
}

startprod () {
    startmongo

    docker run -itd --volumes-from christmaslist-code-front --name christmaslist-front-prod --link christmaslist-mongo:mongodb christmaslist-front
    docker inspect -f '{{ .NetworkSettings.IPAddress }}' christmaslist-front-prod
}

stop () {
    docker stop christmaslist-front christmaslist-mongo
    docker rm christmaslist-front christmaslist-mongo
}

composer () {
    startmongo
    docker run -it --rm -u $(id -u):$(id -g) -v $PWD:/var/www --link christmaslist-mongo:mongodb christmaslist-cli /usr/local/bin/composer $@
}

sf () {
    startmongo
    docker run -it --rm -u $(id -u):$(id -g) -v $PWD:/var/www --link christmaslist-mongo:mongodb christmaslist-cli app/console $@
}

if [ $# -eq 0 ]
then
    start
    exit
fi

$@
