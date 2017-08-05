#!/usr/bin/env bash
set -o nounset
set -o errexit

apt-get install -y libevent-dev
git clone https://github.com/expressif/pecl-event-libevent
cd pecl-event-libevent

phpize
./configure
make
make install
