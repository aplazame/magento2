FROM magento/magento2devbox-web:latest

RUN echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
 && echo "xdebug.remote_connect_back=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
 && echo "xdebug.default_enable=0" >> /usr/local/etc/php/conf.d/xdebug.ini \
 && echo "xdebug.coverage_enable=0" >> /usr/local/etc/php/conf.d/xdebug.ini
