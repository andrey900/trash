#! /bin/bash

FILENAME='dekomo.xls'
JSONFILE="dekomo.json"
URL='http://www.dekomo.ru/Cont/ost2xsl.php?'
URL_PARAMS='brand[]=Arte%20Lamp&brand[]=Brilliant&brand[]=Brizzi&brand[]=Chiaro&brand[]=Citilux&brand[]=Collezioni&brand[]=Crystal%20Lamp&brand[]=Crystal%20Lux&brand[]=De%20Markt&brand[]=DIVINARE&brand[]=Eglo&brand[]=Eurosvet&brand[]=Favourite&brand[]=Feron&brand[]=Fumagalli&brand[]=Globo&brand[]=IDLamp&brand[]=Kink%20Light&brand[]=La%20Lampada&brand[]=Lightstar&brand[]=Loft%20It&brand[]=Luce%20Solara&brand[]=Lussole&brand[]=Mantra&brand[]=Maytoni&brand[]=MW-Light&brand[]=MyOne&brand[]=Novotech&brand[]=Odeon%20Light&brand[]=Omnilux&brand[]=Reccagni%20Angelo&brand[]=Regenbogen%20LIFE&brand[]=SilverLight&brand[]=Sonex&brand[]=ST-Luce&brand[]=Toscom&brand[]=33%20%D0%B8%D0%B4%D0%B5%D0%B8&brand[]=Arti%20Lampadari&brand[]=Collezioni&brand[]=Cosmo&brand[]=DIVINARE&brand[]=Lumion&brand[]=MarksLojd&brand[]=Maxell&brand[]=Regenbogen%20LIFE&brand[]=SilverLight&brand[]=Toscom&brand[]=Uniel&brand[]=VELANTE&brand[]=Vitaluce'
URL_PARAMS1='&brand[]=Аврора&brand[]=Дубравия&brand[]=Жар%20Птица&brand[]=Рассвет&brand[]=Точка%20Света&brand[]=Эконом%20Свет'
#URL_PARAMS='brand'
#URL_PARAMS='brand[]=Arte%20Lamp&brand[]=Brilliant&brand[]=Brizzi&brand[]=Chiaro&brand[]=Citilux&brand[]=Crystal%20Lamp&brand[]=Crystal%20Lux&brand[]=De%20Markt&brand[]=Eglo&brand[]=Eurosvet&brand[]=Favourite&brand[]=Feron&brand[]=Globo&brand[]=IDLamp&brand[]=Kink%20Light&brand[]=La%20Lampada&brand[]=Lightstar&brand[]=Luce%20Solara&brand[]=Lussole&brand[]=Mantra&brand[]=Maytoni&brand[]=MW-Light&brand[]=Novotech&brand[]=Odeon%20Light&brand[]=Omnilux&brand[]=Reccagni%20Angelo&brand[]=SilverLight&brand[]=Sonex&brand[]=ST-Luce'
LOG_FILENAME='sync.log'
PAGES_COUNT=1000

cd "/home/bitrix/www/decomo/";
if [ ! -f "$LOG_FILENAME" ];
then
   touch "$LOG_FILENAME"
fi

if [ -f "$FILENAME" ];
then
	rm $FILENAME 2>>$LOG_FILENAME
fi

echo "" >> $LOG_FILENAME
echo "========= Start sync =========" >> $LOG_FILENAME
date +"       %F %R" >> $LOG_FILENAME
echo "==============================" >> $LOG_FILENAME

echo "Check online remote server: $URL" >> $LOG_FILENAME
read serverStatus <<< $(wget --spider -S "$URL" 2>&1 | grep "HTTP/" | awk '{print $2}')

echo "$serverStatus"
echo "Server status: $serverStatus" >> $LOG_FILENAME
if [ "$serverStatus" == '200' ];
then
	echo "Start download file: $FILENAME" >> $LOG_FILENAME
	wget --save-cookies auth.txt --keep-session-cookies --post-data 'login=client&password=ostdecomo' "http://www.dekomo.ru/Cont/ost.php" --delete-after
	wget --load-cookies auth.txt -c "$URL$URL_PARAMS" -O "$FILENAME"
fi

if [ -f "$FILENAME" ];
then
	echo "File: $FILENAME complete download" >> $LOG_FILENAME
	ls -lh "$FILENAME" | awk '{ print "File-name: "$9"; File-size: "$5 }' >> $LOG_FILENAME
	echo "Start parse file to json: $FILENAME" >> $LOG_FILENAME
	awk -F "</*td>|</*tr>" '/<\/*t[rd]>.*[A-Z][A-Z]/ {print "{\"name\": \""$3"\", \"article\": \""$5"\", \"inStock\": \""$7"\", \"remoteStock\": \""$9"\", \"price\": \""$11"\", \"brand\": \""$13"\", \"status\": \""$15"\", \"inUse\": \""$17"\", \"timeClosed\": \""$19"\"}"}' "$FILENAME" > "$JSONFILE"
	echo "End parse file $FILENAME to json $JSONFILE" >> $LOG_FILENAME
	ls -lh "$JSONFILE" | awk '{ print "File-name: "$9"; File-size: "$5 }' >> $LOG_FILENAME
	read lineInJson <<< $(wc -l < $JSONFILE)
	echo "Numbers line in the file $JSONFILE: $lineInJson" >> $LOG_FILENAME
fi
COUNTER="0"

#read cntPages <<< $(echo "scale=2;$lineInJson / $PAGES_COUNT" | bc | awk '{printf("%.2f\n", $0)}' ) 
#echo "scale=1; 400/10*10" | bc | python -c "from math import *; print int(ceil(float(raw_input())))")
read cntPages <<< $(echo "scale=1; $lineInJson/10*10" | bc | python -c "from math import *; print int(ceil(float(raw_input()) / $PAGES_COUNT))")

#echo "$cntPages"

while [ $COUNTER -lt $cntPages ]; do
	#echo "The counter is $COUNTER"
	php -f main.php page=$COUNTER cntInPage=$PAGES_COUNT
	let COUNTER=COUNTER+1 
done

echo "Update currency" >> $LOG_FILENAME

php -f currency.php

echo "Clear old data" >> $LOG_FILENAME
#rm $FILENAME 2>>$LOG_FILENAME
#rm $JSONFILE 2>>$LOG_FILENAME
echo "==============================" >> $LOG_FILENAME
date +"       %F %R" >> $LOG_FILENAME
echo "========== End sync ==========" >> $LOG_FILENAME