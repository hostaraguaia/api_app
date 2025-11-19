# Use a imagem base do PHP (ajuste a versão conforme necessário)
FROM php:8.4-fpm

#COPY 90-xdebug.ini "${PHP_INI_DIR}/conf.d"
#RUN pecl install xdebug
#RUN docker-php-ext-enable xdebug



ENV TZ=America/Sao_Paulo
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
# set your user name, ex: user=bernardo
ARG user=wellsonalmeida
ARG uid=1000


# Install system dependencies
RUN apt-get update && apt-get install -y \
    libmagickwand-dev \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql pgsql mbstring exif pcntl bcmath gd sockets

RUN apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Install the Imagick PHP extension
RUN pecl install imagick \
    && docker-php-ext-enable imagick

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Install redis
RUN pecl install -o -f redis \
    &&  rm -rf /tmp/pear \
    &&  docker-php-ext-enable redis

# Set working directory
WORKDIR /var/www

# Copy custom configurations PHP
COPY docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini


# Copy custom configurations PHP
COPY docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini


RUN echo "memory_limit = 6G" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "upload_max_filesize = 30G" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "post_max_size = 30G" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "max_execution_time = 5800" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "max_input_time = 5800" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "max_file_uploads = 2000" >> /usr/local/etc/php/conf.d/custom.ini




ENV PORT=9873
EXPOSE 9873 9443

USER $user


