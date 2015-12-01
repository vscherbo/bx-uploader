#!/bin/sh

# delete 3,4 fileds with ^ as a delimiter
cut -d^ -f-2,5- ib30-list.csv > import-update.csv

