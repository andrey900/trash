<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="webstat-container">
	<?=GetMessage("PSWEBSTAT_PROJECT");?> <a href="https://serpseeker.com/projects/" target="blank"
	title="<?=GetMessage("PSWEBSTAT_SERV_TITLE");?>"><?=$arResult["ProectInfo"]["name"]?></a><br>
	<?=GetMessage("PSWEBSTAT_BALANCE");?> <?=round($arResult["balance"]["balance"], 2)?> <?=GetMessage("PSWEBSTAT_BALANCE_RUB");?><br><br>
	<div class="webstat-h1"><center><?=GetMessage("PSWEBSTAT_COMMON_DATA");?></center></div>
	<div class="webstat-int15">
		<?=GetMessage("PSWEBSTAT_SITE");?> <a href="<?=$arResult["ProectInfo"]["url"]?>" target="blank"><?=$arResult["ProectInfo"]["url"]?></a><br>
		<?=GetMessage("PSWEBSTAT_YANDEX_PAGES");?> <?=$arResult["ProectInfo"]["YANDEX_PAGES"]?><br>
		<?=GetMessage("PSWEBSTAT_GOOGLE_PAGES");?> <?=$arResult["ProectInfo"]["GOOGLE_PAGES"]?><br>
		<?=GetMessage("PSWEBSTAT_YANDEX_CY");?> <?=$arResult["ProectInfo"]["YANDEX_CY"]?><br>
		<?=GetMessage("PSWEBSTAT_GOOGLE_PR");?> <?=$arResult["ProectInfo"]["GOOGLE_PAGERANK"]?><br>
	</div>
	<center>
		<div class="webstat-h1"><?=GetMessage("PSWEBSTAT_MAIN_CHART");?> 
			<span style="font-weight:normal;">
				(<?=GetMessage("PSWEBSTAT_MC_CURVAL");?> <?=end($arResult["visibility"])?>%)
			</span>
		</div>
		<?
		require("lib/gChart.php");
		include("lib/main_chart.php");
		?>
	</center><br>
	<div class="webstat-h1"><center><?=GetMessage("PSWEBSTAT_DETAIL_INFO");?></center></div>
	<table class="webstat-tit_table" cellspacing="0" cellpadding="0" border="0">
		<tr><td align="left">
				<div class="webstat-select_block" title="<?=GetMessage("PSWEBSTAT_TOOLTIP");?>">
					<div class="webstat-select_field"><div id="btn"></div>
						<span><?=GetMessage("PSWEBSTAT_DEF_SEL");?></span>
					</div>
					<ul class="webstat-select_list">
						<?foreach($arResult["menu"] as $key => $val)
							echo "<li alt=\"$key\">$val</li>";?>
					</ul>
				</div>
		</td></tr>
	</table>
	<div id="webstat-ajax1">
		<table class="webstat-table" width="100%" cellspacing="0" cellpadding="10" border="0">
			<thead>
				<tr>
					<td width="35%"><?=GetMessage("PSWEBSTAT_TABTITLE1");?></td>
					<td><?=GetMessage("PSWEBSTAT_TABTITLE2");?></td>
					<td><?=GetMessage("PSWEBSTAT_TABTITLE3");?></td>
					<td><?=GetMessage("PSWEBSTAT_TABTITLE4");?></td>
					<td><?=GetMessage("PSWEBSTAT_TABTITLE5");?></td>
				</tr>
			</thead>
			<tr>
				<td align="left">&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>
	</div>
	<div id="webstat-load"></div>
</div>
