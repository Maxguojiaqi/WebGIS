<?php

require('../SCRIPT/firephp-master/lib/FirePHPCore/fb.php');



// '-A /var/www/html/index/temp/soil_sand_clipped.tif -B /var/www/html/index/temp/soil_clay_clipped.tif --outfile=raster_result.tif --calc="A+B"'

function runSubProcess()

{

    // echo $temp;
    $result = shell_exec('python3 ../SCRIPT/python/rastercalc.py');
    $result = shell_exec('python3 ../SCRIPT/python/raster_calc.py '.'-A /var/www/html/index/temp/soil_sand_clipped.tif -B /var/www/html/index/temp/soil_clay_clipped.tif --outfile=/var/www/html/index/temp/sand_clay.tif --calc="A+B"');
    $result = shell_exec('python3 ../SCRIPT/python/raster_calc.py '.'-A /var/www/html/index/temp/AOI_mask.tiff -B /var/www/html/index/temp/sand_clay.tif --outfile=/var/www/html/index/temp/silt.tif --calc="A-B"');


    if ($result) 
    {
        echo("done");
    }

    else
    {
        echo("*Sub Process Failed");
    }
}


$pythonStatus = runSubProcess();


?>