<?php
if(isset($_GET["msg"])){
    $url = "https://smsapi.free-mobile.fr/sendmsg?user=19541329&pass=19541329&msg=" . urlencode($_GET["msg"]);
    var_dump($url);
    http_get($url);
}
?>