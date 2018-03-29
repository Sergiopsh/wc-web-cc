<?
include_once("lib/jpgraph_lib/jpgraph.php");
include_once("lib/jpgraph_lib/jpgraph_line.php");

$table_colors[]="green@0.3";
$table_colors[]="blue@0.3";
$table_colors[]="red@0.3";
$table_colors[]="yellow@0.3";
$table_colors[]="purple@0.3";
$table_colors[]="black@0.3";
$table_colors[]="deeppink@0.3";

// Setup the graph
$graph = new Graph(750,450);
$graph->SetMargin(40,40,45,90); //droit,gauche,haut,bas
$graph->SetMarginColor('white');
$graph->SetScale("linlin");

// Hide the frame around the graph
$graph->SetFrame(false);

// Note: requires jpgraph 1.12p or higher
$graph->SetBackgroundGradient('#FFFFFF','#CDDEFF:0.8',GRAD_HOR,BGRAD_PLOT);
$graph->tabtitle->Set($table_subtitle);
$graph->tabtitle->SetFont(FF_VERDANA,FS_NORMAL,8);
$graph->tabtitle->SetWidth(TABTITLE_WIDTHFULL);

// Enable X and Y Grid
$graph->xgrid->Show();
$graph->xgrid->SetColor('gray@0.5');
$graph->ygrid->SetColor('gray@0.5');

$graph->yaxis->HideZeroLabel();
$graph->xaxis->HideZeroLabel();
$graph->ygrid->SetFill(true,'#EFEFEF@0.5','#CDDEFF@0.5');

// initialisaton fixe de AXE X
$tableau_hours = array("","01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23");
$graph->xaxis->SetTickLabels($tableau_hours);  

// Setup X-scale
$graph->xaxis->SetLabelAngle(90);

// Format the legend box
$graph->legend->SetColor('navy');
$graph->legend->SetFillColor('gray@0.8');
$graph->legend->SetLineWeight(1);
$graph->legend->SetShadow('gray@0.4',3);
$graph->legend->SetAbsPos(15,130,'right','bottom');

// Create the line plots
for ($l=0;$l<$count;$l++){
	$data = array();
    for($k=0;$k<count($list);$k++) array_push($data, $list[$k][$l+1]);
    $data = array_pad($data, 17, 0);
    $data = array_pad($data, -24, 0);   
	$p2[$l] = new LinePlot($data);
	$p2[$l]->SetColor($table_colors[$l]);
	$p2[$l]->SetWeight(2);
	$p2[$l]->SetLegend($legend[$l]);
	$graph->Add($p2[$l]);
}

// Output the graph
$filenam_res=uniqid(true);
$graph->Stroke("modules/ReportsSuper/images/".$filenam_res);

?>
