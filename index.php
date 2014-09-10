<?php

require 'Slim/Slim.php';
require 'Lib/simple_html_dom.php';
require 'Lib/includes.php'
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

$app->get('/', function () {        
        echo "*hic*";
});

$app->get('/url/:type/:country', function ($type, $country) use ($app) {
    $response = requestArray($type, $country);
    print_r($response);

});

$app->get('/test', function () use ($app) {
    echo "test";
});

function requestArray($type, $country) {
    if (!$type){
        $type = "*";
    }
    if (!$country){
        $country = "*";
    }
    echo $type. '<br>';
    echo $country. '<br>';

    $LIQUOR_TYPE_SHORT_="Beer";
    $PRODUCING_CNAME = "Belgium";
    $resultsPerPage = "100000";
    $searchURL = "http://foodanddrink.ca/lcbo-ear/lcbo/product/searchResults.do?
        STOCK_TYPE_NAME=All
        &ITEM_NAME=
        &KEYWORDS=
        &ITEM_NUMBER=
        &productListingType=
        &LIQUOR_TYPE_SHORT_=" . $type . "
        &CATEGORY_NAME=*
        &SUB_CATEGORY_NAME=*
        &PRODUCING_CNAME=" . $country . "
        &PRODUCING_SUBREGION_N=
        &PRODUCING_REGION_N=*
        &UNIT_VOLUME=*
        &SELLING_PRICE=*
        &LTO_SALES_CODE=
        &VQA_CODE=
        &KOSHER_CODE=
        &VINTAGES_CODE=
        &VALUE_ADD_SALES_CO=
        &AIR_MILES_SALES_CO=
        &SWEETNESS_DESCRIPTOR=
        &VARIETAL_NAME=
        &WINE_STYLE=
        &language=EN
        &style=+LCBO.css
        &page=1
        &action=result
        &sort=sortedProduct
        &order=1
        &resultsPerPage=" . $resultsPerPage;
    $searchURL = preg_replace('/\s+/', '', $searchURL); //remove spaces, not sure where coming from
    echo $searchURL. '<br>'. '<br>';

    // Create DOM from URL or file
    $html = file_get_html($searchURL);

    $inventory = array();
 
    foreach($html->find('td[class=main_font]') as $section) {

        $item = array();
        //$item['col1'] = $article->find('a[class=item-details-col1]')->plaintext;
        foreach ($section->find('a[class=item-details-col1]') as $col1 ) {
            $itemName = $col1->plaintext;
            //echo $element->plaintext . '<br>';
        }
        //echo $item['col1'];

        //$item['col2'] = $article->find('a[class=item-details-col2]', 0)->plaintext;
        foreach ( $section->find('a[class=item-details-col2]') as $col2 ) {
            $itemDescription = $col2->plaintext;
            $itemDescription = str_replace("LCBO", "|", $itemDescription); //converts LCBO in string to delimiter "|"
            $itemDescriptionArray = explode('|', $itemDescription); //generated array from string
            $itemDescriptionArray = array_map('trim', $itemDescriptionArray); //removes whitespace from ends of values in array
            if(!isset($itemDescriptionArray[5])){
                $itemDescriptionArray[5] = "NOT DISCONTINUED";
            }
            array_push($itemDescriptionArray, $itemName);
            //$item['description'] = $itemDescriptionArray;

                $itemKeys = array(
                    "country",
                    "company",
                    "lcboID",
                    "size",
                    "price",
                    "availability",
                    "name"
                );
                /*foreach($item['description'] as $key=>$value) {
                    $itemKeys[$key] = $value[];
                }*/
                
                //$item['description'] = array_combine($itemKeys, array_values($item['description']));
                $inventory[] = array_combine($itemKeys, $itemDescriptionArray);
                
                //echo $element->plaintext . '<br>';
        }
        //if(isset($item['description'])){
        //    $inventory[] = $item;
        //        
        //}
    }
    
    return $inventory;
}


$app->run();
