<?php
/**
 * Created by PhpStorm.
 * User: lucian
 * Date: 3/29/2016
 * Time: 11:48 AM
 */

// ===================
// = sales functions =
// ===================

/**
 * returns the entire HTML or XML document of the given file or url
 * @param $link - url to a tf theme page
 * @return DOMDocument ( the entire HTML or XML document ) or false on error
 */

function td_get_page($link){

    $dom = new DOMDocument();
    $check = @$dom->loadHTMLFile($link);

    if ($check === true) {
        return $dom;
    }

    return false;
}

/**
 * this function handles the html code of a themeforest.net theme page and it returns the number of sales
 * @param $dom DOMDocument - the HTML of a page
 * @return string|bool - the number of sales or false on error
 */

function td_get_sales($dom){

    $domxpath = new DOMXPath($dom);
    $filtered_tags = $domxpath->query("//strong[@class='sidebar-stats__number']");

    $tags = array();

    if (!empty($filtered_tags->length)) {
        foreach ($filtered_tags as $tag) {
            $tags[] = $tag;
        }
        $myTag = $tags[0]->nodeValue;
    }

    if (!empty($myTag)){
        $myTag = str_replace(',', '', $myTag);
        return intval($myTag) ;
    }

    return false;
}

/**
 * this function calculates and returns the difference between 2 themes sales given as parameters
 * @param $theme_1 - the sales no for the first theme
 * @param $theme_2 - the sales no for the second one
 * @return ...
 */

function td_calculate_sales ($theme_1, $theme_2) {
    $sales_difference = $theme_1 - $theme_2;

    return $sales_difference;
}

// ======================
// = messages functions =
// ======================

/**
 * intoarce string-ul cu data ultimei actualizari (primeste HTML - ul paginii)
 * @param $dom DOMDocument- HTML-ul paginii
 * @return bool - data ultimei actualizari sau false in caz de eroare
 */

function td_get_date($dom){

    $dom_document_xpath = new DOMXPath($dom);

    //intoarce o lista de noduri care se potrivesc expresiei XPath
    //..$nodes_list este un obiect care are o singura proprietate lenght ce contine numarul de noduri din lista si o metoda item
    //... http://php.net/manual/ro/class.domnodelist.php
    //... "The items in the NodeList are accessible via an integral index, starting from 0."

    $nodes_list = $dom_document_xpath->query("//td/time/@datetime");

    //citim nodurile intr-un array
    $nodes_array = array();
    foreach($nodes_list as $node) {
        $nodes_array[] = $node;
    }

    //verificam daca avem noduri in lista - daca citirea s-a facut cu succes
    if (!empty($nodes_array)) {
        //data ultimei actualizari luata din array cu indexul 1 - al doilea element ( data de pe themeforest)
        $last_update_date = $nodes_array[1]->nodeValue;
        return $last_update_date;
    }

    return false;
}

/**
 * this function checks if the update date has changed
 * @param $date - the date of the last theme update
 * @return bool - true if the date has changed and false otherwise
 */

function td_is_new_date($date){

    //citesc data ultimei actualizari din fisierul "date_chekc.txt"
    $date_chekc_file = 'date_chekc.txt';
    $stored_date = file_get_contents($date_chekc_file);

    // daca data de update s-a schimbat
    if ($date !== $stored_date) {
        // actualizam "date_chekc.txt" cu noua data (curenta) a ultimei actualizari
        file_put_contents($date_chekc_file, $date);
        return true;
    }

    return false;
}

/**
 * this function parses and checks the stored data for the current day of the year and if the maximum number of sent messages has not been reached...
 *  ...it sends messages and then it updates the number of sent messages for that day
 * functia asta trimite un mesajele in fucntie de limita de mesaje si ziua curenta
 * @param $message_text - continutul mesajului
 * @param array $numbers - phone numbers to which to send messages
 * @param $message_text - the text of the message
 * @return bool - false if the maximum number of sent messages is reached/exceeded
 */

function td_send_sms(array $numbers, $message_text){

    //citesc intru-un array continutul fisierului "messages_day_checks.txt"
    $messages_day_checks_file = 'messages_day_checks.txt';
    $file_array = parse_ini_file($messages_day_checks_file);

    //ziua curenta din an
    $day_of_the_year = date('z');

    //daca datele au fost deja initializate sau actualizate...
    if (!empty($file_array['Day #' . $day_of_the_year])) {
        //luam numarul de mesaje a zilei curente
        $number_of_sent_messages = $file_array['Day #' . $day_of_the_year];
    } else {
        //daca nu initializam numarul de mesaje trimise cu 0
        $number_of_sent_messages = 0;
    }

    if ($number_of_sent_messages < 20) {

        //trimite mesaje

        // Authorisation details.
        $username = "contact@tagdiv.com";
        $hash = "9cac90bc0d091179669c49c44d67700523ab4972";

        // api key - to limit message sending just on chosen ip's - http://api.txtlocal.com/docs for more info
        $apiKey = "wgHPlRVOp4U-TmrPcwfSmGPPTZzjZWgoWc1hPxkPAb";

        $textlocal = new Textlocal($username, $hash, $apiKey=false);

        $sender = 'tagDiv';
        //$test = true;
        $test = false;

        $textlocal->sendSms($numbers, $message_text, $sender, null, $test);

        //$response = $textlocal->sendSms($numbers, $message_text, $sender, null, $test);
        //print_r($response);

        // incremeanteaza numarul de mesaje
        $number_of_sent_messages++;

        // adaugam ziua si numarul de mesaje actualizate in fisierul "messages_day_checks.txt"
        $file_content = "Day #" . $day_of_the_year . "  = " . $number_of_sent_messages . "\n";
        file_put_contents($messages_day_checks_file, $file_content, FILE_APPEND);

        return true;
    }
    return false;
}

