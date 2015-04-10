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
    if docker inspect christmaslist-mongo 1>/dev/null 2>&1
    then
        if [ $(docker inspect -f '{{ .State.Running }}' christmaslist-mongo) = "false" ]
        then
            docker restart christmaslist-mongo
        fi
    else
        docker run -itd -v $PWD/app/var/mongo:/data/db --name christmaslist-mongo mongo
    fi
}

start () {
    startmongo
    docker run -itd -v $PWD:/var/www --name christmaslist-front --link christmaslist-mongo:mongodb christmaslist-front
    docker inspect -f '{{ .NetworkSettings.IPAddress }}' christmaslist-front
}

stop () {
    docker stop christmaslist-front christmaslist-mongo
    docker rm christmaslist-front christmaslist-mongo
}

composer () {
    docker run -it -v $PWD:/var/www christmaslist-cli --link christmaslist-mongo:mongodb /usr/local/bin/composer $@
}

sf () {
    startmongo
    docker run -it -v $PWD:/var/www christmaslist-cli app/console $@
}

if [ $# -eq 0 ]
then
    start
    exit
fi

$@
