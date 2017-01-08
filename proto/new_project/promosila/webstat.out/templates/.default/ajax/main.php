<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if (!isset($_GET) || empty($_GET)) die('<p style="color:#ff3344;">Error: illegal ajax-request (file: main.php)</p>');

if(CModule::IncludeModule("promosila.pswebstat"))
{
	$webstat = new Ps\WebStat;
	if($webstat->Charset1251()) header("Content-Type: text/html; charset=windows-1251");
	$res = $webstat->GetVisibility($_GET["project_id"], $_GET["engine_id"]);
}
if(!$res) die('<p style="color:#ff3344;">Error: No data or service is not available</p>');
//IncludeTemplateLangFile(__FILE__);
?>
<table class="webstat-table" width="100%" cellspacing="0" cellpadding="10" border="0">
	<thead>
		<tr>
			<?/*<td width="35%">Поисковый запрос</td>
			<td>Популяр- ность запроса</td>
			<td>Текущая позиция запроса</td>
			<td>Изме- нение позиции</td>
			<td>Динамика за последние 30 дней</td>*/?>
			<td width="35%"><?=GetMessage("PSWEBSTAT_TABTITLE1");?></td>
			<td><?=GetMessage("PSWEBSTAT_TABTITLE2");?></td>
			<td><?=GetMessage("PSWEBSTAT_TABTITLE3");?></td>
			<td><?=GetMessage("PSWEBSTAT_TABTITLE4");?></td>
			<td><?=GetMessage("PSWEBSTAT_TABTITLE5");?></td>
		</tr>
	</thead>
<?
$path = $_GET["path"] . "/ajax/loc_chart.php";
$rq_base = "?project_id=" . $_GET["project_id"] . "&engine_id=" . $_GET["engine_id"];
$rq_base .= "&engine_name=" . str_replace(' ','%20', $_GET["engine_name"]);
//$rq_base .= "&engine_name=" .  rawurlencode($_GET["engine_name"]);
$link_beg = '<a class="webstat-fbox fancybox.ajax" href="';
//$link_end = '">показать</a>';
$link_end = '">' . GetMessage("PSWEBSTAT_CHART_SHOW") . '</a>';
foreach($res as $key => $val)
{
	echo "<tr>\n";
	echo '<td align="left">', $val["phrase"], "</td>\n";
	echo "<td>", $val["phrase_popularity"], "</td>\n";
	if(++$val["previous_pos"] <= 0) $prev = 101;
	else $prev = $val["previous_pos"];
	if(++$val["current_pos"] <= 0)
	{
		$val["current_pos"] = ">100";
		$cur = 101;
	}
	else $cur = $val["current_pos"];
	$change = $prev - $cur;
	echo "<td>", $val["current_pos"], "</td>\n";
	if($change > 0) $change = "<span class=\"webstat-green\">+$change</span>";
	elseif($change < 0) $change = "<span class=\"webstat-red\">$change</span>";
	echo "<td>", $change, "</td>\n";
	$link = $link_beg . $path . $rq_base . "&key=" . $key . "&mode=positions" . $link_end;
	echo "<td>", $link, "</td>\n";
	echo "</tr>\n";
}
?>
</table>
