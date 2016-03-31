<?php
/**
 * Created by PhpStorm.
 * User: lucian
 * Date: 3/10/2016
 * Time: 11:41 AM
 */

$theme_to_check = 'http://themeforest.net/item/sahifa-responsive-wordpress-news-magazine-newspaper-theme/2819356';
$newspaper = 'http://themeforest.net/item/newspaper/5489609';

function sales($link){

    $dom = new DOMDocument;
    @$dom->loadHTMLFile($link);

    $tagName = 'strong';
    $className = 'class';
    $classValue = 'sidebar-stats__number';

    $domxpath = new DOMXPath($dom);
    $filtered_tags = $domxpath->query("//$tagName" . '[@' . $className . "='$classValue']");

    //print_r($filtered_tags);
    $tags = array();

    if (!empty($filtered_tags->length)) {
        foreach ($filtered_tags as $tag) {
            $tags[] = $tag;
        }

        $myTag = $tags[0]->nodeValue;
    }

   if (!empty($myTag)){
        $myTag = str_replace(',', '', $myTag);
    } else {
        $myTag = '';
    }

    //$myTag = str_replace(',', '', $myTag);

    return $myTag;
}

$newspaper_sales = sales($newspaper);
$other_theme_sales = sales($theme_to_check);

if (!empty($newspaper_sales) && !empty($other_theme_sales)) {
    $diff = $other_theme_sales - $newspaper_sales;
    echo $diff . PHP_EOL;
} else {
    echo "nu avem un rezultat";
}