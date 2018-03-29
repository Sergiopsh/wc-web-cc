<?php
require_once 'Image/Graph.php';    

$Graph =& Image_Graph::factory('graph', array(400, 300));

$Font =& $Graph->addNew('font', 'Verdana');
$Font->setSize(10);

$Graph->setFont($Font);

$Graph->add(
    Image_Graph::vertical(
        Image_Graph::vertical(
            Image_Graph::factory('title', array('Daily Call Volumes', 12)),
            Image_Graph::factory('title', array('April 21-26, 2006', 8)),
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
$Dataset->addPoint("Mon\nApr 21", 20);
$Dataset->addPoint("Tue\nApr 22", 24);
$Dataset->addPoint("Wed\nApr 22", 22);
$Dataset->addPoint("Thu\nApr 23", 25);
$Dataset->addPoint("Fri\nApr 24", 21);
$Dataset->addPoint("Sat\nApr 25", 22);
$Dataset->addPoint("Sun\nApr 26", 16);

$Plot =& $Plotarea->addNew('bar', array(&$Dataset));
$Plot->setLineColor('gray');
$Plot->setFillColor('blue@0.2');

//$Plot =& $Plotarea->addNew('smooth_line', array(&$Dataset));
//$Plot->setLineColor('blue');
$Plot->setTitle('Number of Calls');

$DataPreprocessor_Fmt =& Image_Graph::factory('Image_Graph_DataPreprocessor_Formatted', '%02d');

$AxisX =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_X);
$AxisX->setAxisIntersection('min');

$AxisY =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_Y);
$AxisY->showLabel(IMAGE_GRAPH_LABEL_ZERO);
$AxisY->setDataPreprocessor($DataPreprocessor_Fmt);
$AxisY->setTitle('Number of Calls', 'vertical');

$Graph->setPadding(10);
$Graph->done();
?> 
