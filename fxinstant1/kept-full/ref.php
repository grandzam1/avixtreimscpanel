<?php 
	
	if (isset($_GET['ref'])) {
		$ref_user = $_GET['ref'];
		header("location: register.php?ref=$ref_user"); 
	}else{
		header("location: index.html");
	}

 ?>