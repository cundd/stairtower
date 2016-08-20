##
# Docker file for stairtower
#
# Run:
# docker run -d -p 1338:1338 -v $project_dir/var:/usr/src/stairtower/var cundd/stairtower
FROM php:7.0-cli

COPY . /usr/src/stairtower
WORKDIR /usr/src/stairtower

EXPOSE 1338
VOLUME /usr/src/stairtower/var

RUN apt-get update && apt-get install -y git zip
RUN sh ./Resources/Private/Scripts/composer-install.sh
RUN composer.phar install --no-dev

CMD [ "php", "./bin/console", "server:start", "0.0.0.0" ]
