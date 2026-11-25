<?php

@define('DB_SERVER', 'localhost');
@define('DB_USERNAME', getenv('FX_DB_USER') ?: 'YOUR_DB_USER');
@define('DB_PASSWORD', getenv('FX_DB_PASS') ?: 'YOUR_DB_PASSWORD');
@define('DB_NAME', getenv('FX_DB_NAME') ?: 'YOUR_DB_NAME');

 
/* Attempt to connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

?>
