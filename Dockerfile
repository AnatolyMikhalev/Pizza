FROM ubuntu:latest
LABEL authors="anatoly"

ENTRYPOINT ["top", "-b"]




#
#FROM ghcr.io/laravel/sail-php80-composer:latest
#
## Установите XDebug
#RUN pecl install xdebug \
#    && docker-php-ext-enable xdebug
#
## Копируйте оставшуюся часть Dockerfile, как указано в вашем `sail:install`
#COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
#COPY . /var/www/html
#
#WORKDIR /var/www/html
#
#ENTRYPOINT ["./vendor/bin/sail"]
