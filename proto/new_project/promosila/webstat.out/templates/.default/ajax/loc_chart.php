<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if (!isset($_GET) || empty($_GET)) die('<p style="color:#ff3344;">Error: illegal ajax-request (file: loc_chart.php)</p>');

if(CModule::IncludeModule("promosila.pswebstat"))
{
	$webstat = new Ps\WebStat;
	if($webstat->Charset1251()) header("Content-Type: text/html; charset=windows-1251");
	$res = $webstat->GetVisibility($_GET["project_id"], $_GET["engine_id"]);
}
else die('<p style="color:#ff3344;">Error: Module "pswebstat" is not installed</p>');
if(!$res) die('<p style="color:#ff3344;">Error: No data or service is not available</p>');

require("../lib/gChart.php");

$arTmp = Ps\WebStat::ChartZoom($res[$_GET["key"]][$_GET["mode"]]);

if($_GET["mode"] == "positions")
{
	//$title = "График позиций запроса";
	$title = GetMessage("PSWEBSTAT_CHART_TITLE1");
	foreach($arTmp as $key => $val)
	{
		if(++$val <= 0) $val = 101;
		$y[] = $val;
		$d = explode("-", $key);
		$xLabel[] = end($d) . "/" . prev($d);
	}
	$x = count($y);
	$lineChart = new gLineChart(600,300);
	$lineChart->addDataSet($y);
	//$lineChart->setLegend(array("first", "second", "third","fourth"));
	$lineChart->setColors(array("ff3344", "11ff11", "22aacc", "3333aa"));
	$lineChart->setVisibleAxes(array('x','y'));
	$lineChart->setDataRange(100,0);
	//$lineChart->addAxisRange(0, 1, $x, 1);
	$lineChart->addAxisLabel(0, $xLabel);
	$lineChart->addAxisRange(1, 100, 0);
	$lineChart->setGridLines(100/--$x,10);
}
else die('<p style="color:#ff3344;">Error: illegal ajax-request (file: loc_chart.php)</p>');

//echo "<pre>"; print_r($arTmp); echo "</pre>";

?>
<div class="webstat-h1"><?=$_GET["engine_name"]?></div>
<center>
<div class="webstat-h1"><?=$title?> "<?=$res[$_GET["key"]]["phrase"]?>"</div>
</center>
<img src="<? print $lineChart->getUrl();  ?>" />
