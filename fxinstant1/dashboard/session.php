<?php 
session_start();

	include '../db.php';
	include '../config.php';

	if (isset($_SESSION['email']) && isset($_SESSION['id'])) {
		$email = $_SESSION['email'];
		$query = mysqli_query($link, "SELECT * FROM users WHERE email = '$email' ");
		if (mysqli_num_rows($query) > 0) {
			$row = mysqli_fetch_assoc($query);

			$uemail = $row['email'];
			$username = $row['username'];
			$id = $row['id'];
			$fullname = $row['fullname'];
			$password = $row['password'];
			$phone = $row['phone'];
			$last_access = $row['last_access'];
			$date = $row['date'];
			$dbalance = $row['dbalance'];
			$mbalance = $row['mbalance'];
			$abonus = $row['abonus'];
			$refbalance = $row['refbalance'];
			$upgrade = $row['upgrade'];
			$verification = $row['verification'];
			$wpin = $row['wpin'];
		
			
			$refs = mysqli_query($link, "SELECT COUNT(id) as refids FROM users WHERE referral = '$username' ");
		    $row11 = mysqli_fetch_assoc($refs);
		    if($row11['refids'] != ""){
		    	$refals = $row11['refids'];
		    }else{
		    	$refals = "0";
			}
			
		}

	}else{
// 		header("location: ../signin.php?loginprompt");
		echo "<script>window.location.href = '../signin.php?loginprompt' </script>";
	}


?>