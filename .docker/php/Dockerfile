FROM php:fpm-alpine

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Installation Composant DB
#RUN docker-php-ext-install pdo_mysql #MySQL

# POSTGRESQL (Migration du 06/07 pour déploiement Heroku)
RUN apk add postgresql-dev
RUN docker-php-ext-install pdo_pgsql

# Install intl
RUN apk add icu-dev
RUN docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-enable intl

# Install Symfony cli
RUN apk update && apk add bash
RUN wget https://get.symfony.com/cli/installer -O - | bash && mv /root/.symfony/bin/symfony /usr/local/bin/symfony
RUN apk add --update nodejs npm
RUN npm install --global yarn

WORKDIR /var/www/ecommerce

CMD ["php-fpm"]
EXPOSE 9000
