<?php

function response_decode($D,$K){
    $D = base64_decode($D);
    for($i=0;$i<strlen($D);$i++){
        $c = $K[$i+1&15];
        $D[$i] = $D[$i]^$c;
    }
    var_dump(gzdecode($D));
}

function request_decode($D,$K){
    $D = base64_decode(urldecode($D));
    for($i=0;$i<strlen($D);$i++){
        $c = $K[$i+1&15];
        $D[$i] = $D[$i]^$c;
    }
    var_dump(gzdecode($D));
}

$response_data = 'LbptyjdmMWI4ZketfMqs+Pt4UU45UAFSykkfUX0RSRxD/S6FNWbN6MfnLmIZYw==';
$request_data = 'LbptyjdmMWI4ZX+sfppKv+H1UtwFXBhmsaLV5NGMPKGVI40opG7QeTRey+0r6rrdJgH8rTma25k12SWS4sHrI0zgKDN1H7Kxyr8CrFKF8uA9Y0WvyVPfythrPeoea54YmZSVcRnNjdMMQ==';
$key = '421eb7f1b8e4b3cf';

request_decode($request_data, $key);
response_decode($response_data, $key);

?>