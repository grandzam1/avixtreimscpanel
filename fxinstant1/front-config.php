<?php
include("connection.php");
$fetch="SELECT * FROM site_details";
$query=mysqli_query($link, $fetch);
$row=mysqli_fetch_array($query);
$site_name=$row["site_name"];
$email=$row["email"];
$address=$row["address"];
$logo=$row["logo"];