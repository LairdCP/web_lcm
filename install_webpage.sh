#This script will install and setup WB webserver
#Contact Laird Wireless Support: ews.support@lairdtech.com

echo "Installing Web LCU!"

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
[ -e php-cgi ] && PHPCGI=0 || PHPCGI=1
if [ $PHP == 1 ];
then
	if [ $PHPCGI == 0 ];
	then
		cp php-cgi /usr/bin/
		chmod +x /usr/bin/php-cgi
	else
		echo "php-cgi file not found, cant install correctly"
	fi
fi

[ -d /etc/lighttpd/conf.d ] && Confd=0 || Confd=1
if [ $Confd == 1 ];
then
	mkdir /etc/lighttpd/conf.d/
fi

cp *.html /var/www/docs/

[ -e status.html ] && Statushtml=0 || Statushtml=1
if [ $Statushtml == 0 ];
then
	cp status.html /var/www/docs/
else
	echo "status.html file not found, cant install correctly"
fi

[ -e profileconfig.html ] && Profilehtml=0 || Profilehtml=1
if [ $Profilehtml == 0 ];
then
	cp profileconfig.html /var/www/docs/
else
	echo "profileconfig.html file not found, cant install correctly"
fi

[ -e globalconfig.html ] && Globalhtml=0 || Globalhtml=1
if [ $Globalhtml == 0 ];
then
	cp globalconfig.html /var/www/docs/
else
	echo "globalconfig.html file not found, cant install correctly"
fi

[ -e advancedconfig.html ] && Advancedhtml=0 || Advancedhtml=1
if [ $Advancedhtml == 0 ];
then
	cp advancedconfig.html /var/www/docs/
else
	echo "advancedconfig.html file not found, cant install correctly"
fi

[ -e about.html ] && Abouthtml=0 || Abouthtml=1
if [ $Abouthtml == 0 ];
then
	cp about.html /var/www/docs/
else
	echo "about.html file not found, cant install correctly"
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
	echo "lighttpd already running, no need to start..."
else
	chmod +x /etc/init.d/S99lighttpd
	/etc/init.d/S99lighttpd start
fi

if [ $PHPCGI == 1 ] || [ $Statushtml == 1 ] || [ $Profilehtml == 1 ] || [ $Globalhtml == 1 ] || [ $Abouthtml == 1 ] || [ $Assets == 1 ] || [ $LightPass == 1 ] || [ $Advancedhtml == 1 ] || [ $Lightconf == 1 ];
then
	echo "Web LCU will not function as intended, file(s) missing!"
else
	echo "Web LCU installed and should be running!"
fi