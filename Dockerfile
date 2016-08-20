FROM php:7.0-cli

COPY . /usr/src/stairtower
WORKDIR /usr/src/stairtower

EXPOSE 1338

RUN apt-get update && apt-get install -y git zip
RUN sh ./Resources/Private/Scripts/composer-install.sh
RUN composer.phar install --no-dev

CMD [ "php", "./bin/console", "server:start", "0.0.0.0" ]
