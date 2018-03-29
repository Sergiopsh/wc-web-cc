<?php
session_start();
extract($_REQUEST);
include_once("../../config.php");
include_once("../../lib/func.php");
include_once("../../lib/jpgraph_lib/jpgraph.php");
include_once("../../lib/jpgraph_lib/jpgraph_line.php");
include_once("../../lib/jpgraph_lib/jpgraph_bar.php");

include_once("lang/".$lang.".lang.php");

if (!($hourinterval>=0) && ($hourinterval<=23)) exit();

$min_call= intval($min_call);
if (($min_call!=0) && ($min_call!=1)) $min_call=0;


if (!isset($periodfrom)) {	
	$fromstatsday_sday = date("d");
	$fromstatsmonth_sday = date("Y-m");	
}else {
 $periodfrom=dt_to_dbformat($periodfrom."00:00","00");
 $fromstatsday_sday = substr($periodfrom,8,2);
 $fromstatsmonth_sday = substr($periodfrom,0,7);

}
//echo "sday=".$fromstatsday_sday."<br>";
//echo "smon=".$fromstatsmonth_sday."<br>";
$hourintervalplus = $hourinterval+1;

if (isset($fromstatsday_sday) && isset($fromstatsmonth_sday)) 
     $date_clause.=" AND calldate < '$fromstatsmonth_sday-$fromstatsday_sday ".$hourintervalplus.":00:00' AND calldate >= '$fromstatsmonth_sday-$fromstatsday_sday ".$hourinterval.":00:00' ";	


  if(!$db) $db=DbConnect();
  $sql="";
  switch($statustype) {
		case 2: $status_sql = " AND disposition = 'ANSWERED'"; break;
		case 3: $status_sql = " AND disposition = 'NO ANSWER'"; break;
		case 4: $status_sql = " AND disposition = 'BUSY'"; break;
		case 6: $status_sql = " AND disposition = 'FAILED'"; break;
		case 8: $status_sql = " AND disposition = 'CHANANVAIL'"; break;
		default: $status_sql = " "; break;
  }
  $sql.=$status_sql;


  $db->set_sql_interval("ratesec",array($duration1,$duration2));
  $sql.=$db->sql_interval;
  
  if($t_accountcode) $sql.=" AND (t_accountcode = ".quote($t_accountcode).")";


  $db->set_sql_like(array("name","accountcode","dst","src","userfield","department"),array($name,$accountcode,$dst,$src,$userfield,$department)); 
  $sql.=$db->sql_like;





    $sql.=$date_clause;
	/************************/
	$QUERY = "SELECT calldate,duration FROM cdr WHERE 1".$sql;  

	$db -> query($QUERY);
	$num = $db -> num_rows();
	for($i=0;$i<$num;$i++) {				
		$db -> next_record();
		$list_total[] =$db -> Record;
	}






$nbcall = count($list_total);

if(!$nbcall) { ?>
  <html>
  <head>		
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  </head>
  <body>
  <center>
  <div style="font-family: verdana,sans; font-size: 11px; color: #34548f; font-style: italic;">
    <?=$strNoCalls?>
  </div>
  </center>
  </body>
  </html>    
<?
 exit();    
}



$mycall_min[0]=0;
$mycall_dur[0]=0;

for ($i=1; $i <= $nbcall; $i++){

  	$mycall_min[$i] = substr($list_total[$i-1][0],14,2);
	$mycall_minsec_start[$i][0] = substr($list_total[$i-1][0],14,2).substr($list_total[$i-1][0],17,2);
	$mycall_dur[$i] = $list_total[$i-1][1];	
	
	$nx_sec_report = 0;
	$nx_sec = substr($list_total[$i-1][0],17,2) + ($mycall_dur[$i]%60);
	$nx_sec_report = intval($nx_sec/60);
	$nx_sec = $nx_sec%60;
	
	$nx_min = substr($list_total[$i-1][0],14,2) + intval($mycall_dur[$i]/60) + $nx_sec_report;
	if ($nx_min>59) { $nx_min=59; $nx_sec = 59; }
	
	$mycall_minsec_start[$i][1] = sprintf("%02d",$nx_min).sprintf("%02d",$nx_sec);	
	
	//if ($i==10) break;
}

for ($k=0; $k<=count($mycall_minsec_start); $k++){

	if (is_numeric($fluctuation[$mycall_minsec_start[$k][0]])){
		$fluctuation[$mycall_minsec_start[$k][0]]++; 
	}else{
		$fluctuation[$mycall_minsec_start[$k][0]]=1;
	} 
	if (is_numeric($fluctuation[$mycall_minsec_start[$k][1]])){
		$fluctuation[$mycall_minsec_start[$k][1]]--; 
	}else{
		$fluctuation[$mycall_minsec_start[$k][1]]=-1;
	}
}

ksort($fluctuation);

$maxload=1;
$load=0;
while (list ($key, $val) = each ($fluctuation)) {

  $load = $load + $val;
  if (is_numeric($key)) $fluctuation_load[substr($key,0,2).':'.substr($key,2,2)] = $load;

  if ($load > $maxload){ 
  		$maxload=$load;
		
  }
}

function recursif_count_load ($ind, $table, $load)
{
		$maxload=$load;
		$current_start = $table[$ind][0];
		$current_end = $table[$ind][1];
		
		for ($k=$ind+1; $k<=count($table); $k++){
			if ($table[$k][0]<= $current_end){
				$load = recursif_count_load ($k, $table, $load+1);
				if ($load > $maxload) $maxload=$load;	
			}else{
				break;	
			}	
		}
		if ($k<count($table)) $load = recursif_count_load ($k, $table, $load);
		if ($load > $maxload) $maxload=$load;
		return $maxload;
}

// Some data
for ($j=0; $j <= 59; $j++){
 	if ($j==-1) 
		$datax[] = '';
	else 
		$datax[] = sprintf("%02d",$j);

	//$datax = array("","00","01","02","03","04","05","06","07","08","09","10","01","02","03","04","05","06","07","08","09","10","01","02","03","04","05","06","07","08","09","10","01","02","03","04","05","06","07","08","09","10","01","02","03","04","05","06","07","08","09","10");
}	


sort ($mycall_min);
//print_r($mycall_min);

$lineSetWeight = 500 / $nbcall; 
for ($k=1; $k <= $nbcall; $k++){
	$mycall_dur[$k] = intval($mycall_dur[$k] /60)+1;	
	
	for ($j=0; $j <= 59; $j++){
		if ($j==-1){
			$datay[$k][]='';
		}else{
			if ($j==$mycall_min[$k]){
				$datay[$k][]=$k*1;
			}elseif ($j>$mycall_min[$k]){ // CHECK SESSIONTIME
				
				if ( ($mycall_min[$k]+$mycall_dur[$k]) >= $j ) $datay[$k][]=$k*1;
				else $datay[$k][]='';
			
			}else{ // FILL WITH BLANK
				$datay[$k][]='';
			}	
		}
	}	
}

$myrgb = new RGB();
foreach ($myrgb -> rgb_table as $minimecolor){
	$table_colors[]= $minimecolor;
}

/*****************************************************/
/* 		  2 GRAPH - FLUCTUATION & WATCH TRAFFIC	 	 */
/*****************************************************/

if ($typegraph == 'fluctuation'){
		// Setup the graph
		
		$width_graph=750;
		//echo count($fluctuation_load);	
		if (count($fluctuation_load)>60){
			$multi_width = intval(count($fluctuation_load)/60);
			$width_graph  =$width_graph * $multi_width;
		}
		
		//echo "width = $width_graph"; exit();
		//$width_graph = $width_graph * 2;
		$graph = new Graph($width_graph,450);
		$graph->SetMargin(40,40,45,90); //droit,gauche,haut,bas
		$graph->SetMarginColor('white');
		//$graph->SetScale("linlin");
		$graph->SetScale("textlin");
		$graph->yaxis->scale->SetGrace(3);
		
		// Hide the frame around the graph
		$graph->SetFrame(false);
		
		// Note: requires jpgraph 1.12p or higher
		if($hourintervalplus < 10) $hourintervalplus_show="0$hourintervalplus:00"; else $hourintervalplus_show="$hourintervalplus:00";
		$hourinterval_show="$hourinterval:00";
		$graph->tabtitle->Set("$fromstatsmonth_sday-$fromstatsday_sday $strHourGraphic $hourinterval_show-$hourintervalplus_show.  $strCalls -  $nbcall. $strMaxLoad = $maxload");
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
		
		$graph->xaxis->SetTickLabels(array_keys($fluctuation_load)); 
		
		
		
		// Setup X-scale
		//$graph->xaxis->SetTickLabels($tableau_hours[0]);
		$graph->xaxis->SetLabelAngle(90);
		
		
		// Format the legend box
		$graph->legend->SetColor('firebrick1');
		
		$graph->legend->SetFillColor('gray@0.8');
		$graph->legend->SetLineWeight(2);
		$graph->legend->SetShadow('gray@0.4',3);
		$graph->legend->SetPos(0.1,0.12,'left','top');
		$graph->legend->SetMarkAbsSize(1);
//		$graph->legend->SetFont(FF_FONT1,FS_BOLD); 
		$graph->legend->SetFont(FF_VERDANA,FS_BOLD,8); 
	
		
		$indgraph=0;
		
		
		
			$bplot[$indgraph] = new BarPlot(array_values($fluctuation_load));
			
			//$bplot[$indgraph]->SetColor($table_colors[$indgraph]);
			$bplot[$indgraph]->SetWeight(1);
			$bplot[$indgraph]->SetFillColor('orange');
			$bplot[$indgraph]->SetShadow('black',1,1);
			
			$bplot[$indgraph]->value->Show();	
			$bplot[$indgraph]->SetLegend("Max Load = $maxload");
			$graph->Add($bplot[$indgraph]);
			$indgraph++;	
		
		
		// Output the graph
		$graph->Stroke();



}else{


		
		
		
		
		$graph = new Graph(750,800);
		$graph->SetMargin(60,40,45,90); //droit,gauche,haut,bas
		$graph->SetMarginColor('white');
		//$graph->SetScale("linlin");
		$graph->SetScale("textlin");
		$graph->yaxis->scale->SetGrace(1,1);
		
		// Hide the frame around the graph
		$graph->SetFrame(false);
		
		// Note: requires jpgraph 1.12p or higher
		$graph->SetBackgroundGradient('#FFFFFF','#CDDEFF:0.8',GRAD_HOR,BGRAD_PLOT);
		if($hourintervalplus < 10) $hourintervalplus_show="0$hourintervalplus:00"; else $hourintervalplus_show="$hourintervalplus:00";
		$hourinterval_show="$hourinterval:00";
		$graph->tabtitle->Set("$fromstatsmonth_sday-$fromstatsday_sday $strHourGraphic $hourinterval_show-$hourintervalplus_show.  $strCalls - $nbcall. $strMaxLoad = $maxload");
		$graph->tabtitle->SetFont(FF_VERDANA,FS_NORMAL,8);
		$graph->tabtitle->SetWidth(TABTITLE_WIDTHFULL);
		
		//$graph->yaxis->Hide();
		// Enable X and Y Grid
		//$graph->xgrid->Show();
		$graph->xgrid->SetColor('gray@0.5');
		$graph->ygrid->SetColor('gray@0.5');
		
		//$graph->yaxis->HideZeroLabel();
		$graph->xaxis->HideZeroLabel();
		$graph->ygrid->SetFill(true,'#EFEFEF@0.5','#CDDEFF@0.5');
		
		$graph->xaxis->SetTickLabels($datax);
		$graph->xaxis->SetLabelAngle(90);
		
		
		$graph->yaxis->HideFirstLastLabel();
		//$graph->yaxis->HideLine();
		$graph->yaxis->HideTicks(); 
		$strFormat="%1d $strShortCalls";
		$graph->yaxis->SetLabelFormatString($strFormat); 
		$graph->yaxis->SetFont(FF_VERDANA,FS_NORMAL,8); 
		$graph->yaxis->SetTextLabelInterval(2); 
		
		
		// Format the legend box
		$graph->legend->SetColor('firebrick1');
		
		$graph->legend->SetFillColor('gray@0.8');
		$graph->legend->SetLineWeight(2);
		$graph->legend->SetShadow('gray@0.4',3);
		$graph->legend->SetPos(0.2,0.2,'left','center');
		$graph->legend->SetMarkAbsSize(1);
//		$graph->legend->SetFont(FF_FONT1,FS_BOLD); 
		$graph->legend->SetFont(FF_VERDANA,FS_BOLD,8); 
		for ($i=1;$i<=count($datay);$i++){
		
			// Create the first line
			$p1[$i] = new LinePlot($datay[$i]);	
			$p1[$i]->SetColor($table_colors[($i+20) % 436]  );
			$p1[$i]->SetCenter();
			$p1[$i]->SetWeight($lineSetWeight);
			if ($i==1) {

			    $p1[$i]->SetLegend("Max Load = $maxload");
			}
			$graph->Add($p1[$i]);
			
		}
		
		// Output line
		$graph->Stroke();
		
		
		
}//END IF (typegraph)


?>
