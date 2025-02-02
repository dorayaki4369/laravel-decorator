FROM php:8.2-fpm

ARG UID=1000
ARG GID=1000
ARG USERNAME=user
ARG GROUPNAME=group

RUN groupadd -g ${GID} ${GROUPNAME} && \
    useradd -m -s /bin/bash -u ${UID} -g ${GID} ${USERNAME}

RUN apt update && apt upgrade -y && apt install -y git
RUN git config --global --add safe.directory /app

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions xdebug @composer

COPY overwrite.ini $PHP_INI_DIR/conf.d/

# PHPStorm coverage
RUN mkdir /opt/phpstorm-coverage && chown $USERNAME:$GROUPNAME /opt/phpstorm-coverage

USER $USERNAME
