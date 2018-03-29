<?php

include_once("lib/jpgraph_lib/jpgraph.php");
include_once("lib/jpgraph_lib/jpgraph_pie.php");
include_once("lib/jpgraph_lib/jpgraph_pie3d.php");

/**************************************/
//$data = array(40,60,21,33, 10, NULL);

$graph = new PieGraph(500,270,"auto");
$graph->SetShadow();
$graph->SetAntiAliasing();
//$months_display=$agent_compare;

$graph->title->Set("$graph_title");
$graph->title->SetFont(FF_VERDANA,FS_BOLD,10);

$tt = $data_agents[1];
$data_agents[1] = $data_agents[0];
$data_agents[0] = $tt;

$tt = $name_agents[1];
$name_agents[1] = $name_agents[0];
$name_agents[0] = $tt;


$p1 = new PiePlot3D($data_agents);  //$Data_graph - массив с данными для секторов!!!
$p1->ExplodeSlice(1);
$p1->SetCenter(0.38);
$p1->SetSliceColors(array('darkolivegreen3','royalblue','brown1','darkolivegreen1','lightblue3','coral','palegreen'));
$p1->SetLegends($name_agents);
$p1->value->HideZero();

// Format the legend box
$graph->legend->SetColor('navy');
$graph->legend->SetFillColor('gray@0.8');
$graph->legend->SetLineWeight(1);
$graph->legend->SetFont(FF_COURIER,FS_BOLD,9);
$graph->legend->SetShadow('gray@0.4',3);
$graph->legend->SetAbsPos(12,25,'right','top');


$graph->Add($p1);
$agents_res=uniqid(true);
$graph->Stroke("modules/operator_reports/images/".$agents_res);
?>
