<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

$this->setFrameMode(true);
?>

<div id="text-<?= $arResult['TEXT_HASH'] ?>" class="text-unique-checks" data-text-hash="<?= $arResult['TEXT_HASH'] ?>">
	<div class="loading">
		<?=GetMessage("NSANDREY_TEXTCHECKER_ZAGRUZKA_DANNYH_OB_U")?></div>
	<div class="error">
		<?=GetMessage("NSANDREY_TEXTCHECKER_PROIZOSLA_OSIBKA_PRI")?></div>
	<div class="data">
		<? if ($arParams['SHOW_UNIQUE_STAT'] == 'Y'): ?>
			<span><?=GetMessage("NSANDREY_TEXTCHECKER_PROCENT_UNIKALQNOSTI")?><span class="unique-percent"></span></span></span>
		<? endif ?>

		<? if ($arParams['SHOW_SPELLING_STAT'] == 'Y'): ?>
			<span><?=GetMessage("NSANDREY_TEXTCHECKER_OSIBOK_PRAVOPISANIA")?><span class="spelling-errors"></span></span></span>
		<? endif ?>

		<? if ($arParams['SHOW_CHARS_WITH_SPACE_STAT'] == 'Y'): ?>
			<span><?=GetMessage("NSANDREY_TEXTCHECKER_KOLICESTVO_SIMVOLOV")?><span class="chars-with-space"></span></span>
		<? endif ?>

		<? if ($arParams['SHOW_CHARS_WITHOUT_SPACE_STAT'] == 'Y'): ?>
			<span><?=GetMessage("NSANDREY_TEXTCHECKER_KOLICESTVO_SIMVOLOV1")?><span class="chars-without-space"></span></span>
		<? endif ?>

		<? if ($arParams['SHOW_WORDS_STAT'] == 'Y'): ?>
			<span><?=GetMessage("NSANDREY_TEXTCHECKER_KOLICESTVO_SLOV")?><span class="words"></span></span>
		<? endif ?>

		<? if ($arParams['SHOW_SEO_WATER_STAT'] == 'Y'): ?>
			<span><?=GetMessage("NSANDREY_TEXTCHECKER_PROCENT_VODY")?><span class="water"></span></span>
		<? endif ?>

		<? if ($arParams['SHOW_SEO_SPAM_STAT'] == 'Y'): ?>
			<span><?=GetMessage("NSANDREY_TEXTCHECKER_PROCENT_ZASPAMLENNOS")?><span class="spam"></span></span>
		<? endif ?>

		<? if ($arParams['SHOW_MIXED_WORDS_STAT'] == 'Y'): ?>
			<span><?=GetMessage("NSANDREY_TEXTCHECKER_KOLICESTVO_SLOV_S_SI")?><span class="mixed-words"></span></span>
		<? endif ?>
	</div>
</div>
