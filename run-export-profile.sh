#!/bin/sh
#time 
[ +$1 == + ] && { echo "1st parameter export_profile_ID is missed."; exit 123; }
/bin/nice -n19 /usr/bin/ionice -c2 -n7 /usr/bin/php /home/bitrix/www/bitrix/php_interface/include/catalog_export/cron_frame.php $1


