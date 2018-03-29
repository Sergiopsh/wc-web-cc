<?php

include_once("lib/jpgraph_lib/jpgraph.php");
include_once("lib/jpgraph_lib/jpgraph_bar.php");
include_once("lib/jpgraph_lib/jpgraph_line.php");


$datax1 = array_keys($table_graph_hours);
$datay1 = array_values ($table_graph_hours);

//$days_compare // 3
$nbday=1;  // in tableau_value and tableau_hours to select the day in which you store the data

$table_subtitle[0]="$strStat : $strCallsPerHour";
$table_subtitle[1]="$strStat : $strASRPerHour";
$table_subtitle[2]="$strStat : $strCallsAndASRPerHour";

$table_colors[]="orange";
$table_colors[]="red@0.3";
$table_colors[]="blue@0.3";
$table_colors[]="red@0.3";

$jour = substr($datax1[0],8,2); //le jour courant 
$legend[0] = $strCalls;
$legend[1] = "ASR %";

$max_calls=0;
$max_asr=0;
foreach ($table_graph_hours as $key => $value) {
	//echo "<br> asr =(".$value[2]."/".$value[0].")*100";
	$heure = intval(substr($key,11,2));
	$tableau_value[0][$heure] = $value[0];
	if ($value[0] > $max_calls) $max_calls=$value[0];
	if ($value[0] ==0) $tableau_value[1][$heure] = 0;
	else $tableau_value[1][$heure] = $value[2]/$value[0]*100;
	if ($tableau_value[1][$heure] > $max_asr) $max_asr=$tableau_value[1][$heure];
	
}



if ($max_asr > 0) $coeff = $max_calls/$max_asr; 
else $coeff=1;

if ($type_gr == 3) {
      for ($j=0; $j<24; $j++) {
              $tableau_value[1][$j]*=$coeff;
	      if ($tableau_value[1][$j]==0) $tableau_value[1][$j]=null;
      }
}  

// je remplie les cases vide par des 0
//echo $nbday;
for ($i=0; $i<=$nbday; $i++)
      for ($j=0; $j<24; $j++)
              if (!isset($tableau_value[$i][$j])) $tableau_value[$i][$j]=0;

//Je remplace les 0 par null pour pour les heures 
$i = 23;

// Setup the graph
$graph = new Graph(750,450);
$graph->SetMargin(40,40,45,90); //droit,gauche,haut,bas
$graph->SetMarginColor('white');
//$graph->SetScale("linlin");
$graph->SetScale("textlin");
$graph->yaxis->scale->SetGrace(3);

//$graph->yaxis->HideZeroLabel();
$graph->yaxis->HideTicks(true);

// Hide the frame around the graph
$graph->SetFrame(false);

// Setup title
$graph->title->SetFont(FF_VERDANA,FS_NORMAL,11);

// Note: requires jpgraph 1.12p or higher
$graph->SetBackgroundGradient('#FFFFFF','#CDDEFF:0.8',GRAD_HOR,BGRAD_PLOT);

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
$tableau_hours[0] = array("00","01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23");
$graph->xaxis->SetTickLabels($tableau_hours[0]);  

// Setup X-scale
//$graph->xaxis->SetTickLabels($tableau_hours[0]);
$graph->xaxis->SetLabelAngle(90);

// Format the legend box
$graph->legend->SetColor('navy');
$graph->legend->SetFillColor('gray@0.8');
$graph->legend->SetLineWeight(1);
//$graph->legend->SetFont(FF_ARIAL,FS_BOLD,8);
$graph->legend->SetShadow('gray@0.4',3);
$graph->legend->SetAbsPos(40,90,'right','bottom');
$graph->legend->SetFont(FF_VERDANA,FS_NORMAL,8);


$indgraph=0;

while ($indgraph<=1){
	
	$bplot[$indgraph] = new BarPlot($tableau_value[$indgraph]);
		
	
	$bplot[$indgraph]->SetColor($table_colors[$indgraph]);
	$bplot[$indgraph]->SetWeight(2);
	$bplot[$indgraph]->SetFillColor($table_colors[$indgraph]);
	$bplot[$indgraph]->SetShadow();
	$bplot[$indgraph]->SetLegend($legend[$indgraph]);
	$indgraph++;
	
}




if ($type_gr == 1 ) {

    $bplot[0]->value->Show();
    $bplot[0]->value->HideZero();
    $graph->tabtitle->Set($table_subtitle[0]);
    $graph->Add($bplot[0]);
}
elseif ($type_gr == 2 ) {
    $bplot[1]->value->Show();
    $bplot[1]->value->HideZero();
    $graph->tabtitle->Set($table_subtitle[1]);
    $graph->Add($bplot[1]);
}
else {

	$bp = new LinePlot($tableau_value[1]);
	$bp->SetColor("red");
	$bp->SetWeight(2);
	$bp->SetLegend($legend[1]);
        $bplot[0]->value->Show();
	$bplot[0]->value->HideZero();
     
      $graph->tabtitle->Set($table_subtitle[2]);
 
	$graph->Add($bplot[0]);
	$graph->Add($bp);
}


// Output the graph
$filename_res=uniqid(true);
$graph->Stroke("modules/ReportsSuper/images/".$filename_res);

?>
