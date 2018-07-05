FROM magento/magento2devbox-web:latest

ARG XDEBUG_REMOTE_OPTION=xdebug.remote_connect_back=on

RUN echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
 && echo $XDEBUG_REMOTE_OPTION >> /usr/local/etc/php/conf.d/xdebug.ini \
 && echo "xdebug.default_enable=0" >> /usr/local/etc/php/conf.d/xdebug.ini \
 && echo "xdebug.coverage_enable=0" >> /usr/local/etc/php/conf.d/xdebug.ini
