#!/bin/sh
#time 
[ +$1 == + ] && { echo "1st parameter export_profile_ID is missed."; exit 123; }
/usr/bin/php -f /home/bitrix/www/bitrix/php_interface/include/catalog_export/cron_frame.php $1

