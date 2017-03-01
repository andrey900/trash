<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Бренды");?>
<div class="about-section mb-80">
	<?$APPLICATION->IncludeComponent(
        "bitrix:news.line", 
        "brands", 
        Array(
            "ACTIVE_DATE_FORMAT" => "d.m.Y",    // Формат показа даты
            "CACHE_GROUPS" => "Y",  // Учитывать права доступа
            "CACHE_TIME" => "36000", // Время кеширования (сек.)
            "CACHE_TYPE" => "A",    // Тип кеширования
            "DETAIL_URL" => "", // URL, ведущий на страницу с содержимым элемента раздела
            "FIELD_CODE" => array(  // Поля
                0 => "NAME",
                1 => "PREVIEW_TEXT",
                2 => "PREVIEW_PICTURE",
                3 => "",
            ),
            "IBLOCKS" => array( // Код информационного блока
                0 => IBLOCK_BRANDS_ID,
            ),
            "IBLOCK_TYPE" => IBLOCK_CONTENT_TYPE,   // Тип информационного блока
            "NEWS_COUNT" => "100",   // Количество новостей на странице
            "SORT_BY1" => "NAME",    // Поле для первой сортировки новостей
            "SORT_BY2" => "ID",   // Поле для второй сортировки новостей
            "SORT_ORDER1" => "ASC",    // Направление для первой сортировки новостей
            "SORT_ORDER2" => "DESC", // Направление для второй сортировки новостей
            "SHOW_NAME" => "Y",
            "USE_SLIDER" => "N",
            "SHOW_ALL" => "Y"
        ),
        false
    );?>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>