FROM php:5.6.31-cli

RUN apt-get update && apt-get install -y \
        git \
        unzip \
        libjpeg62-turbo-dev \
        libpng-dev \
        libvpx-dev \
    && docker-php-ext-configure gd \
            --with-jpeg-dir=/usr/include/ \
            --with-vpx-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd

RUN wget -O /usr/local/bin/composer \
        'https://github.com/composer/composer/releases/download/1.5.2/composer.phar' && \
    chmod +x /usr/locl/bin/composer
