<?
$file = base64_decode($_POST['img']);
if(file_exists($file)){
	if(unlink($file)){
		echo 'удалили';
	}else{
		echo 'удалить не удалось';
	}
}else{
	echo 'файл не найден '.$file;
}
?>