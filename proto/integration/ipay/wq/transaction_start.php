<?php
define("NO_KEEP_STATISTIC", true); // отключим статистику
require ($_SERVER ["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_before.php');

p2f($_REQUEST, false);

header('Content-Type: text/html; charset=windows-1251'); 

echo "<?xml version=\"1.0\" encoding=\"windows-1251\" ?>";
?>
<ServiceProvider_Response>
<TransactionStart>
<ServiceProvider_TrxId>8571502</ServiceProvider_TrxId>
</TransactionStart>
<Info xml:space="preserve">
<InfoLine>Заказ "123", оформленный на сайте tv.by.</InfoLine>
<InfoLine>Телевизор Sumsung 48EU6500. </InfoLine>
<InfoLine>Сумма: 9 200 000 бел. руб.</InfoLine>
</Info>
</ServiceProvider_Response>