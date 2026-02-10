FROM php:8.3-apache-bookworm as correlation-id

RUN apt-get update && apt-get install -y zlib1g-dev libzip-dev zip unzip git

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*