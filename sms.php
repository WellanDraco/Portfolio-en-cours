<?php
if(isset($_GET["msg"]))
    http_get("https://smsapi.free-mobile.fr/sendmsg?user=19541329&pass=19541329&msg=" . $_GET["msg"]);
?>