<?/*
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
header("Content-type: application/vnd.ms-word");
header("Content-Disposition: attachment;Filename=ComersialOffer.doc");

CModule::IncludeModule("iblock");
CModule::IncludeModule("sale");

if( !$GLOBALS['USER']->isAuthorized() )
	die;

if( (int)$_GET['ORDER_ID']<=0 ){
	if( (int)$_GET['BASKET_ID']<=0 ){
		die;
	}
}

include("/includes/docphp/PHPWord.php") ;

global $arResult;
$arUser = C1User::GetByID($GLOBALS['USER']->GetID())->Fetch();
$arResult['USER_NAME'] = $arUser['LAST_NAME'].' '.$arUser['NAME'];
$arResult['USER_PHONE'] = $arUser['PERSONAL_PHONE'];
$arResult['USER_EMAIL'] = $arUser['EMAIL'];

if( (int)$_GET['BASKET_ID']>0 )
	$dbBasketItems = CSaleBasket::GetList(array(), array("ID" => (int)$_GET['BASKET_ID']));
elseif((int)$_GET['ORDER_ID']>0)
	$dbBasketItems = CSaleBasket::GetList(array(), array("ORDER_ID" => (int)$_GET['ORDER_ID'])); // ID заказа

$arResult['BASKET'] = array();
while ($arItems = $dbBasketItems->Fetch()){
	$arElemInfo = CIBlockExt::GetElementInfo($arItems['PRODUCT_ID']);
	$arResult['BASKET'][] = array('NAME' => $arItems['NAME'], 
								'PREVIEW_TEXT' => $arElemInfo['PREVIEW_TEXT'],
								'QUANTITY' => $arItems['QUANTITY'],
								'BASE_PRICE' => $arItems['PRICE']
								);
}


CMain::IncludeFile( '/includes/_kp_doc.tpl', array() );*/



require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
//header("Content-type: application/vnd.ms-word");
//header("Content-Disposition: attachment;Filename=ComersialOffer.doc");

CModule::IncludeModule("iblock");
CModule::IncludeModule("sale");

if( !$GLOBALS['USER']->isAuthorized() )
    die;

if( (int)$_GET['ORDER_ID']<=0 ){
    if( (int)$_GET['BASKET_ID']<=0 ){
        die;
    }
}

include("includes/docphp/PHPWord.php") ;

global $arResult;
$arUser = CUser::GetByID($GLOBALS['USER']->GetID())->Fetch();
$arResult['USER_NAME'] = $arUser['LAST_NAME'].' '.$arUser['NAME'];
$arResult['USER_PHONE'] = $arUser['PERSONAL_PHONE'];
$arResult['USER_EMAIL'] = $arUser['EMAIL'];

if( (int)$_GET['BASKET_ID']>0 )
    $dbBasketItems = CSaleBasket::GetList(array(), array("ID" => (int)$_GET['BASKET_ID']));
elseif((int)$_GET['ORDER_ID']>0)
    $dbBasketItems = CSaleBasket::GetList(array(), array("ORDER_ID" => (int)$_GET['ORDER_ID'])); // ID заказа

$arResult['BASKET'] = array();
while ($arItems = $dbBasketItems->Fetch()){
    $arElemInfo = CIBlockExt::GetElementInfo($arItems['PRODUCT_ID']);

    $res = CIBlockElement::GetByID($arElemInfo['ID'])->GetNext();

    $pic = CFile::ResizeImageGet($res['PREVIEW_PICTURE'], array('width'=>150, 'height'=>150), BX_RESIZE_IMAGE_PROPORTIONAL, true);

    $arResult['BASKET'][] = array('NAME' => $arItems['NAME'],
        'PREVIEW_TEXT' => $arElemInfo['PREVIEW_TEXT'],
        'QUANTITY' => $arItems['QUANTITY'],
        'BASE_PRICE' => $arItems['PRICE'],
        'PICTURE' => $pic
    );
}


// New Word Document
$PHPWord = new PHPWord();

$PHPWord->setDefaultFontName('Tahoma');
$PHPWord->setDefaultFontSize(12);
$properties = $PHPWord->getProperties();
$properties->setTitle('Коммерческое предложение');

// New portrait section
$section = $PHPWord->createSection();
//$sectionStyle = $section->getSettings();
//$sectionStyle->setLandscape();
//$sectionStyle->setPortrait();
/*$sectionStyle->setMarginLeft(300);
$sectionStyle->setMarginRight(900);
$sectionStyle->setMarginTop(900);*/
//$sectionStyle->setMarginBottom(900);

$PHPWord->addFontStyle('CenterText', array('color'=>'000000', 'size'=>18, 'align'=>'center'));
$PHPWord->addFontStyle('LeftText', array('color'=>'000000', 'size'=>14, 'align'=>'left'));
$PHPWord->addFontStyle('RightText', array('color'=>'000000', 'size'=>14, 'align'=>'right', 'underline'=>PHPWord_Style_Font::UNDERLINE_SINGLE));
$PHPWord->addParagraphStyle('pStyle', array('align'=>'center', 'spaceAfter'=>100));
$PHPWord->addParagraphStyle('pStyleR', array('align'=>'right', 'spaceAfter'=>100));


// Add header
$header = $section->createHeader();
//$header->setMarginLeft(floor(15*56.7));

$header->addImage('includes/docphp/pic/header.png', array('width'=>700, 'height'=>200, 'align'=>'center'));

// Write some text
$section->addTextBreak(2);


$section->addText('КОММЕРЧЕСКОЕ ПРЕДЛОЖЕНИЕ', 'CenterText', 'pStyle');

$section->addTextBreak();
$section->addText('Дата создания: '.date("d.m.Y"), 'LeftText');
$section->addTextBreak();
$section->addText('Мебель производства, серия ЭПМ (алюминиевый каркас, наполнение химостойкий пластик)', 'LeftText');
$section->addTextBreak();
$section->addText('Каркас из анодированного алюминиевого профиля обеспечивает прочность и устойчивость конструкции, защищает от сколов материал поверхностей, не ржавеет при попадании влаги, устойчив к химическому воздействию.
Горизонтальные и вертикальные плоскости изготовлены из химически стойкого пластика(6мм)
В отличие от древосодержащих материалов, пластик не разбухает, не крошится, не теряет форму со временем, устойчив к воздействию  агрессивных реагентов, в т.ч. и длительному.
', 'LeftText');
$section->addTextBreak();


// Add table
$tableStyle = array(
    'width' => 100,
    'borderColor' => '000000',
    'borderSize' => 6,
    'cellMargin' => 50
);
$firstRowStyle = array('bgColor' => 'FFFFFF');
$PHPWord->addTableStyle('myTable', $tableStyle, $firstRowStyle);
$table = $section->addTable('myTable');

$table->addRow(1000);
$table->addCell(1000)->addText('№');
$table->addCell(20000)->addText('Наименование');
$table->addCell(10000)->addText('Изображение');
$table->addCell(4000)->addText('Кол-во');
$table->addCell(6000)->addText('Цена за ед., с НДС');
$table->addCell(8000)->addText('Всего, с НДС');

$sum = 0;
foreach ($arResult['BASKET'] as $k=>$val) {
    // Add row
    $table->addRow();
    // Add Cell
    $table->addCell(1000)->addText($k+1);
    $table->addCell(20000)->addText($val['NAME']);
    $table->addCell(10000)->addImage(substr($val['PICTURE']['src'],1), array('width'=>150, 'height'=>150, 'align'=>'center'));
    $table->addCell(4000)->addText((int)$val['QUANTITY']);
    $table->addCell(6000)->addText($val['BASE_PRICE']);
    $table->addCell(8000)->addText(CurrencyFormat($val['QUANTITY']*$val['BASE_PRICE'], 'RUB'));

    $sum += $val['QUANTITY']*$val['BASE_PRICE'];
}
//$section->addTextBreak();
$section->addText('ВСЕГО:'.$sum.', в т.ч. НДС 18%', 'RightText', 'pStyleR');
//$section->addTextBreak();
$section->addText('Срок производства 15 дней. Гарантия 12 месяцев. Имеется сертификат качества.', 'RightText');
//$section->addTextBreak();
$section->addText('С Вами  работает '.$arResult['USER_NAME'], 'RightText');
//$section->addTextBreak(1);
$section->addText('Группа компаний «Профлаб», отдел оптовых продаж', 'LeftText');
//$section->addTextBreak(1);
$section->addText('(812) 336-51-71 '.$arResult['USER_PHONE'].', 8911-926-74-24', 'LeftText');
//$section->addTextBreak(1);
$section->addText($arResult['USER_EMAIL'].', www.proflabspb.ru,  www.epmlab.ru', 'LeftText');


/*new dBug($arResult,'',true);
new dBug($_SERVER['SERVER_NAME'],'',true);*/



// Save File
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="document.docx"');
header('Cache-Control: max-age=0');
$objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
$objWriter->save('php://output');



