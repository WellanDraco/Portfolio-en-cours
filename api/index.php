<?php
/**/
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
/**/
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
        //var_dump($a);
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
            //var_dump($ts);
            $originalSplitedName = explode('.', $ts["media_original"]["url"]);
            $originalName = $originalSplitedName[0] . "." . $originalSplitedName[1] . "." . $originalSplitedName[2];
            $extention = "." . $originalSplitedName[count($originalSplitedName)-1];
            //var_dump($originalName);
            //var_dump($extention);
            for($i = 0; $i < count($ts)-1;$i++) {
                $t = $ts[array_keys($ts)[$i]];

                //on cherche les dimensions en supprimant tout le contenu
                $shortUrl = str_replace($extention,"",str_replace($originalName,"",$t["url"]));
                if($shortUrl != ""){
                    $splitedUrl = explode("-",$shortUrl);
                    $dimension = explode("x",$splitedUrl[count($splitedUrl)-1]);
                    $t["size"] = $dimension[0];
                }
                $ts[array_keys($ts)[$i]] = $t;
            }

            $render .= "<img alt='thumbnail de ". $a->title->rendered . "' srcset='";
            for($i = 0; $i < count($ts)-1;$i++) {

                $t = $ts[array_keys($ts)[$i]];
                //var_dump($t);
                if($t["size"] != 0){
                    $render .= $t["url"] . " " . $t["size"] . "w, ";
                }
            }
            $render .= "' src='" . $ts["media_original"]["url"] . "'></img>";
        }

        $render .= "</div>";

        $htmlContent = str_replace("\n\n","\n<br>",trim($a->content->rendered));
        $useExcerpt = (isset($a->excerpt->rendered) && ($htmlContent != trim($a->excerpt->rendered)));

        if($a->slug == "contact"){
            $htmlContent .= "<ul>";
            $contacts = $a->x_metadata;
            foreach( $contacts as $ckey => $cvalue){
                //si la clé ne contient pas d'underscore
                if(strpos($ckey,"_") === false){
                    if($ckey == "email") $cvalue = "mailto:".$cvalue;
                    if($ckey == "téléphone") $cvalue = "tel:".$cvalue;

                    $htmlContent.= "<li class='".$ckey."'><a href='".$cvalue."'>".$cvalue."</a></li>";
                }
            }
            $htmlContent .= "</ul>";
        }

        if($useExcerpt){

            $render .= "<div class='excerpt show'>" . trim($a->excerpt->rendered) . "<button>En savoir plus</button></div>";
            $render .= "<div class='content hide'><div class='inner'>" . $htmlContent . "</div><button>Réduire</button></div>";
        }
        else {
            $render .= "<div class='content show'>" . $htmlContent . "</div>";
        }

        $render .= "</article>";

        $retour[] = $render;
    }

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

function RenderPage($content){
    $templatename ="../template.html";
    $filename = "../index.html";
    $fileContent = file_get_contents($templatename);
    //var_dump( $content);
    /**/
    $contact = $content->rendered->pages[0];
    $presentation = $content->rendered->pages[1];
    $travaux = $content->rendered->travaux;

/*

    //VRContact
    $splitedFileContent = explode("<!--VRCONTACT PHPMARKER-->", $fileContent);
    //var_dump($splitedFileContent);
    if(count($splitedFileContent) != 1){
        $fileContent = $splitedFileContent[0];
        $fileContent .= "<a-entity htmlembed mixin='FocusCamera'>".$contact."</a-entity>";
        $fileContent .= $splitedFileContent[1];
    }

    //VRpresentation
    $splitedFileContent = explode("<!--VRPRESENTATION PHPMARKER-->", $fileContent);
    if(count($splitedFileContent)!= 1){
        $fileContent = $splitedFileContent[0];
        $fileContent .= "<a-entity htmlembed mixin='FocusCamera'>".$presentation."</a-entity>";
        $fileContent .= $splitedFileContent[1];
    }



*/


    //Contact
    $splitedFileContent = explode("<!--CONTACT PHPMARKER-->", $fileContent);
    //var_dump($splitedFileContent);
    if(count($splitedFileContent) != 1){
        $fileContent = $splitedFileContent[0];
        $fileContent .= $contact;
        $fileContent .= $splitedFileContent[1];
    }
    //var_dump($fileContent);

    // presentation
    $splitedFileContent = explode("<!--PRESENTATION PHPMARKER-->", $fileContent);
    if(count($splitedFileContent)!= 1){
        $fileContent = $splitedFileContent[0];
        $fileContent .= $presentation;
        $fileContent .= $splitedFileContent[1];
    }

    // Travaux
    $splitedFileContent = explode("<!--PROJECTS PHPMARKER-->", $fileContent);
    //ar_dump($splitedFileContent);
    if(count($splitedFileContent)!= 1) {
        $fileContent = $splitedFileContent[0];
        foreach ($travaux as $travail) {
            $fileContent .= $travail;
        }
        $fileContent .= $splitedFileContent[1];
    }

    //var_dump($fileContent);

    //var_dump($fileContent);
    file_put_contents($filename,$fileContent);
    /**/
};

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

        if ($travaux === false || $medias === false || $pages === false || $posts === false) {

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

            /**/
            file_put_contents($filename, $content);
            /**/
            RenderPage(json_decode($content));
        }

    }
    else {

        $fileContent = file_get_contents($filename);
        $content = $fileContent;

    }
    return $content;
}

$jsonExport = GetContent();
//echo "<br><br><br><br><br>RESULT<br><br><br><br><br>";
/**/
echo $jsonExport;
/**/

