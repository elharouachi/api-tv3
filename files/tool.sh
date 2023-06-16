#!/bin/bash
HOST_DOMAIN="host.docker.internal"
ping -q -c1 $HOST_DOMAIN > /dev/null 2>&1
if [ $? -ne 0 ]; then
  HOST_IP=$(ip route | awk 'NR==1 {print $3}')
  echo -e "$HOST_IP\t$HOST_DOMAIN" >> /etc/hosts
fi
if [[ ! -z $UUID ]]; then
 usermod -u $UUID -g www-data www-data
fi

if [[ ! -z $ALIAS ]]; then
 IFS=:
 for i in $(echo $ALIAS); do
   echo "alias $i" >> /home/www-data/.zshrc
 done
 IFS=$' \t\n'
fi

if [ $# -eq 0 ]
  then
    su www-data;exit
else
  env | grep -v 'HOME' > /home/www-data/.profile && chown www-data:www-data /home/www-data/.profile
  su - www-data -c "cd `pwd`; source ~/.profile > /dev/null 2>&1; $*"
fi

