#!/bin/bash

# $: ./u_add.sh file_name.json

LOG_FILENAME='sync.log'
PAGES_COUNT=1000

if [ ! -f "$LOG_FILENAME" ];
then
   touch "$LOG_FILENAME"
fi

echo "" >> $LOG_FILENAME
echo "========= Start user sync =========" >> $LOG_FILENAME
date +"       %F %R" >> $LOG_FILENAME
echo "==============================" >> $LOG_FILENAME

if [ -f "$1" ];
then
	echo "User file fined: $1" >> $LOG_FILENAME
	ls -lh "$1" | awk '{ print "File-name: "$9"; File-size: "$5 }' >> $LOG_FILENAME
	read lineInJson <<< $(wc -l < $1)

	COUNTER="0"
	read cntPages <<< $(echo "scale=1; $lineInJson/10*10" | bc | python -c "from math import *; print int(ceil(float(raw_input()) / $PAGES_COUNT))")

	while [ $COUNTER -lt $cntPages ]; do
		#echo "The counter is $COUNTER"
		php -f main.php page=$COUNTER cntInPage=$PAGES_COUNT fileName=$1
		let COUNTER=COUNTER+1 
	done

	echo "==============================" >> $LOG_FILENAME
	date +"       %F %R" >> $LOG_FILENAME
	echo "========== End sync ==========" >> $LOG_FILENAME
fi