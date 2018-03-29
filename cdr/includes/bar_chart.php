<?php
/*
 Copyright (C) 2006 Earl C. Terwilliger
 Email contact: earl@micpc.com

    This file is part of The Asterisk Queue/CDR Log Analyzer WEB Interface.

    These files are free software; you can redistribute them and/or modify
    them under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    These programs are distributed in the hope that they will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
*/

require_once 'Image/Graph.php';    

$xt       = $_GET['xt'];
$data     = unserialize($_GET['data']);
$title    = unserialize($_GET['ti']);
$subtitle = unserialize($_GET['st']);

if (sizeof($data) > 7) {
   $Graph =& Image_Graph::factory('graph', array(600, 400));
   $Font =& $Graph->addNew('font', 'Verdana');
   $Font->setSize(8);
   $Graph->setFont($Font);
}
else { 
   $Graph =& Image_Graph::factory('graph', array(400, 300));
   $Font =& $Graph->addNew('font', 'Verdana');
   $Font->setSize(10);
   $Graph->setFont($Font);
}


$Graph->add(
    Image_Graph::vertical(
        Image_Graph::vertical(
            Image_Graph::factory('title', $title),
            Image_Graph::factory('title', $subtitle),
            80
        ),
        Image_Graph::vertical(
            $Plotarea = Image_Graph::factory('plotarea'),
            $Legend   = Image_Graph::factory('legend'),
            85
        ),
    9)
);
$Legend->setPlotarea($Plotarea);

$GridY =& $Plotarea->addNew('line_grid', null, IMAGE_GRAPH_AXIS_Y);
$GridY->setLineColor('gray@0.1');

$Dataset =& Image_Graph::factory('dataset');
while (list($key,$val) = each($data))  $Dataset->addPoint($key,$val);

$Plot =& $Plotarea->addNew('bar', array(&$Dataset));
$Plot->setLineColor('gray');
$Plot->setFillColor('blue@0.2');

//$Plot =& $Plotarea->addNew('smooth_line', array(&$Dataset));
//$Plot->setLineColor('blue');
$Plot->setTitle($xt);

//$DataPreprocessor_Fmt =& Image_Graph::factory('Image_Graph_DataPreprocessor_Formatted', '%02d');

$AxisX =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_X);
$AxisX->setAxisIntersection('min');

$AxisY =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_Y);
$AxisY->showLabel(IMAGE_GRAPH_LABEL_ZERO);
$AxisY->setDataPreprocessor($DataPreprocessor_Fmt);
$AxisY->setTitle($xt, 'vertical');

//$Marker =& $Plot->addNew('Image_Graph_Marker_Value', IMAGE_GRAPH_VALUE_Y);    
//$Marker->setDataPreprocessor($DataPreprocessor_Fmt);
//$PointingMarker =& $Plot->addNew('Image_Graph_Marker_Pointing_Angular', array(20, &$Marker));
//$Plot->setMarker($PointingMarker);      

$Graph->setPadding(10);
$Graph->done();
?> 
