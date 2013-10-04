#This script will install and setup WB webserver
#Joe Conley, joe.conley@lairdtech.com

echo "Installing web SCU!"
rm -r /var/www/docs/* 
rm -r /var/www/secure
cp -r *.html /var/www/docs/
cp -r assets/ /var/www/docs/
cp lighttpd.password /etc/lighttpd/
cp php-cgi /usr/bin/
mkdir /etc/lighttpd/conf.d/
cp fastcgi.conf /etc/lighttpd/conf.d/
cp lighttpd.conf /etc/lighttpd/
chmod +x /etc/init.d/S99lighttpd
cd /etc/init.d
./S99lighttpd restart
cd -

echo "Web SCU installed and should be running!"
