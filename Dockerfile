FROM php:apache

# Install PDO and PGSQL Drivers
RUN apt-get update && apt-get install -y libpq-dev \
  && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
  && docker-php-ext-install pdo pdo_pgsql pgsql

COPY source /var/www/html