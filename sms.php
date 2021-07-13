<?php
if(isset($_GET["msg"])){
    $url = "https://smsapi.free-mobile.fr/sendmsg?user=19541329&pass=19541329&msg=" . rawurlencode($_GET["msg"]);
    var_dump($url);
    //file_get_contents($url);

    $ch = curl_init();

    //Set the URL that you want to GET by using the CURLOPT_URL option.
    curl_setopt($ch, CURLOPT_URL, $url);

    //Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    //Execute the request.
    $data = curl_exec($ch);

    //Close the cURL handle.
    curl_close($ch);

}
?>