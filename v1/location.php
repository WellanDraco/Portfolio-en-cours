<?php 


// create a new cURL resource
$ch = curl_init();

// set URL and other appropriate options
curl_setopt($ch, CURLOPT_URL, 'https://smsapi.free-mobile.fr/sendmsg?user=19541329&pass=q6vJkpJQxGhuwG&msg='.urlencode($_GET['notif']));
curl_setopt($ch, CURLOPT_HEADER, 0);

// grab URL and pass it to the browser
curl_exec($ch);

// close cURL resource, and free up system resources
curl_close($ch);



?>