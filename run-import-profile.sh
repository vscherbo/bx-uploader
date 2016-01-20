#!/bin/sh

[ +$1 == + ] && { echo "1st parameter import_profile_ID is missed."; exit 123; }

/bin/nice -n19 /usr/bin/ionice -c2 -n7 /usr/bin/php $ARC_PATH/call-import-profile.php $1

