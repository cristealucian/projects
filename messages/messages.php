<?php

/*
 *  structura proiect:
 * -- td_get_page($link) - intoarce html-ul pagini
 * -- td_get_sales($html) - intoarce numarul de sell-uri (primeste HTML)
 * -- td_get_date($html) - intoarce string-ul cu data ultimei modificari (primeste HTML)
 * -- td_is_new_date($date) - functia asta verifica daca e noua data sau e una veche
 * -- td_send_sms($number, $text) - trimite un mesaj (aici e si limita de mesaje implementata)
 */

/**
 *  this includes and evaluates the textlocal class file (functionality required for sending messages) and includes the functions file
 */

require('textlocal.class.php');
include "functions.php";

/**
==============================================================================================================================
 * =========================================== GET SALES =====================================================================
 * This part of code evaluates and, on execution, it displays the difference of sales between 2 given themeforest themea (pages)
==============================================================================================================================
 */

$theme_to_check = 'http://themeforest.net/item/sahifa-responsive-wordpress-news-magazine-newspaper-theme/2819356';
$newspaper = 'http://themeforest.net/item/newspaper/5489609';

// ..load the newspaper theme tf page..
$newspaper_theme_page = td_get_page($newspaper);

if ($newspaper_theme_page === false) {
    echo "error";
    die;
}

//..and another theme
$other_theme_page = td_get_page($theme_to_check);

if ($other_theme_page === false) {
    echo "error";
    die;
}

// ..render the dom and get the sales no.
    $np_sales = td_get_sales($newspaper_theme_page);

//.. other theme sales
    $other_theme_sales = td_get_sales($other_theme_page);

if ($np_sales === false || $other_theme_sales === false) {
    echo "error";
    die;
}

echo td_calculate_sales($np_sales, $other_theme_sales);

/**
==============================================================================================================================
 * =========================================== SEND MESSAGES =================================================================
 * This code sends messages based on the last update date of a tf theme ( page )!!!
==============================================================================================================================
*/

//verificam daca avem noduri in lista - daca citirea s-a facut cu succes
$last_update_date = td_get_date($other_theme_page);

if ($last_update_date === false) {
    echo "error";
    die;
}

//verificam daca data s-a schimbat
$date_chekc_status = td_is_new_date ($last_update_date);

//daca data este schimbata se trimit mesaje
if ($date_chekc_status == true) {
    td_send_sms (array(40740901964 ),'message text here!');
}