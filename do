#!/usr/bin/env sh

cd $(dirname $0)
DO_DIR=$(pwd)
DO=./$(basename $0)

build () {
    docker build -t christmaslist-front docker/php-server
    docker build -t christmaslist-cli docker/php-cli
}

buildprod () {
    $DO build
    $DO composer install
    $DO sf cache:clear --env=prod
    rm -rf docker/php-server-prod/data
    mkdir docker/php-server-prod/data
    cp -rf app src web vendor docker/php-server-prod/data
    docker build -t christmaslist-front-prod docker/php-server-prod
}

run () {
    docker rm christmaslist-mongo christmaslist-front

    docker run -d -t -i -v $DO_DIR/app/var/mongo:/data/db --name=christmaslist-mongo mongo
    docker run -d -t -i -v $DO_DIR:/var/www --name=christmaslist-front --link=christmaslist-mongo:mongodb christmaslist-front
}

stop () {
    docker stop christmaslist-mongo christmaslist-front
}

composer () {
    docker run -t -i -v $(pwd):/var/www christmaslist-cli /usr/local/bin/composer $@
}

sf () {
    docker run -t -i -v $(pwd):/var/www christmaslist-cli app/console $@
}

if [ -z "$1" ]
then
    echo "Usage: $DO <command>"
    echo "Commands:"
    echo "    build"
    echo "    buildprod"
    echo "    run"
    echo "    stop"
    echo "    composer"
    echo "    sf"
    exit
fi

$@
