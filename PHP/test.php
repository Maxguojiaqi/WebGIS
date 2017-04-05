<?php
$post = simplexml_load_file("info.xml") or die ("Error")
$url = "http://localhost:8080/geoserver/wcs";

$post = str_replace("\r", "", $post);
$post = str_replace("\n", "", $post);
$post = str_replace("\t", "", $post);
$post = str_replace("> <", "><", $post);

// Setup the headers and post options, then execute curlPOST
$ch = curl_init($url);    
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));  // set all the transfer options for the cURL
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$ch_result = curl_exec($ch);
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
FB::info($contentType);
curl_close($ch);

?>