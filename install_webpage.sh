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
fi

cp lighttpd.password /etc/lighttpd/

[ -x /usr/bin/php-cgi ] && PHP=0 || PHP=1
if [ $PHP == 1 ];
then
	cp php-cgi /usr/bin/
fi

[ -d /etc/lighttpd/conf.d ] && Confd=0 || Confd=1
if [ $Confd == 1 ];
then
	mkdir /etc/lighttpd/conf.d/
fi

cp *.html /var/www/docs/
cp fastcgi.conf /etc/lighttpd/conf.d/
cp lighttpd.conf /etc/lighttpd/
chmod +x /etc/init.d/S99lighttpd

WebRun=`pidof lighttpd`
if [ $WebRun == "" ];
then
	cd /etc/init.d
	./S99lighttpd restart
	cd -
fi

echo "Web SCU installed and should be running!"
