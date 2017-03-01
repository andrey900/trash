<?php

function isAuth( $data ){
  return preg_match('#<form[^>]+class="loginOst"#Usi',$data);
}

$ch = curl_init();
$url = 'http://www.dekomo.ru/Cont/ost.php';
curl_setopt($ch, CURLOPT_URL, $url ); // отправляем на
curl_setopt($ch, CURLOPT_HEADER, 0); // пустые заголовки
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // возвратить то что вернул сервер
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // следовать за редиректами
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);// таймаут4
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// просто отключаем проверку сертификата
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__).'/cookie.txt'); // сохранять куки в файл
curl_setopt($ch, CURLOPT_COOKIEFILE,  dirname(__FILE__).'/cookie.txt');
curl_setopt($ch, CURLOPT_POST, 1); // использовать данные в post
curl_setopt($ch, CURLOPT_POSTFIELDS, array(
  'lg'=>'Войти',
  'login'=>'client',
  'password'=>'ostdecomo',
  'remember'=>"on",
));
echo isAuth($data = curl_exec($ch))?'Failed':'Success';
curl_close($ch);