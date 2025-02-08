FROM php:8.2-fpm

# RUN apt-get update && \
#     apt-get install -y \
#         php-xdebug \
#         php8.2-mysql \
#         php8.2-sqlite \
#         mysql-client

#RUN docker-php-ext-enable xdebug
#RUN docker-php-ext-install -j$(nproc) pdo_mysql

RUN docker-php-ext-install -j$(nproc) pdo_mysql

