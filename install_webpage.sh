#This script will install and setup WB webserver
#Joe Conley, joe.conley@lairdtech.com

echo "Installing web SCU!"

[ -d /var/www/docs ] && Docs=0 || Docs=1
if [ $Docs=0 ];
then
	rm -r /var/www/docs/*
else
	mkdir /var/www/docs/
fi


[ -d /var/www/secure ] && Secure=0 || Secure=1
if [ $Secure == 0 ];
then
	rm -r /var/www/secure
fi

[ -d assets/ ] && Assets=0 || Assets=1
if [ $Assets == 0 ];
then
	cp -r assets/ /var/www/docs/
else
	echo "assets/ directory not found, cant install correctly"
fi

[ -e lighttpd.password ] && LightPass=0 || LightPass=1
if [ $LightPass == 0 ];
then
	cp lighttpd.password /etc/lighttpd/
else
	echo "lighttpd.password not found, cant install correctly"
fi

[ -x /usr/bin/php-cgi ] && PHP=0 || PHP=1
if [ $PHP == 1 ];
then
	cp php-cgi /usr/bin/
	chmod +x /usr/bin/php-cgi
fi

[ -d /etc/lighttpd/conf.d ] && Confd=0 || Confd=1
if [ $Confd == 1 ];
then
	mkdir /etc/lighttpd/conf.d/
fi

cp *.html /var/www/docs/

[ -e fastcgi.conf ] && Fastcgi=0 || Fastcgi=1
if [ $Fastcgi == 0 ];
then
	cp fastcgi.conf /etc/lighttpd/conf.d/
else
	echo "fastcgi.conf not found, cant install correctly"
fi

[ -e lighttpd.conf ] && Lightconf=0 || Lightconf=1
if [ $Lightconf == 0 ];
then
	cp lighttpd.conf /etc/lighttpd/
else
	echo "lighttpd.conf not found, cant install correctly"
fi 

if ps aux | grep "[p]hp-cgi" > /dev/null
then
	echo "lighttpd already running..."
else
	chmod +x /etc/init.d/S99lighttpd
	/etc/init.d/S99lighttpd start
fi

echo "Web SCU installed and should be running!"
