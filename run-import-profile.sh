#!/bin/sh

[ +$1 == + ] && { echo "1st parameter import_profile_ID is missed."; exit 123; }

# TODO 
# csv-filename must be passed as an parameter according to devmod.impex table

protect_csv() {
# $1 - profile id
# $2 - on/off
CHATTR="sudo /usr/bin/chattr"
if [ +$2 == "+on" ]
then
   CHATTR=${CHATTR}" +i "
else
   CHATTR=${CHATTR}" -i "
fi

case $1 in
  38) CSV_FILE="/home/uploader/upload/import-catalog.csv"
      ;;
  49) CSV_FILE="/home/uploader/upload/import-modificators.csv"
      ;;
  48) CSV_FILE="/home/uploader/upload/import-price.csv"
      ;;
  44) CSV_FILE="/home/uploader/upload/import-catalog-xml.csv"
      ;;
  41) CSV_FILE="/home/uploader/upload/import-modificators-xml.csv"
      ;;
  45) CSV_FILE="/home/uploader/upload/import-price-xml.csv"
      ;;
  35) CSV_FILE="/home/uploader/upload/import-update.csv"
      ;;
   *) echo "ERROR: Unknown profile. Exiting"
      exit 123
esac

[ -e $CSV_FILE ] || touch $CSV_FILE
$CHATTR $CSV_FILE
} # End of protect_csv

protect_csv $1 on
/bin/nice -n19 /usr/bin/ionice -c2 -n7 /usr/bin/php $ARC_PATH/call-import-profile.php $1
protect_csv $1 off

# do not import the same csv one more time
DT=`date +%F_%H_%M_%S`
mv $CSV_FILE $CSV_FILE-$DT

if [ $1 -eq 35 ]
then    
    FIN_INFO_CSV='/home/uploader/upload/fin-info-update-list.csv'
    rm -f $FIN_INFO_CSV
    ln -sf $CSV_FILE-$DT $FIN_INFO_CSV
fi    

