<?php

$theme_to_check=file_get_contents('http://themeforest.net/item/sahifa-responsive-wordpress-news-magazine-newspaper-theme/2819356');
$newspaper = file_get_contents('http://themeforest.net/item/newspaper/5489609');

function sales($theme_to_check) {

    $mystring = '<span class="sidebar-stats__icon"><i class="e-icon -icon-cart"></i></span>';
    $pos = strpos ( $theme_to_check , $mystring );
    $sub_string = substr ( $theme_to_check , $pos , 90 );
    preg_match_all('!\d+([\d,]?\d)*(\.\d+)?!', $sub_string, $matches);
    $result = str_replace( ",", "", $matches[0] );

return $result;

}

$newspaper_sales=sales($newspaper);
$other_theme_sales=sales($theme_to_check);

$diff=$other_theme_sales[0] - $newspaper_sales[0];
echo $diff;