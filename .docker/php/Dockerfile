FROM php:fpm-alpine

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PDO
RUN docker-php-ext-install pdo_mysql

# Install Symfony cli
RUN apk update && apk add bash
RUN wget https://get.symfony.com/cli/installer -O - | bash && mv /root/.symfony/bin/symfony /usr/local/bin/symfony
RUN apk add --update nodejs npm
RUN npm install --global yarn

WORKDIR /var/www/ecommerce

CMD ["php-fpm"]
EXPOSE 9000
