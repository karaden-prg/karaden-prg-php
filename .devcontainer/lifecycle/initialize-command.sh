#!/bin/bash -eu

echo "USERNAME=${USER}\n\
LOCALE=ja_JP.UTF-8\n\
PHP_TIME_ZONE=Asia/Tokyo\n\
WORKING_DIR=$(realpath `pwd`/..)" > .env
