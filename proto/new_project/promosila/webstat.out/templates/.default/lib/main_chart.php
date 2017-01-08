<?

$y = Ps\WebStat::ChartZoom($arResult["visibility"]);
$x = count($y);
foreach($y as $key => $val)
{
	$d = explode("-", $key);
	$xLabel[] = end($d) . "/" . prev($d);
}

$lineChart = new gLineChart(600,300);
$lineChart->addDataSet($y);
//$lineChart->setLegend(array("first", "second", "third","fourth"));
$lineChart->setColors(array("ff3344", "11ff11", "22aacc", "3333aa"));
$lineChart->setVisibleAxes(array('x','y'));
$lineChart->setDataRange(0,100);
//$lineChart->addAxisRange(0, 1, $x, 1);
$lineChart->addAxisLabel(0, $xLabel);
$lineChart->addAxisRange(1, 0, 100);
$lineChart->setGridLines(100/--$x,10);

?>
<img src="<? print $lineChart->getUrl();  ?>" /> <br>
