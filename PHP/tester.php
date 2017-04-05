<?php

require('../SCRIPT/firephp-master/lib/FirePHPCore/fb.php');
$minX = $_REQUEST['minX'];
$minY = $_REQUEST['minY'];
$maxX = $_REQUEST['maxX'];
$maxY = $_REQUEST['maxY']; 


FB::info($minX);
FB::info($minY);
FB::info($maxX);
FB::info($maxY);
$url_cropping_history = 'http://127.0.0.1:8080/geoserver/ows?service=WCS&version=2.0.1&request=GetCoverage&CoverageId=manitoba:84_proj&subset=Long('.$minX.','.$maxX.')&subset=Lat('.$minY.','.$maxY.')';

// Setup the headers and post options, then execute curlPOST  &subset=Long('.$minX.','.$maxX.')&subset=Lat('.$minY.','.$maxY.')
$ch_soil = curl_init($url_cropping_history);    

curl_setopt($ch_soil, CURLOPT_RETURNTRANSFER, 1);

$ch_result_cropping_history = curl_exec($ch_soil);
$contentType = curl_getinfo($ch_soil, CURLINFO_CONTENT_TYPE);

// FB::info($contentType);
curl_close($ch_soil);

$filesave_cropping_history = "../temp/cropping_history.tif";
file_put_contents($filesave_cropping_history, $ch_result_cropping_history);

?>