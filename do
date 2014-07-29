#!/usr/bin/env sh

build () {
    docker build -t christmaslist-front docker/php-server
    docker build -t christmaslist-cli docker/php-cli
}

run () {
    docker rm christmaslist-mongo christmaslist-front

    docker run -d -t -i -v $(pwd)/app/var/mongo:/data/db --name=christmaslist-mongo mongo $@
    docker run -d -t -i -v $(pwd):/var/www --name=christmaslist-front --link=christmaslist-mongo:mongodb christmaslist-front $@
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
    echo "Usage: $0 <command>"
    echo "Commands:"
    echo "    build"
    echo "    run"
    echo "    stop"
    echo "    composer"
    echo "    sf"
    exit
fi

$@
