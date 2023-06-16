#!/bin/bash
if [[ ! -z $UUID ]]; then
 usermod -u $UUID -g www-data www-data
fi
php5-fpm -F
