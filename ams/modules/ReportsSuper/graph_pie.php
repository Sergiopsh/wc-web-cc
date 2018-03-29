<?php

include_once("lib/jpgraph_lib/jpgraph.php");
include_once("lib/jpgraph_lib/jpgraph_pie.php");
include_once("lib/jpgraph_lib/jpgraph_pie3d.php");

/**************************************/
//$data = array(40,60,21,33, 10, NULL);

$graph = new PieGraph(545,220,"auto");
$graph->SetShadow();
$graph->SetAntiAliasing();
$months_display=$months_compare+1;
switch ($months_display) {
    case 1: $ms=$strMonthOne; break;
    case 2: $ms=$strMonthTwo; break;
    case 3: $ms=$strMonthTwo; break;
    case 4: $ms=$strMonthTwo; break;
    default: $ms=$strMonths; break;
}

$graph->title->Set("$strTrafficLast $months_display $ms");
$graph->title->SetFont(FF_VERDANA,FS_BOLD,10);

$p1 = new PiePlot3D($data_graph);
$p1->ExplodeSlice(1);
$p1->SetCenter(0.28);
$p1->SetSliceColors(array('darkolivegreen3','royalblue','brown1','darkolivegreen1','lightblue3','coral','palegreen'));
$p1->SetLegends($mylegend);
$p1->value->HideZero();

// Format the legend box
$graph->legend->SetColor('navy');
$graph->legend->SetFillColor('gray@0.8');
$graph->legend->SetLineWeight(1);
$graph->legend->SetFont(FF_COURIER,FS_BOLD,9);
$graph->legend->SetShadow('gray@0.4',3);
$graph->legend->SetAbsPos(12,25,'right','top');


$graph->Add($p1);
$filename_res=uniqid(true);
$graph->Stroke("modules/ReportsSuper/images/".$filename_res);
?>
