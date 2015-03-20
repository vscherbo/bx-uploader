#!/bin/sh
#time 
[ +$1 == + ] && { echo "1st parameter import_profile_ID is missed."; exit 123; }
/usr/bin/php -f /home/bitrix/www/bitrix/php_interface/include/catalog_import/cron_frame.php $1

