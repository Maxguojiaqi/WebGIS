<?php

/*

PHP script for inclusion with the Web Map Application prototype. 
Developed by Andrew Roberts 2017, for Professor X. 

FirePHP console commands for logging php activity:
FB::log('Log Message');
FB::info('Info Message');
FB::warn('Warn Message');
FB::error('Error Message');
FB::trace('Simple Trace');

*/

// Include the FirePHP debugging script
require('../Script/FirePHPCore/fb.php');
$errMsg = "";


// Secure submited data against malicious script injection
function secure_input($data) {
    // strip unnecessary chars (extra space, tab, newline, etc)
    $data = trim($data);
    // remove backslashes (\)
    $data = stripslashes($data);
    // save as HTML escaped code --> any scripts in input data will not run
    $data = htmlspecialchars($data);
    return $data;
} // close secure_input()


// Write extent coordinates and error message to file (/temp) 
function writeToFile($array, $error) {
    $fileName = "temp/extent" . date("Y-m-dThisa") . ".txt";
    $newFile = fopen($fileName, "w");
    if (!$newFile){        
        return "*Hmmm, something went wrong with your file save..";
    }else{
        $txt = "New Extent: " . json_encode($array) . "\n" . "Error Msg: " . $error;    
        fwrite($newFile, $txt);
        fclose($newFile);
        return "Success! Your coords file was saved";
    }
} // close writeToFile   


// Fetch GeoTIF from GeoServer using cURL, save to file
function curlPostGeoServer($minX, $minY, $maxX, $maxY) {
    $url = "http://ulysses.agr.gc.ca:8080/geoserver/wcs";
    $post = '<?xml version="1.0" encoding="UTF-8"?>
        <GetCoverage version="1.0.0" service="WCS" 
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
        xmlns="http://www.opengis.net/wcs" 
        xmlns:ows="http://www.opengis.net/ows/1.1" 
        xmlns:gml="http://www.opengis.net/gml" 
        xmlns:ogc="http://www.opengis.net/ogc" 
        xsi:schemaLocation="http://www.opengis.net/wcs
        http://schemas.opengis.net/wcs/1.0.0/getCoverage.xsd">
          <sourceCoverage>Canada:Canada_SNDPPT_M_sl1_250m_ll</sourceCoverage>
          <domainSubset>
            <spatialSubset>
              <gml:Envelope srsName="EPSG:4326">
                <gml:pos>' . $minX . ' ' . $minY . '</gml:pos>
                <gml:pos>' . $maxX . ' ' . $maxY . '</gml:pos>
              </gml:Envelope>
              <gml:Grid dimension="2">
                <gml:limits>
                  <gml:GridEnvelope>
                    <gml:low>0 0</gml:low>
                    <gml:high>1208 560</gml:high>
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
    


    // Clean up the string
    // ** could make this a function    
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
        
    // Setup error check. Returned cURL data should be a GeoTIFF if successful.
    // If there's an error, GeoServer returns an XML. Here, we check for XML since
    // PHP can't check (easily) for validity (incase of corruption) of .tif.
    //
    // Create XML reader object and load in returned cURL data
    FB::log("working?");
    $reader = new XMLReader();
    $reader->xml($ch_result);
    FB::log($reader->read());
    // If XMLReader can read(returns TRUE), cURL data was XML and we send an error report
    if($reader->read()){
        // Write error report to error log, including returned XML
        $errMsg = "\n\n" . date("Y-m-d H:i:s") . " ERROR " . $_SERVER["REQUEST_TIME"] . "\n\t " . __FILE__ . "\n\t Request XML error: POST request returned without a valid GeoTIFF.\n\t Refer to XML for info:\n\t XML Response:\n\t url: {$url}\n\t xml:\n\t {$ch_result} ";
        file_put_contents("error/errorlog.txt", $errMsg, FILE_APPEND);
        return("*GeoTiff save failed - Contact Server Administrator");
    }else{
        // Apply time-stamp to file and save
        $fileName = "../PHP/temp/geotiff". date("Y-m-dThisa") . ".tif";
        file_put_contents($fileName, $ch_result);
        return($fileName);
    }   
} // close curlPostGeoServer()

// calling on python!
// function runSubProcess(){
//     $result = exec('python ../Script/helloworld.py "Hello from python!"');
//     if($result){
//         return($result);
//     }else{
//         return("*Sub Process Failed");
//     }
// }

// This si for testing purposes, see if the plumbing works
function curlTest(){
    
    $xmlString =     '<?xml version="1.0" encoding="UTF-8"?><wps:Execute version="1.0.0" service="WPS" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.opengis.net/wps/1.0.0" xmlns:wfs="http://www.opengis.net/wfs" xmlns:wps="http://www.opengis.net/wps/1.0.0" xmlns:ows="http://www.opengis.net/ows/1.1" xmlns:gml="http://www.opengis.net/gml" xmlns:ogc="http://www.opengis.net/ogc" xmlns:wcs="http://www.opengis.net/wcs/1.1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xsi:schemaLocation="http://www.opengis.net/wps/1.0.0 http://schemas.opengis.net/wps/1.0.0/wpsAll.xsd"><ows:Identifier>ras:PolygonExtraction</ows:Identifier><wps:DataInputs><wps:Input><ows:Identifier>data</ows:Identifier><wps:Reference mimeType="image/tiff" xlink:href="http://geoserver/wcs" method="POST"><wps:Body><wcs:GetCoverage service="WCS" version="1.1.1"><ows:Identifier>Canada:PEI</ows:Identifier><wcs:DomainSubset><ows:BoundingBox crs="http://www.opengis.net/gml/srs/epsg.xml#4326"><ows:LowerCorner>-64.450018488 45.886673304</ows:LowerCorner><ows:UpperCorner>-61.933352224 47.053339784</ows:UpperCorner></ows:BoundingBox></wcs:DomainSubset><wcs:Output format="image/tiff"/></wcs:GetCoverage></wps:Body></wps:Reference></wps:Input><wps:Input><ows:Identifier>insideEdges</ows:Identifier><wps:Data><wps:LiteralData>false</wps:LiteralData></wps:Data></wps:Input><wps:Input><ows:Identifier>nodata</ows:Identifier><wps:Data><wps:LiteralData>0</wps:LiteralData></wps:Data></wps:Input></wps:DataInputs><wps:ResponseForm><wps:RawDataOutput mimeType="application/json"><ows:Identifier>result</ows:Identifier></wps:RawDataOutput></wps:ResponseForm></wps:Execute>';
    
    $ch = curl_init("http://www.example.com/");
    $fp = fopen("temp/example_homepage.txt", "w");
    
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
    FB::log("something");
    $string = "";
    $xml = new XMLReader();
    FB::trace($xml);
    $xml->xml($string);
    FB::trace($xml->read());
    return("ran");
}
 

/******************  Process (round coords to 4 decimal places)   *********************/
/******************  Process (write coords to file)               *********************/
/******************  Process (tiff to file)                       *********************/



// Check that all input is present, numeric, and safe, then trim and write
// to an array
//
if (empty($_REQUEST["minX"]) || empty($_REQUEST["minY"]) || empty($_REQUEST["maxX"]) || empty($_REQUEST["maxY"]) || !is_numeric($_REQUEST["minX"]) || !is_numeric($_REQUEST["minY"]) || !is_numeric($_REQUEST["maxX"]) || !is_numeric($_REQUEST["maxY"])) {
    
    // Throws error if inputs aren't entered or numeric. 
    $errMsg = "*Oops, all fields are required and must be WGS84 coordinates.";
    
} else{
    
    // Validate and secure input data
    $minX = secure_input($_REQUEST["minX"]);
    $minY = secure_input($_REQUEST["minY"]);
    $maxX = secure_input($_REQUEST["maxX"]);
    $maxY = secure_input($_REQUEST["maxY"]);
    
    // Write data to an array, then adjust decimal places
    $coords = array($minX, $minY, $maxX, $maxY);
    $length = count($coords);
    for ($x = 0; $x < $length; $x++) {
        $coords[$x] = number_format((float)$coords[$x], 4, '.', '');
    }
    
    // Try write coords to file, send msg on success 
    $fileStatus = writeToFile($coords, $errMsg);    
    
    // Try curlPostGeoServer
    $postStatus = curlPostGeoServer($minX, $minY, $maxX, $maxY);
        
    // Try run a sub process
    $pythonStatus = runSubProcess();
}



/**********************  Return (corrected coordiantes)  ************************/



// Send the secure, processed data back to be AJAXed in
echo json_encode(array($errMsg, $coords, $fileStatus, $postStatus, $pythonStatus));

?>














