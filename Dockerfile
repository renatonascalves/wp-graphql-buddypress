############################################################################
# Container for running Codeception tests on a WPGraphQL BuddyPress Docker instance. #
############################################################################

# Using the 'DESIRED_' prefix to avoid confusion with environment variables of the same name.
ARG DESIRED_WP_VERSION
ARG DESIRED_PHP_VERSION

FROM renatonascalves/bp-graphql-app:wp${DESIRED_WP_VERSION}-php${DESIRED_PHP_VERSION}

LABEL author=renatonascalves
LABEL author_uri=https://github.com/renatonascalves

SHELL [ "/bin/bash", "-c" ]

ARG DESIRED_WP_VERSION
ARG DESIRED_PHP_VERSION

# Install php extensions
RUN docker-php-ext-install pdo_mysql

# Install Xdebug
RUN if [ "$DESIRED_PHP_VERSION" == "5.6" ]; then yes | pecl install xdebug-2.5.5; else yes | pecl install xdebug;  fi \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/xdebug.ini

# Install composer
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN curl -sS https://getcomposer.org/installer | php -- \
    --filename=composer \
    --install-dir=/usr/local/bin

# Add composer global binaries to PATH
ENV PATH "$PATH:~/.composer/vendor/bin"

# Configure php
RUN echo "date.timezone = UTC" >> /usr/local/etc/php/php.ini

# Remove exec statement from base entrypoint script.
RUN sed -i '$d' /usr/local/bin/app-entrypoint.sh

# Set up entrypoint
WORKDIR    /var/www/html/wp-content/plugins/wp-graphql-buddypress
COPY       bin/testing-entrypoint.sh /usr/local/bin/testing-entrypoint.sh
RUN        chmod 755 /usr/local/bin/testing-entrypoint.sh
ENTRYPOINT ["testing-entrypoint.sh"]