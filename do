#!/usr/bin/env sh

cd $(dirname $0)

build () {
    docker build -t christmaslist-front docker/php-server
    docker build -t christmaslist-cli docker/php-cli
}

buildprod () {
    build
    composer install
    sf cache:clear --env=prod
    rm -rf docker/php-server-prod/data
    mkdir docker/php-server-prod/data
    cp -rf app src web vendor docker/php-server-prod/data
    docker build -t christmaslist-front-prod docker/php-server-prod
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

stop () {
    docker stop christmaslist-front christmaslist-mongo
    docker rm christmaslist-front christmaslist-mongo
}

composer () {
    startmongo
    docker run -it --rm -u $(id -u):$(id -g) -v $PWD:/var/www christmaslist-cli --link christmaslist-mongo:mongodb /usr/local/bin/composer $@
}

sf () {
    startmongo
    docker run -it --rm -u $(id -u):$(id -g) -v $PWD:/var/www christmaslist-cli --link christmaslist-mongo:mongodb app/console $@
}

if [ $# -eq 0 ]
then
    start
    exit
fi

$@
