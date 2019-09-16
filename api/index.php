<?php

header("Content-Type: application/json; charset=UTF-8");
$url = "https://back.arthur-moug.in/wp-json/wp/v2/travaux";
$url2 = "https://back.arthur-moug.in/wp-json/wp/v2/pages";

function APICall($url){
    //https://support.ladesk.com/061754-How-to-make-REST-calls-in-PHP
    $curl = curl_init($url);
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    $curl_reponse = curl_exec($curl);
    if($curl_reponse === false) {
        $retour = false;
    } else {
        $decoded = json_decode($curl_reponse);
        if(isset($decoded->response->status) && $decoded->response->status == 'ERROR'){
            $retour = false;
        }
        else {
            $retour = $decoded;
        }
    }
    curl_close($curl);
    return $retour;
}

function GetContent($url,$url2){
    $filename = "backup.txt";
    session_start();

    var_dump($_GET);

    if(!$_SESSION['FirstLog'] || isset($_GET['update'])) {
        echo "updated";
        $_SESSION['FirstLog'] = true;


        $fileContent = file_get_contents($filename);
        $content = $fileContent;

    }
    else {

        $travaux = APICall($url);
        $pages = APICall($url2);

        if ($travaux === false || $pages === false) {

            $content = file_get_contents($filename);

        } else {

            $content = json_encode(array("travaux" => $travaux, "pages" => $pages));
            file_put_contents($filename, $content);

        }

    }
    return $content;
}

$jsonExport = GetContent($url,$url2);

//echo $jsonExport;


