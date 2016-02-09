#!/bin/sh

[ +$1 == + ] && { echo "1st parameter import_profile_ID is missed."; exit 123; }

# fast patch
# csv-filename must be passed as parameter according to devmod.impex table

protect_csv() {
# $1 - profile id
# $2 - on/off
CHATTR="sudo /usr/bin/chattr -V"
if [ +$2 == "+on" ]
then
   CHATTR=${CHATTR}" +i "
else
   CHATTR=${CHATTR}" -i "
fi

case $1 in
  38) $CHATTR "/home/uploader/upload/import-catalog.csv"
      ;;
  49) $CHATTR "/home/uploader/upload/import-modificators.csv"
      ;;
  48) $CHATTR "/home/uploader/upload/import-price.csv"
      ;;
  44) $CHATTR "/home/uploader/upload/import-catalog-xml.csv"
      ;;
  41) $CHATTR "/home/uploader/upload/import-modificators-xml.csv"
      ;;
  45) $CHATTR "/home/uploader/upload/import-price-xml.csv"
      ;;
   *) echo "ERROR: Unknown profile. Exiting"
      exit 123
esac
}

protect_csv $1 on
/bin/nice -n19 /usr/bin/ionice -c2 -n7 /usr/bin/php $ARC_PATH/call-import-profile.php $1
protect_csv $1 off

