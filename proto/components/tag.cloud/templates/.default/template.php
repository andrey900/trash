<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
if( !empty($arResult['ITEMS']) ):
$s = '';
	foreach ($arResult['ITEMS'] as $PROP_NAME => $arElements) {
		foreach ($arElements as $arElement) {
            //p($arElement);
            if( preg_match("/section/i", $PROP_NAME, $match) )
                $s .= sprintf('<li><a href="%s">%s</a></li>', $arElement['SECTION_PAGE_URL'], $arElement['NAME']);
            else
                $s .= sprintf('<li><a href="/brands/%s">%s</a></li>', $arElement['CODE'], $arElement['NAME']);
            }
	}
endif;
?>
<canvas width="990" height="130" id="tagcloud">
<p>Anything in here will be replaced on browsers that support the canvas element</p>
<ul>
<li><a href="/novelty/?type=newscompany">Новинки компании</a></li>
<li><a href="/novelty/?type=newsmarket">Новости рынка</a></li>
<?=$s?>
</ul>
</canvas>
<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.tagcanvas.min.js?2.5.1" type="text/javascript"></script>
<script>
$(document).ready(function() {
    $('#tagcloud').tagcanvas({
    	freezeActive : true,
        textColour : '#FF770D', // Цвет текста
        outlineThickness : 0, // Обводка у ссылок (Да, Нет)
        maxSpeed : 0.03, // Максимальная скорость
        depth : 0.1, // Глубина. От 0 до 1
        shape : 'shape',
        decel : 0.05,
        outlineColour: 'transparent',
        lock : "x",
        zoom : 1,
        stretchX: 7,
        initial: [0.1, 0.0],
        ToolTipDelay: 1000
    })
});
</script>