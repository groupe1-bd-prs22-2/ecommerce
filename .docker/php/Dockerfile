FROM php:8.1.3-fpm

# FIX : Création d'un utilisateur avec les mêmes ID que l'utilisateur hôte WSL
# Permet de régler les problèmes de permission pour l'écriture dans certains répertoires (Upload d'images produits/articles...)
# Arguments correspondant aux Ids de l'utilisateur hôte
ARG USER_ID
ARG GROUP_ID
ARG USERNAME

# Création du nouveau groupe / user
RUN groupadd -f -g $GROUP_ID $USERNAME
RUN useradd -u $USER_ID -d /home/$USERNAME -g $GROUP_ID $USERNAME

RUN mkdir /home/$USERNAME
RUN chown -R $USERNAME:$USERNAME /home/$USERNAME

# Installation d'outils divers
RUN apt update
RUN apt install -y --no-install-recommends locales apt-utils libicu-dev g++ libpng-dev libxml2-dev libzip-dev libonig-dev libxslt-dev;

RUN docker-php-ext-configure intl
RUN docker-php-ext-install gd opcache intl zip calendar dom mbstring xsl
RUN pecl install apcu && docker-php-ext-enable apcu

# Installation des composants PostgreSQL
RUN apt install -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Installation de Composer et Symfony
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash
RUN apt install -y symfony-cli

USER $USERNAME
WORKDIR /var/www/ecommerce

CMD ["php-fpm"]
EXPOSE 9000
