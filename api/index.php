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

    //$urlPattern = '/https:\\\/\\\/back.arthur-moug.in\\\/[a-zA-Z0-9\\\/-]+\.((jpg)|(png))/g';
    //obtenir toutes les images du fichier
    if(preg_match_all('/back\.arthur-moug\.in[^"\'\s]+\.[a-z]+/', $content, $return)) {
        //comparer avec les images existantes
        $filename = "savedImages.txt";
        $imagesFolderPath = "";
        $finalPath = "https://arthur-moug.in/api/";
        $return[1] = array();

        $savedImagesString= file_get_contents($filename);
        $receivedImages = array();

        if(!$savedImagesString) $savedImagesString = "[]";
        $savedImages = json_decode($savedImagesString);

        // scan toutes les images
        foreach ($return[0] as $oldImgPath) {
            //print_r($img);
            //echo "\n";
            $newImgName = str_replace('back.arthur-moug.in\/wp-content\/uploads\/',"",$oldImgPath);
            $newImgName = str_replace('\/',"",$newImgName);
            $oldImgPath = str_replace('\\',"",$oldImgPath);
            $receivedImages[] = $newImgName;
            $newUrl = $imagesFolderPath . $newImgName;
            $finalUrl ="";

            //on cherche des images inconnues pour les télécharger
            if(!in_array($newImgName,$savedImages,true)){
                $content = file_get_contents('https://' . $oldImgPath);

                //si l'image existe et que l'upload a bien eu lieu
                if($content){
                    echo "\n\n";
                    echo $newUrl . "\n";

                    $createdImage = fopen($newUrl,'W');

                    if($createdImage) {
                        if(fwrite($createdImage,$content) ) {
                            if(fclose($createdImage)) {
                                echo "done\n";
                                $finalUrl = $finalPath . $newImgName;
                            }
                            else {
                                echo "fail to close \n";
                                $finalUrl = $oldImgPath;
                            }
                        }
                        else {
                            echo "fail to write \n";
                            $finalUrl = $oldImgPath;
                        }
                    }
                    else {
                        echo "fail to create\ntest file_put";

                        if(file_put_contents($newUrl,$content)) {
                            echo "done\n";
                            $finalUrl = $finalPath . $newImgName;
                        }
                        else {
                            echo "fail to fileput \n";
                            $finalUrl = $oldImgPath;
                        }

                    }



                } else {
                    $finalUrl = $oldImgPath;
                }
            }

            //on met l'url finale dans un second tableau associé au premier
            $return[1][] = $finalUrl;

        }

        // on cherche les images qui ne servent plus à rien pour les supprimer
        foreach ($savedImages as $savedImage) {
            if(!in_array($savedImage,$receivedImages,true)){

                $realpath = realpath($imagesFolderPath . $savedImage);
                if(is_writable($realpath)) unlink($realpath);

            }
        }

        var_dump($return);
        echo "\n\n\n\n";
    }

    //var_dump( $content );
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
        //$medias = APICall("https://back.arthur-moug.in/wp-json/wp/v2/media");
        $posts = APICall("https://back.arthur-moug.in/wp-json/wp/v2/posts");

        if ($travaux === false || $pages === false || $posts === false) {

            $content = file_get_contents($filename);

        }
        else {

            $content = json_encode(array(
                "travaux" => $travaux,
                "pages" => $pages,
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
echo "\n\n\nRESULT\n\n";
echo $jsonExport;


