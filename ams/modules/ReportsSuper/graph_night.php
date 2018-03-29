<?
include_once("lib/jpgraph_lib/jpgraph.php");
include_once("lib/jpgraph_lib/jpgraph_line.php");
include_once("lib/jpgraph_lib/jpgraph_bar.php");

$graph = new Graph(800,540);
$graph->SetScale("textlin");
$graph->SetShadow();
$graph->img->SetMargin(40,130,30,40);

// Format the legend box
$graph->legend->SetColor('navy');
$graph->legend->SetFillColor('gray@0.8');
$graph->legend->SetLineWeight(1);
$graph->legend->SetFont(FF_COURIER,FS_BOLD,9);
$graph->legend->SetShadow('gray@0.4',3);
$graph->legend->SetAbsPos(12,25,'right','top');

$tableau_hours[0] = array("21","22","23","00","01","02","03","04","05","06","07" );

$graph->xaxis->SetTickLabels($tableau_hours[0]);

$gdataanswered = array();
$gdatanoanswer = array();
for($j=21,$s=0; $j!=8; $j++,$s++){
    if ($j==24){ 
	$j = 0;
    };
    $gdataanswered[$s] = $dataanswered[$j];
    $gdatanoanswer[$s] = $datanoanswer[$j];
};



$b1plot = new BarPlot($gdataanswered);
$b1plot->SetFillColor("darkolivegreen3");
$b1plot->value->Show();
$b1plot->value->SetFont(FF_VERDANA,FS_BOLD,5);
$b1plot->value->SetAngle(45);
$b1plot->value->SetFormat('%0.1f');
$b1plot->SetLegend("Принято");

$b2plot = new BarPlot($gdatanoanswer);
$b2plot->SetFillColor("royalblue");
$b2plot->value->Show();
$b2plot->value->SetFont(FF_VERDANA,FS_BOLD,5);
$b2plot->value->SetAngle(45);
$b2plot->value->SetFormat('%0.1f');
$b2plot->SetLegend("Пропущено");

$gbplot = new GroupBarPlot(array($b1plot,$b2plot));

$graph->Add($gbplot);


$graph->title->Set("Звонков в час.");
//$graph->xaxis->title->Set("X-title");
//$graph->yaxis->title->Set("Y-title");

$graph->title->SetFont(FF_VERDANA,FS_BOLD,10);
//$graph->tabtitle->SetWidth(TABTITLE_WIDTHFULL);
//$graph->title->SetFont(FF_FONT1,FS_BOLD);
//$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
//$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

$filenam_res=uniqid(true);
$graph->Stroke("modules/ReportsSuper/images/".$filenam_res);
?>
