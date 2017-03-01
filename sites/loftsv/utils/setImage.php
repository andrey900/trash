<?
define("STOP_STATISTICS", true);
define('NO_AGENT_CHECK', true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Studio8\Main\Helpers;

$arImages = [
'4395' => 'lf_loft1029w-2.jpg',
'4382' => 'lf_loft1482w-2.jpg',
'4383' => 'lf_loft1482w-5.jpg',
'4385' => 'lf_loft1482w-6.jpg',
'4341' => 'lf_loft1891w.jpg',
'5764' => 'bra-azzardo-gm1111-ago-leticia.jpg',
'1770' => '5001-01-ap-5.jpg',
'378' => 'dl18406-12ww-black-golg.jpg',
'374' => 'dl18418-11ww-black-golg.jpg',
'4635' => 'pv1-ab.jpg',
'4637' => 'pv1-ob.jpg',
'4646' => 'pv1-pn.jpg',
'6237' => 'bra-favourite-1756-1w-helix.jpg',
'6206' => 'bra-favourite-1757-1w-humpen.jpg',
'6204' => 'bra-favourite-1758-1w-humpen.jpg',
'6216' => 'bra-favourite-1761-1w-bellows.jpg',
'6217' => 'bra-favourite-1783-1w-mesh.jpg',
'6222' => 'bra-favourite-1784-1w-mesh.jpg',
'6228' => 'bra-favourite-1785-1w-brook.jpg',
'6171' => 'bra-favourite-1786-2w-schild.jpg',
'6173' => 'bra-favourite-1787-2w-schild.jpg',
'6227' => 'bra-favourite-1788-1w-strainer.jpg',
'6229' => 'bra-favourite-1789-1w-strainer.jpg',
'6238' => 'bra-favourite-1793-1w-spool.jpg',
'6176' => 'bra-favourite-1801-1w-globi.jpg',
'4523' => 'fe-bluffton1.jpg',
'4570' => 'fe-harrow1.jpg',
'4629' => 'fe-urbanrwl-wb1.jpg',
'4620' => 'fe-urbanrwl-wb2.jpg',
'4545' => 'hk-congres1-a-bc.jpg',
'4541' => 'hk-congres1-a-cm.jpg',
'4556' => 'hk-congres1-b-bc.jpg',
'4544' => 'hk-congres1-b-cm.jpg',
'4548' => 'hk-congres1-c-bc.jpg',
'4549' => 'hk-congres1-c-cm.jpg',
'4478' => 'hk-hampton1.jpg',
'4520' => 'hk-rigby1-kz.jpg',
'4560' => 'kl-brinley1.jpg',
'4500' => 'kl-roswell1.jpg',
'679' => '763690.jpg',
'693' => '765614.jpg',
'692' => '765616.jpg',
'695' => '765617.jpg',
'696' => '765624.jpg',
'698' => '765627.jpg',
'4431' => 'lf_loft1836w.jpg',
'5408' => 'bra-lucide-aty-40105-01-11.jpg',
'5221' => 'bra-lucide-aty-40205-01-11.jpg',
'5275' => 'bra-lucide-baarn-31290-01-15.jpg',
'5012' => 'bra-lucide-bok-17211-03-12.jpg',
'5373' => 'bra-lucide-crunch-31283-01-30.jpg',
'4990' => 'bra-lucide-goa-28857-06-31.jpg',
'5158' => 'bra-lucide-tjoll-37203-01-30.jpg',
'5159' => 'bra-lucide-tjoll-37203-01-31.jpg',
'5222' => 'bra-lucide-tjoll-37203-01-36.jpg',
'5370' => 'bra-lucide-tjoll-37203-11-30.jpg',
'5371' => 'bra-lucide-tjoll-37203-11-31.jpg',
'5372' => 'bra-lucide-tjoll-37203-11-36.jpg',
'5170' => 'bra-lucide-verto-23234-02-12.jpg',
'5171' => 'bra-lucide-verto-23234-02-30.jpg',
'5172' => 'bra-lucide-verto-23235-02-12.jpg',
'75' => 'lsp-9101.jpg',
'77' => 'lsp-9104.jpg',
'86' => 'lsp-9121.jpg',
'90' => 'lsp-9141.jpg',
'103' => 'lsp-9181.jpg',
'97' => 'lsp-9182.jpg',
'102' => 'lsp-9192.jpg',
'188' => 'lsp-9683.jpg',
'227' => 'lsp-9884.jpg',
'4290' => '104854.jpg',
'64' => '2876-1w.jpg',
'6139' => 'bra-omnilux-oml-50001-01.jpg',
'6145' => 'bra-omnilux-oml-50401-01-fartol.jpg',
'6141' => 'bra-omnilux-oml-90001-01-horizon.jpg',
'4577' => 'qz-admiral1-an.jpg',
'4574' => 'qz-admiral1-ib.jpg',
'4499' => 'qz-theaterrow1is.jpg',
'4502' => 'qz-theaterrow1wt.jpg',
'4648' => 'qz-trilogy1.jpg',
'4156' => 'sl464-111-01.jpg',
'67' => '2900-1w.jpg',
'73' => '2900-1wa.jpg',
'71' => '2901-1w.jpg',
];

$arElements = Helpers::_GetInfoElements(false, ["ID", 'DETAIL_PICTURE'], ['IBLOCK_ID' => IBLOCK_CATALOG_ID, 'ACTIVE' => "Y", "ID" => array_keys($arImages)]);

foreach ($arElements as $item) {
	// if( $item['ID'] == 1258 ){
	   // CIBlockElement::SetPropertyValueCode($item['ID'], "MORE_PHOTO", ['VALUE' => CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].$item['DETAIL_PICTURE'])]);
	$el = new CIBlockElement;
	$arLoadProductArray = Array(
		"DETAIL_PICTURE" => CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/utils/images/".$arImages[$item['ID']])
	);
	// $el->Update($item['ID'], $arLoadProductArray);
	// }
   // p($item);
}

