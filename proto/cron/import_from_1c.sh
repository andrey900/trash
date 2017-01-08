#!/bin/bash
##########################################################################################
# Принцип работы скрипта: проверяем есть ли zip-архивы в EXCHANGE_DIR. Если тестирование
# unzip -t возвращает код 0, то переносим архив в /upload/1c_catalog/spool/
# Обработку архивов, расположенных
#########################################################################################

# Инициализируем переменную для абсолютного пути к скрипту
SCRIPT_DIR=$(dirname $0)

if [ $SCRIPT_DIR = "." ]; then
	SCRIPT_DIR=`pwd`
fi

# Полное имя скрипта
SCRIPT_NAME=`basename $0`
# Имя скрипта без расширения
FILE_NAME=`echo "$SCRIPT_NAME" | cut -d'.' -f1`
# PID-файл php-скрипта
FILE_PID=$SCRIPT_DIR/$FILE_NAME.pid
# LOG-файл
FILE_LOG=$SCRIPT_DIR/$FILE_NAME.log
# PHP-скрипт, выполняющий импорт
PHP_SCRIPT=$SCRIPT_DIR/$FILE_NAME.php
# время жизни lock-файла в минутах
TTL_LOCK=60 
FINDS="find $FILE_PID -type f -mmin +$TTL_LOCK"
# предполагается, что архивы для обмена с 1С располагаются в папке /upload/1c_catalog/ и
# сам скрипт находится в каталоге 1-го уровня по отношению к корню сайта
EXCHANGE_DIR=$SCRIPT_DIR/../upload/1c_catalog
# каталог для архивов -- архивы накапливаются в течении суток
CURRENT_DATE=`date +%Y%m%d`
ARCHIVE_DIR=$EXCHANGE_DIR/archive/$CURRENT_DATE

if [ -f $FILE_PID ]; then
	echo "[bash] Обнаружен PID-файл." >> $FILE_LOG
	IN_PROCESS=true
	for RESULT_FIND in `$FINDS`;
	do
		# если файл LOCK_PID старше TTL_LOCK минут, то удаляем процесс, PID которого указан
		# в $FILE_PID и запускаем скрипт заново
		echo "[bash] Файл $FILE_PID старше $TTL_LOCK минут." 
		echo "[bash] Удаляем и запускаем заново скрипт $PHP_SCRIPT" >> $FILE_LOG
		kill -9 `cat $FILE_PID`
		rm -rf $FILE_PID
		IN_PROCESS=false
	done
	if $IN_PROCESS; then
		echo "[bash] Скрипт $PHP_SCRIPT выполняется и время жизни PID-файла ещё не превысило установленный лимит в $TTL_LOCK минут" >> $FILE_LOG
		exit 0
	fi
fi

# такая хитрая конструкция нужна, т.к. в for не удаётся избежать проблем, если не обнаружен файл по маске
ls $EXCHANGE_DIR/*zip &> /dev/null
RETVAL=$?

if [ "$RETVAL" -ne "0" ]; then
	exit $RETVAL
fi

for FILE_ZIP in `ls -1 $EXCHANGE_DIR/*zip`;
do
	echo "[bash] `date`" >> $FILE_LOG
	echo "[bash] Обнаружен архив [$FILE_ZIP] в каталоге для обмена данными." >> $FILE_LOG
	echo "[bash] Тестируем архив на целостность." >> $FILE_LOG 
	
	unzip -t $FILE_ZIP >/dev/null 2>&1
	
	RETVAL=$?
	
	if [ "$RETVAL" = 0 ]
	then
		echo "[bash] Целостность архива подтверждена. Переходим к обработке." >> $FILE_LOG
		break 
	else
		echo "[bash] Архив повреждён или неполон. Проверяем производится ли его загрузка в настоящий момент." >> $FILE_LOG
 	 	FILESIZE_BEFORE=$(stat -c%s "$FILE_ZIP")
  		sleep 5
  		FILESIZE_AFTER=$(stat -c%s "$FILE_ZIP")
  		
 	 	if [ $FILESIZE_BEFORE -eq $FILESIZE_AFTER ]; then
    		echo "[bash] Загрузка файла не производится. Архив повреждён. Удаляем его" >> $FILE_LOG
    		rm -f $FILE_ZIP  
  		else
    		echo "[bash] Файл $FILE_ZIP не загружен полностью, переходим к обработке следующего файла" >> $FILE_LOG
  		fi
	fi
done

# если FILE_PID не существует, и существует архив $FILE_ZIP, то запусаем php-скрипт
if [ ! -f "$FILE_PID" ] && [ -f "$FILE_ZIP" ]; then
	echo "[bash] Удаляем xml-файлы." >> $FILE_LOG
    rm -f $EXCHANGE_DIR/*.xml
    
	echo "[bash] Распаковываем архив." >> $FILE_LOG
    unzip -o $FILE_ZIP -d $EXCHANGE_DIR/ >/dev/null 2>&1
    
    echo "[bash] Переносим файл $FILE_ZIP в архив $ARCHIVE_DIR" >> $FILE_LOG
    
    mkdir -p $ARCHIVE_DIR
    chmod 0777 $ARCHIVE_DIR
    mv -f $FILE_ZIP $ARCHIVE_DIR

    echo "[bash] Запускаем скрипт $PHP_SCRIPT" >> $FILE_LOG
    /usr/bin/php $PHP_SCRIPT >/dev/null 2>&1 &
    PID=$!
    echo $PID > $FILE_PID
    chmod 666 $FILE_PID
fi
