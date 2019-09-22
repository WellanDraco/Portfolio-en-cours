<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$url = "https://back.arthur-moug.in/wp-json/wp/v2/travaux";
$url2 = "https://back.arthur-moug.in/wp-json/wp/v2/pages";
$url3 = "https://back.arthur-moug.in/wp-json/wp/v2/media";
$url4 = "https://back.arthur-moug.in/wp-json/wp/v2/posts";

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

function FilterImages($content){

    $urlPattern = '/https:\\\/\\\/back.arthur-moug.in\\\/[a-zA-Z0-9\\\/-]+\.((jpg)|(png))/g';
    preg_match_all('\/back/', $content, $return);
    print_r($return);


    echo "\n\n\n";
    var_dump( $content );
    return $content;
}

function GetContent(){
    $filename = "backup.txt";

    /**
     * Deux méthodes existent pour obtenir le contenu :
     *
     *  1 : aller chercher le tout auprès de l'API wordpress
     *      + Est à jour et update l'option 2
     *      - Lent et risque de rater
     *  2 : aller chercher le contenu prémaché dans le fichier de backup
     *      + fiable et rapide
     *      - peut ne pas etre à jour
     *
     * l'option 1 sera uniquement utilisée par des mises à jour serveur
     */


    if(isset($_GET['update'])) {

        $travaux = APICall("https://back.arthur-moug.in/wp-json/wp/v2/travaux");
        $pages = APICall("https://back.arthur-moug.in/wp-json/wp/v2/pages");
        $medias = APICall("https://back.arthur-moug.in/wp-json/wp/v2/media");
        $posts = APICall("https://back.arthur-moug.in/wp-json/wp/v2/posts");

        if ($travaux === false || $pages === false || $medias === false || $posts === false) {

            $content = file_get_contents($filename);

        }
        else {

            $content = json_encode(array(
                "travaux" => $travaux,
                "pages" => $pages,
                "medias" => $medias,
                "posts" => $posts,
            ));

            $content = FilterImages($content);

            file_put_contents($filename, $content);

        }

    }
    else {

        $fileContent = file_get_contents($filename);
        $content = $fileContent;

    }
    return $content;
}

$jsonExport = GetContent();

//echo $jsonExport;


