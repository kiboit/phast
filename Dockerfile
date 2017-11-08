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
RUN    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('SHA384', 'composer-setup.php') === '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php --install-dir=/usr/bin/ --filename=composer \
    && php -r "unlink('composer-setup.php');"

