<?php 
define("NO_KEEP_STATISTIC", true); // Ð¾Ñ‚ÐºÐ»ÑŽÑ‡Ð¸Ð¼ ÑÑ‚Ð°Ñ‚Ð¸ÑÑ‚Ð¸ÐºÑƒ
require ($_SERVER ["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_before.php');

require ($_SERVER ["DOCUMENT_ROOT"] . '/bitrix/php_interface/include/iPayFactory.php');
require ($_SERVER ["DOCUMENT_ROOT"] . '/bitrix/php_interface/include/abstractbitrixadapter.php');
require ($_SERVER ["DOCUMENT_ROOT"] . '/bitrix/php_interface/include/interfacefactory.php');
require ($_SERVER ["DOCUMENT_ROOT"] . '/bitrix/php_interface/include/orderfactory.php');
require ($_SERVER ["DOCUMENT_ROOT"] . '/bitrix/php_interface/include/userfactory.php');

//p2f($_REQUEST['XML'], false);
$_REQUEST['XML'] = '<?xml version="1.0" encoding="windows-1251" ?>
<ServiceProvider_Request>
  <DateTime>20151103141216</DateTime>
  <Version>1</Version>
  <RequestType>ServiceInfo</RequestType>
  <PersonalAccount>389</PersonalAccount>
  <Currency>974</Currency>
  <RequestId>39097</RequestId>
  <ServiceInfo>
    <Agent>369</Agent>
  </ServiceInfo>
</ServiceProvider_Request>';
$xml = new SimpleXMLElement($_REQUEST['XML']);
$iPay = new iPayFactory( $xml );
$orderFactory = new OrderFactory();
$order = $orderFactory->factoryMethod( $iPay->getOrderNumber() );
$iPay->setOrder( $order );
$userFactory = new UserFactory();
$iPay->setUser( $userFactory->factoryMethod( $order->userId ) );

$fXML = $iPay->answerServiceInfo('Занесение средств на личный счет на сайте poisk-mastera.by в размере '.$order->price);

echo $fXML;

//md5-хэш от константы конкатенированной с XML-запросом.
//4. Пример работы с ЦП на php

/*echo "<?xml version=\"1.0\" encoding=\"windows-1251\" ?>";
//Заказ номер NNNN не существует. Начните оплату заново на сайте magazin.by
//Заказ номер NNNN уже оплачен
//Заказ номер NNNN находится в процессе оплаты
//Заказ номер NNNN отменен. Начните оплату заново на сайте magazin.by
/*?>
<ServiceProvider_Response>
<ServiceInfo>
<Amount Editable="N" MinAmount="26000" MaxAmount="26000">
<Debt>26000</Debt>
<Penalty/>
</Amount>
<Name Editable="Y">
<Surname>Èâàíîâ</Surname>
<FirstName>Èâàí</FirstName>
<Patronymic>Èâàíîâè÷</Patronymic>
</Name>
<Address Editable="Y">
<City>Ìèíñê</City>
<Street>Ïóøêèíà</Street>
<House>10</House>
<Building>1</Building>
<Apartment>100</Apartment>
</Address>
<Info xml:space="preserve">
<InfoLine>Çàêàç "123", îôîðìëåííûé íà ñàéòå tv.by.</InfoLine>
<InfoLine>Òåëåâèçîð Sumsung 48EU6500. </InfoLine>
<InfoLine>Ñóììà: 9 200 000 áåë. ðóá.</InfoLine>
</Info>
</ServiceInfo>
</ServiceProvider_Response>*/
/*
$xml = new SimpleXMLElement('<xml/>');

for ($i = 1; $i <= 8; ++$i) {
    $track = $xml->addChild('track');
    $track->addChild('path', "song$i.mp3");
    $track->addChild('title', "Track $i - Track Title");
}

Header('Content-type: text/xml');
print($xml->asXML());
*/