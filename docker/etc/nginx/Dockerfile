FROM ubuntu:22.04

LABEL Author="Yusuf Doğan <yusufdogan21@gmail.com>"

ARG DEBIAN_FRONTEND=noninteractive
RUN apt-get update
RUN apt list --upgradable
RUN apt-get update

#Installing the Nginx Web Server
RUN apt-get install nginx -y
RUN apt-get install apt-utils -y
RUN apt-get install git -y
RUN apt-get install curl -y
RUN apt-get install nano -y
RUN apt-get install mysql-client -y
RUN apt-get install openssl -y
RUN apt-get update

RUN apt-get update && apt-get install -y \
    php8.1-fpm \
    php8.1-cli \
    php8.1-curl \
    php8.1-gd \
    php8.1-ldap \
    php8.1-mbstring \
    php8.1-mysql \
    php8.1-soap \
    php8.1-sqlite3 \
    php8.1-xml \
    php8.1-zip \
    php8.1-intl

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Nginx config
RUN rm /etc/nginx/sites-enabled/default
ADD default.conf /etc/nginx/sites-available/default.conf
RUN ln -s /etc/nginx/sites-available/default.conf /etc/nginx/sites-enabled/default.conf
RUN sed -i "s/user  nginx;/user  www-data;/g" /etc/nginx/nginx.conf

# Expose ports.
EXPOSE 80
#EXPOSE 443
WORKDIR /var/www/rub

CMD service php8.1-fpm start && nginx -g "daemon off;"
