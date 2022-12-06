FROM php:7.1-fpm
RUN apt update -y \
    && apt-get install -y zip