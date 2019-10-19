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

    if(!preg_match_all('/back\.arthur-moug\.in[^"\'\s]+\.[a-z]+/', $content, $return)) {

        return $content;

    }

    //$urlPattern = '/https:\\\/\\\/back.arthur-moug.in\\\/[a-zA-Z0-9\\\/-]+\.((jpg)|(png))/g';
    //obtenir toutes les images du fichier

    //comparer avec les images existantes
    $filename = "savedImages.txt";
    $imagesFolderPath = "../assets/images/";
    $finalPath = "arthur-moug.in/assets/images/";
    $return[1] = array();

    $savedImagesString= file_get_contents($filename);
    $receivedImages = array();

    if(!$savedImagesString) $savedImagesString = "[]";
    $savedImages = json_decode($savedImagesString);

    // scan toutes les images et télécharger les nouvelles
    foreach ($return[0] as $oldImgPath) {
        //print_r($img);
        //echo "\n";
        $newImgName = str_replace('back.arthur-moug.in\/wp-content\/uploads\/',"",$oldImgPath);
        $newImgName = str_replace('\/',"",$newImgName);
        $oldImgPath = str_replace('\\',"",$oldImgPath);
        $receivedImages[] = $newImgName;
        $newUrl = $imagesFolderPath . $newImgName;
        $finalUrl = $finalPath . $newImgName;

        //on cherche des images inconnues pour les télécharger
        if(!in_array($newImgName,$savedImages,true)){
            $imgContent = file_get_contents('https://' . $oldImgPath);

            //si l'image existe et que l'upload a bien eu lieu
            if($imgContent){

                if(file_put_contents($newUrl,$imgContent)) {
                    //echo "done\n";
                    $finalUrl = $finalPath . $newImgName;
                }
                else {
                    //echo "fail to fileput \n";
                    $finalUrl = $oldImgPath;
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
        if (!in_array($savedImage, $receivedImages, true)) {
            //echo "delete ".$savedImage."\n";
            $realpath = realpath($imagesFolderPath . $savedImage);
            if (is_writable($realpath)) unlink($realpath);

        }
    }

    //var_dump($return);

    //echo "\n\n\n";

    $old = $return[0];
    $new = $return[1];


    for($i = 0; $i<count($old);$i++){

        //echo "\n\n";

        $new[$i] = str_replace('/','\/',$new[$i]);

        //echo "i:".$i." [0][i]:'".$old[$i]."' -> [1][i]:'".$new[$i]."'\n";

        $newcontent = str_replace($old[$i],$new[$i],$content);

        if($newcontent == $content){
            //echo "pas de modification\n";
        }
        else {
            //echo "done\n";
            $content = $newcontent;
        }
    }


    //var_dump( $content );
    //echo "\n\n\n";

    return $content;
}

function RenderSingleContent($singleContent){


    $retour = array();

    /**
     * <article>
     *     <div class='titleContainer'>
     *         <h1>titre</h1>
     *         if(haveUrl) <a href='url'>url</a>
     *         if(haveThumbnail) <img  srcset  src>
     *     </div>
     *     if(UseExcerpt){
     *          <div class='excerpt show'>
     *              <p>excerpt</p>
     *              <button>more</button>
     *          <div>
     *          <div class='content hide'>
     *              content
     *          </div>
     *     } else {
     *          <div class='content show'>
     *              content
     *          </div>
     *     }
     *
     * </article>
     */
    foreach ($singleContent as $a){
        //$a = article
        $haveUrl =isset($a->x_metadata->{"url"});
        var_dump($a);
        $render = "<article><div class='titleContainer'><h2>" . $a->title->rendered . "</h2>";

        if($haveUrl){
            $render .= "<a href=" . $a->x_metadata->url . "'>" . $a->x_metadata->url . "</a>";
        }
        if($a->featured_media != 0){
            //$ts = thumbnails
            $ts = array(
                "media" =>array(
                    "url" => $a->x_featured_media,
                    "size" => 0
                ),
                "media_medium" => array(
                    "url" => $a->x_featured_media_medium,
                    "size" => 0
                ),
                "media_large" => array(
                    "url" => $a->x_featured_media_large,
                    "size" => 0
                ),
                "media_original" => array(
                    "url" => $a->x_featured_media_original,
                    "size" => 0
                )
            );
            var_dump($ts);
            $originalSplitedName = str_split(".", $ts["media_original"]["url"]);
            $originalName = $originalSplitedName[0] . "." . $originalSplitedName[1] . "." . $originalSplitedName[2];
            $extention = "." . $originalSplitedName[2];
            var_dump($originalSplitedName);
            var_dump($originalName);
            var_dump($extention);
            for($i = 0; $i < count($ts)-1;$i++) {
                $t = $ts[$i];
                //on cherche les dimensions en supprimant tout le contenu
                $shortUrl = str_replace($extention,"",str_replace($originalName,"",$t->url));
                if($shortUrl != ""){
                    $splitedUrl = str_split("-",$shortUrl);
                    $dimension = str_split("x",$splitedUrl[count($splitedUrl)-1]);
                    $t->size = $dimension[0];
                }
            }

            $render .= "<img alt='thumbnail de ". $a->title->rendered . "' srcset='";
            for($i = 0; $i < count($ts)-1;$i++) {
                $t = $ts[$i];
                if($t->size != 0){
                    $render .= $t->url . " " . $t->size . "w, ";
                }
            }
            $render .= "' src='" . $ts[count($ts)-1]->url . "'></img>";
        }

        $render .= "</div>";

        //$htmlContent = str_replace('\n',"<br>",trim($a->content->rendered));

        var_dump($render);
        echo "\n\n\n\n";
    }
    echo "\n\n\n\n\n\n\n\n";

    return $retour;
}

function AddRenderContent($classicContent){
    $rendered= array(
        "travaux" => RenderSingleContent($classicContent["travaux"]),
        "pages" => RenderSingleContent($classicContent["pages"]),
        "posts" => RenderSingleContent($classicContent["posts"]),
    );
    $classicContent["rendered"] = $rendered;
    return $classicContent;
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

        if ($travaux === null || $medias === null || $pages === null || $posts === null) {

            $content = file_get_contents($filename);

        }
        else {

            $classicContent =array(
                "travaux" => $travaux,
                "pages" => $pages,
                "media" => $medias,
                "posts" => $posts,
            );

            $classicContent = AddRenderContent($classicContent);

            $content = json_encode($classicContent);

            $content = FilterImages($content);

            /**
            file_put_contents($filename, $content);
            /**/
        }

    }
    else {

        $fileContent = file_get_contents($filename);
        $content = $fileContent;

    }
    return $content;
}

$jsonExport = GetContent();
//echo "\n\n\nRESULT\n\n";
echo $jsonExport;


