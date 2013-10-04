#This script will install and setup WB webserver
#Joe Conley, joe.conley@lairdtech.com

rm -r /var/www/docs/*
rm -r /var/www/secure
mkdir /var/www/docs/pics
cp -r *.html /var/www/docs/
cp laird_logo.png /var/www/docs/pics/
cp lighttpd.password /etc/lighttpd/
cp php-cgi /usr/bin
mkdir /etc/lighttpd/conf.d
cp fastcgi.conf /etc/lighttpd/conf.d
cp lighttpd.conf /etc/lighttpd/
chmod +x /etc/init.d/S99lighttpd
cd /etc/init.d
./S99Lighttpd start
cd -
