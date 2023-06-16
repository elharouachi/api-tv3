#!/bin/bash
echo $VHOSTS
if [[ ! -z $VHOSTS ]]; then
  for i in $(echo $VHOSTS |sed "s/,/ /g"); do
    if [[ $i == *":"* ]]; then
      vhost=$(echo $i | cut -f1 -d":")
      conf=$(echo $i | cut -f2 -d":")
      documentroot=$(echo $i | cut -f3 -d":")
      if [[ $conf == *"symfony"* ||  $conf == *"symfony4"*  || $conf == *"drupal"* || $conf == *"evene"* ]]; then
        port=9000
      else
        port=80
    fi
    else
      vhost=$i
      conf=symfony
      port=9000
    fi
    cat > /etc/nginx/conf.d/upstream_$vhost.conf <<-EOF
upstream $vhost-upstream {
        server $vhost:$port;
}
EOF
    cp /etc/nginx/conf.d/$conf.conf /etc/nginx/conf.d/$vhost.conf
    sed -i "s/fastcgi_pass php-upstream/fastcgi_pass $vhost-upstream/g" /etc/nginx/conf.d/$vhost.conf
    sed -i "s/proxy_pass http:\/\/php-upstream/proxy_pass http:\/\/$vhost-upstream/g" /etc/nginx/conf.d/$vhost.conf
    if [[ ! -z $documentroot ]]; then
      sed -i "s#root /var/www/web#root $documentroot#" /etc/nginx/conf.d/$vhost.conf
    else
      sed -i "s#root /var/www/web#root /var/www/$vhost/web/" /etc/nginx/conf.d/$vhost.conf
    fi
  done
  rm /etc/nginx/conf.d/proxy.conf /etc/nginx/conf.d/symfony.conf /etc/nginx/conf.d/symfony4.conf /etc/nginx/conf.d/upstream.conf /etc/nginx/conf.d/drupal.conf /etc/nginx/conf.d/evene.conf 2> /dev/null
else
  rm /etc/nginx/conf.d/nodejs.conf /etc/nginx/conf.d/drupal.conf /etc/nginx/conf.d/proxy.conf /etc/nginx/conf.d/evene.conf 2> /dev/null
fi

if [[ ! -z $LARGEHEADERS ]]; then
  cat /etc/nginx/nginx-large > /etc/nginx/nginx.conf
fi

nginx
