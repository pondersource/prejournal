FROM php
RUN apt update && apt install -yq git
COPY --from=composer /usr/bin/composer /usr/bin/composer
ADD . /app
WORKDIR /app
RUN composer install
RUN mv testnet.env .env
ENV LOAD_ENV 1
RUN apt-get install libzip-dev zip libicu-dev libpq-dev -y
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql && docker-php-ext-install pdo_pgsql pgsql
CMD php src/server.php
