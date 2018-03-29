<?
include_once("lib/jpgraph_lib/jpgraph.php");
include_once("lib/jpgraph_lib/jpgraph_line.php");


$datax1 = array_keys($table_graph_hours);
$datay1 = array_values ($table_graph_hours);

//$days_compare // 3
$nbday=0;  // in tableau_value and tableau_hours to select the day in which you store the data
//$min_call=0; // min_call variable : 0 > get the number of call 1 > number minutes


$table_subtitle[]="$strStat : $strCallsPerHour";
$table_subtitle[]="$strStat : $strMinPerHour";

$table_colors[]="green@0.3";
$table_colors[]="blue@0.3";
$table_colors[]="red@0.3";
$table_colors[]="yellow@0.3";
$table_colors[]="purple@0.3";

$jour = substr($datax1[0],8,2); //le jour courant 
$legend[0] = substr($datax1[0],0,10); //l

$min_call=intval($min_call);
if (($min_call!=0) && ($min_call!=1)) $min_call=0;
$min_call=0;

foreach ($table_graph_hours as $key => $value) {
	
	$jour_suivant = substr($key,8,2);
	
	if($jour_suivant != $jour) {
		  $nbday++; 
		  $legend[$nbday] = substr($key,0,10);
		  $jour = $jour_suivant;
	}
  
	
	$heure = intval(substr($key,11,2));

	if ($min_call == 0) $div = 1; else $div = 60;

	$tmp=$tableau_value[$nbday][$heure] = $value[$min_call]/$div;
	//echo "<br> day = $nbday hour = $heure value = $tmp";
}

// je remplie les cases vide par des 0
?><br><br>
<?
for ($i=0; $i<=$nbday; $i++)
      for ($j=0; $j<24; $j++) {
              if (!isset($tableau_value[$i][$j])) $tableau_value[$i][$j]=0;
	      $tmp = $tableau_value[$i][$j];
      }

for($i=0;$i<23;$i++) {
    $t=$tableau_value[2][$i];
    //echo "t =".$t."<br>";
}

//Je remplace les 0 par null pour pour les heures 
$i = 23;
//while ($tableau_value[$nbday][$i] == 0) {
//      $tableau_value[$nbday][$i] = null;
//      $i--;
//}


// Setup the graph
$graph = new Graph(750,450);
$graph->SetMargin(40,40,45,90); //droit,gauche,haut,bas
$graph->SetMarginColor('white');
$graph->SetScale("linlin");

// Hide the frame around the graph
$graph->SetFrame(false);

// Note: requires jpgraph 1.12p or higher
$graph->SetBackgroundGradient('#FFFFFF','#CDDEFF:0.8',GRAD_HOR,BGRAD_PLOT);
$graph->tabtitle->Set($table_subtitle[$min_call]);
$graph->tabtitle->SetFont(FF_VERDANA,FS_NORMAL,8);
$graph->tabtitle->SetWidth(TABTITLE_WIDTHFULL);

// Enable X and Y Grid
$graph->xgrid->Show();
$graph->xgrid->SetColor('gray@0.5');
$graph->ygrid->SetColor('gray@0.5');

$graph->yaxis->HideZeroLabel();
$graph->xaxis->HideZeroLabel();
$graph->ygrid->SetFill(true,'#EFEFEF@0.5','#CDDEFF@0.5');

//$graph->xaxis->SetTickLabels($tableau_hours[0]);

// initialisaton fixe de AXE X
$tableau_hours[0] = array("","01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23");
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
$graph->legend->SetAbsPos(15,130,'right','bottom');

// Create the line plots

for ($indgraph=0;$indgraph<=$nbday;$indgraph++){
	
	$p2[$indgraph] = new LinePlot($tableau_value[$indgraph]);
	$p2[$indgraph]->SetColor($table_colors[$indgraph]);
	$p2[$indgraph]->SetWeight(2);
	$p2[$indgraph]->SetLegend($legend[$indgraph]);
	$graph->Add($p2[$indgraph]);
	
}


// Output the graph
$filenam_res=uniqid(true);
$graph->Stroke("modules/Reports/images/".$filenam_res);

?>
