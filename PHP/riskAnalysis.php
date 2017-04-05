<?php

require('../SCRIPT/firephp-master/lib/FirePHPCore/fb.php');





function runSubProcess()

{

    $riskArray = $_REQUEST["riskArray"];         
    FB::info($riskArray);
    $temp = json_encode($riskArray);

    // echo $temp;
    $result = shell_exec('python3 ../SCRIPT/python/riskAnalysis.py ' ."'".$temp."'");
    FB::info($result);
    settype($result, "integer");
    FB::info($result);
    $type = gettype($result);
    FB::info($type);

    if ($result >= 40 ) 
    {
        return("It is likely worth spraying!");
    }
    else if ($result < 40)
    {
        return ("Not worth spraying!");
    }
    else
    {
        return("*Sub Process Failed");
    }
}


$pythonStatus = runSubProcess();
echo json_encode($pythonStatus);

?>