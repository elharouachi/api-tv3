FROM alpine:3.13

RUN apk add tzdata && cp /usr/share/zoneinfo/Europe/Paris /etc/localtime && echo "Europe/Paris" >  /etc/timezone && apk del tzdata

RUN apk update  --no-cache
RUN apk --update --no-cache add \
	php7 \
	php7-dev \
	php7-common \
	php7-mysqli \
	php7-apcu \
	php7-gd \
	php7-curl \
    autoconf \
	php7-amqp \
	php7-fileinfo \
	php7-json \
    curl \
	php7-phar \
	php7-intl \
	php7-openssl \
	php7-session \
	php7-mysqli \
	php7-memcached \
	php7-xml \
	php7-bcmath \
	php7-apcu \
	php7-zlib \
	php7-iconv \
	php7-pdo \
	php7-pdo_mysql \
	php7-dom \
	php7-xdebug \
	php7-ctype \
	php7-sockets \
	php7-calendar \
	php7-ftp \
	php7-gd \
	php7-simplexml \
	php7-xmlrpc \
	php7-xmlwriter \
	php7-mcrypt \
 	php7-pcntl \
	php7-tokenizer \
	php7-redis \
        php7-dev \
        php7-pear \
        php7-ctype \
        php7-iconv \
	php7-soap \
        php7-sqlite3 \
        php7-pdo_sqlite \
	php7-ldap \
        php7-imagick \
        php7-sysvsem \
	php7-posix \
	php7-xsl \
        php7-sodium \
	sudo \
	mysql-client \
	mini-sendmail \
	zsh \
	git \
	bash \
	shadow \
	make \
        openssh-client \
        gnupg \
        openssl \
	php7-dev \
        php7-pear \
	gpgme \
	gpgme-dev \
        php7-zip \
	sqlite \
        build-base \
        perl-image-exiftool \
        exiftool \
	parallel \
#        libc6-compat \
#        libgcc \
#        gcc g++ linux-headers libstdc++ \
#        nss \
#        nspr \
#        xvfb \
        chromium-chromedriver \
	chromium \
        && pecl install gnupg \
        && apk del php-dev

RUN apk --no-cache add php7-mbstring php7-iconv

RUN addgroup -g 1000 www-data && adduser -u 1000 -G www-data -D www-data -s /bin/zsh

RUN usermod -d /home/www-data www-data
RUN sudo -u www-data bash -cx "$(curl -fsSL https://raw.githubusercontent.com/robbyrussell/oh-my-zsh/master/tools/install.sh)"
RUN curl -sS https://getcomposer.org/installer -o composer-install.php && php composer-install.php --version=1.10.5
RUN curl -sS https://getcomposer.org/composer-2.phar -o composer-2.phar
RUN mv composer.phar /usr/bin/composer1 && mv composer-2.phar /usr/bin/composer && ln -s /usr/bin/composer /usr/bin/composer2
RUN chmod +x /usr/bin/composer && chmod +x /usr/bin/composer1

ADD files/zshrc /home/www-data/.zshrc
ADD files/tool.sh /usr/bin/tool.sh
RUN chown -R www-data:www-data /home/www-data
RUN sed -i 's/memory_limit = .*/memory_limit = 2048M/' /etc/php7/php.ini
RUN sed -i 's/;date.timezone =/date.timezone = Europe\/Paris/g' /etc/php7/php.ini
RUN echo "extension=gnupg.so" >> /etc/php7/conf.d/gnupg.ini
RUN passwd --delete www-data
COPY files/xdebug.ini /etc/php7/conf.d/00_xdebug.ini
WORKDIR /var/www
ENTRYPOINT ["/usr/bin/tool.sh"]
