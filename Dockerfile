FROM php:7.2-apache

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

SHELL ["/bin/bash", "-o", "pipefail", "-c"]

ARG PROJECT_PATH
RUN : "${PROJECT_PATH:?The 'PROJECT_PATH' variable must be set during build: docker build --build-arg=PROJECT_PATH='/path/to/example-oauth-server'}"

RUN export DEBIAN_FRONTEND=noninteractive       \
  && apt-get update                             \
  && apt-get install -y --no-install-recommends \
        git                                     \
        libzip-dev                              \
        unzip                                   \
        zip                                     \
  && rm -rf /var/lib/apt/lists/*                \
  && a2enmod rewrite                            \
  && a2enmod ssl                                \
  && docker-php-ext-configure zip --with-libzip \
  && docker-php-ext-install zip

WORKDIR /tls
RUN openssl req       \
  -days 365           \
  -keyout server.key  \
  -new                \
  -nodes              \
  -out server.cert    \
  -subj "/C=NL/ST=Overijssel/L=Enschede/O=PDS Interop/OU=IT/CN=pdsinterop.org" \
  -x509

WORKDIR /app
COPY site.conf /etc/apache2/sites-enabled/site.conf

COPY "${PROJECT_PATH}/composer.json" /app/composer.json
RUN composer install --no-dev --prefer-dist

COPY "${PROJECT_PATH}/src/" /app/src
COPY "${PROJECT_PATH}/web/" /app/web
COPY "${PROJECT_PATH}/tests/" /app/tests

EXPOSE 443
