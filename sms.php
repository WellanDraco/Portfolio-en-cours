<?php
if(isset($_GET["msg"])){
    $url = "https://smsapi.free-mobile.fr/sendmsg?user=19541329&pass=19541329&msg=" . rawurlencode($_GET["msg"]);
    var_dump($url);
    file_get_contents($url);

}
?>