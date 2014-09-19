<?php

require 'Slim/Slim.php';
require 'Lib/simple_html_dom.php';
require 'Lib/includes.php';

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

$app->get('/', function () {        
        echo "*hic*";
});

$app->get('/url/:type/:country', function ($type, $country) use ($app) {
    $response = requestArray($type, $country);
    //print_r($response);

    $jsonResponse = json_encode($response);

    //Create json filename based on date time of request
    $dateResponse = (new DateTime('America/New_York'))->format('Y-m-d H-i-s');

    //Create request folder if does not already exist
    $folder = "Data/" . $type . "/" . $country . "/";
    if (!file_exists($folder)) {
        mkdir($folder, 0777, true);
    }

    //Add json response to file
    $filename = $folder . $dateResponse . ".json";
    file_put_contents($filename, $jsonResponse);

    //creates array of filenames in "Data" folder
    $requestHistory = scandir($folder, 1);
    //removes all values from the array that are not json files
    foreach ($requestHistory as $key => $ll) { 
        if (strpos($ll,'.json') == false) {
            unset($requestHistory[$key]);
        }
    }
    //print_r($requestHistory);

    $mostRecent= 0;
    $now = time();

    //sort($requestHistory);
    print_r($requestHistory);

    /*
    foreach($requestHistory as $date){
        $date = substr($date, 0, -5); //remove json filename
        
      $curDate = strtotime($date);
      //echo $date . "<br />";
      if ($curDate > $mostRecent && $curDate < $now) {
         $mostRecent = $curDate;
         echo $mostRecent;
      }
    }*/

    /*
    if ( $a == $b ) {
        echo 'We are the same!'
    }*/

});

$app->get('/test', function () use ($app) {
    echo "test";
});

$app->run();
