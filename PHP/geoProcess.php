<?php

// ob_strart();
// $firephp = FirePHP::getInstance(true);
// $todays_date = date(‘l jS of F Y h:i:s A‘);
// $firephp->log($todays_date, ‘Date‘);
// FB::info('Info message');
// FB::warn('Warn message');
// FB::error('Error message');
// FB::trace('Simple Trace');

require('../SCRIPT/firephp-master/lib/FirePHPCore/fb.php');


// FB::info(simplexml_load_file("info.xml"));


if (empty($_REQUEST["minX"]) || empty($_REQUEST["minY"]) || empty($_REQUEST["maxX"]) || empty($_REQUEST["maxY"]) || !is_numeric($_REQUEST["minX"]) || !is_numeric($_REQUEST["minY"]) || !is_numeric($_REQUEST["maxX"]) || !is_numeric($_REQUEST["maxY"]))
{
    
    // Throws error if inputs aren't entered or numeric. 
    $errMsg = "*Oops, all fields are required and must be WGS84 coordinates.";
    
} else

{
    
	$minX = $_REQUEST['minX'];
	$minY = $_REQUEST['minY'];
	$maxX = $_REQUEST['maxX'];
	$maxY = $_REQUEST['maxY']; 
        
}



// FB::info(gettype($minX));
FB::info($minX);
FB::info($minY);
FB::info($maxX);
FB::info($maxY);


// calling on python!

function PostGeoServer($minX,$minY,$maxX,$maxY)
{
	FB::trace("function worked");


 	$post = '<?xml version="1.0" encoding="UTF-8"?><GetCoverage version="1.0.0" service="WCS" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.opengis.net/wcs" xmlns:ows="http://www.opengis.net/ows/1.1" xmlns:gml="http://www.opengis.net/gml" xmlns:ogc="http://www.opengis.net/ogc" xsi:schemaLocation="http://www.opengis.net/wcs http://schemas.opengis.net/wcs/1.0.0/getCoverage.xsd">
  <sourceCoverage>PEI:PEI_CECSOL_M_sl1_250m_ll</sourceCoverage>
  <domainSubset>
    <spatialSubset>
      <gml:Envelope srsName="EPSG:4326">
        <gml:pos>-63.19866022357827 46.11814020973505</gml:pos>
        <gml:pos>-62.72075495014077 46.41477106911005</gml:pos>
      </gml:Envelope>
      <gml:Grid dimension="2">
        <gml:limits>
          <gml:GridEnvelope>
            <gml:low>0 0</gml:low>
            <gml:high>229 142</gml:high>
          </gml:GridEnvelope>
        </gml:limits>
        <gml:axisName>x</gml:axisName>
        <gml:axisName>y</gml:axisName>
      </gml:Grid>
    </spatialSubset>
  </domainSubset>
  <output>
    <crs>EPSG:4326</crs>
    <format>GeoTIFF</format>
  </output>
</GetCoverage>';

 	FB::info(gettype($post));

	FB::log($post);
	
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


	$reader = new XMLReader();
	$reader->xml(ch_result);
	if ($reader->read())
	{
		$errMsg = "\n\n" . date("Y-m-d H:i:s") . " ERROR " . $_SERVER["REQUEST_TIME"] . "\n\t " . __FILE__ . "\n\t Request XML error: POST request returned without a valid GeoTIFF.\n\t Refer to XML for info:\n\t XML Response:\n\t url: {$url}\n\t xml:\n\t {$ch_result} ";
        file_put_contents("error/errorlog.txt", $errMsg, FILE_APPEND);
        return("this is an error save tiff file");
	}

	else
	{
		$filesave = "../temp/data.tif";
		$filedown = "../temp/silt.tif";
		file_put_contents($filesave, $ch_result);
		return ($filedown);
	}
}


$postStatus = PostGeoServer($minX,$minY,$maxX,$maxY);

// FB::info();
echo json_encode($postStatus);



?>