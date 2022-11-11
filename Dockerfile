FROM php
RUN apt update && apt install -yq git libzip-dev zip libicu-dev libpq-dev postgresql vim
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql && docker-php-ext-install pdo_pgsql pgsql
COPY --from=composer /usr/bin/composer /usr/bin/composer
ADD . /app
WORKDIR /app
ENV LOAD_ENV 1
RUN composer install
CMD php -S 0.0.0.0:80 src/server.php
