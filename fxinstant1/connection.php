<?php
$link = mysqli_connect("localhost", getenv('FX_DB_USER') ?: 'YOUR_DB_USER', getenv('FX_DB_PASS') ?: 'YOUR_DB_PASSWORD', getenv('FX_DB_NAME') ?: 'YOUR_DB_NAME');
