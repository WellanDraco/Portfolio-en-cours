<?php
//header("Content-Type: application/json; charset=UTF-8");
$url = "https://back.arthur-moug.in/wp-json/wp/v2/travaux";
$url2 = "https://back.arthur-moug.in/wp-json/wp/v2/pages";

function APICall($url){
    //https://support.ladesk.com/061754-How-to-make-REST-calls-in-PHP
    $curl = curl_init($url);
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    $curl_reponse = curl_exec($curl);
    if($curl_reponse === false) {
        $retour = curl_getinfo($curl);
    } else {
        $retour = json_decode($curl_reponse);
    }
    curl_close($curl);
    return $retour;
}

var_dump(APICall($url));


