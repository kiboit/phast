FROM php:5.6.31-cli

RUN apt-get update && apt-get install -y \
        sudo \
        git \
        unzip \
        libjpeg62-turbo-dev \
        libpng-dev \
        libvpx-dev \
    && docker-php-ext-configure gd \
            --with-jpeg-dir=/usr/include/ \
            --with-vpx-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd

RUN useradd -m docker

COPY docker/entrypoint /entrypoint
ENTRYPOINT ["/bin/bash", "/entrypoint"]
