#!/bin/bash

web_port=$(docker-compose port web 80)
web_port=${web_port#*:}
varnish_port=$(docker-compose port varnish 6081)
varnish_port=${varnish_port#*:}

echo 'Build docker images'
docker-compose up --build -d

echo 'Install Magento'
docker-compose exec -d --user magento2 web /home/magento2/scripts/m2init magento:install --no-interaction --magento-host=magento2.aplazame
docker-compose exec -d --user magento2 web mkdir -p /var/www/magento2/app/code/Aplazame
docker-compose exec -d --user magento2 web ln -s /aplazame /var/www/magento2/app/code/Aplazame/Payment
docker-compose exec -d --user magento2 web ln -s /aplazame /var/www/magento2/aplazame
docker-compose exec -d --user magento2 web composer require aplazame/aplazame-api-sdk
