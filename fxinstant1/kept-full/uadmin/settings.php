<?php 
$page = "Settings";
	include 'header.php';


	$msg = "";

	if (isset($_POST['save'])) {
		$site_name = test_input($_POST['site_name']);
		$site_email = test_input($_POST['site_email']);
		$site_url = test_input($_POST['site_url']);
		$btcwallet = test_input($_POST['btcwallet']);
		$ref = test_input($_POST['ref']);
        $whatsapp = test_input($_POST['whatsapp']);
        

		if (empty($site_name) || empty($site_email) || empty($site_url)) {
			echo "";
		}else{
			$save = mysqli_query($link, "UPDATE settings SET sitename = '$site_name', sitemail = '$site_email', siteurl = '$site_url', walt = '$btcwallet', ref_bonus = '$ref', whatsapp = '$whatsapp' ");
			if($save){
				$msg = "Settings has been updated";
			}
		}
	}

	function test_input($data) {
	    $data = trim($data);
	    $data = stripslashes($data);
	    $data = htmlspecialchars($data);
	    return $data;
	  }
 ?>

 <div class="row">
    <?php 
            if ($msg != "") {
         ?>
         <div class="alert alert-success"><?php echo $msg; ?></div>
        <?php } ?>
    <div class="col-xl-12">
        <div class="card">
         
            <div class="card-body">
                <form class="needs-validation" method="post" action="settings.php" novalidate>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="validationCustom01">Site Name</label>
                                <input type="text" name="site_name" class="form-control" id="validationCustom01" placeholder="Site Name" value="<?php echo $sitename ?>" required>
                                
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="validationCustom02">Site URL</label>
                                <input type="text" name="site_url" class="form-control" id="validationCustom02" placeholder="https://example.com" value="<?php echo $siteurl ?>" required>
                                
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="validationCustom03">Site Email</label>
                                <input type="text" name="site_email" class="form-control" id="validationCustom03" value="<?php echo $sitemail ?>" placeholder="mail@domain.com" required>
                                
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="validationCustom04">BTC Wallet</label>
                                <input type="text" class="form-control" name="btcwallet" id="validationCustom04"  value="<?php echo $wallet ?>" placeholder="BTC Wallet Address" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="validationCustom05">Referal Bonus</label>
                                <input type="text" name="ref" value="<?php echo $ref_bonus ?>" class="form-control" id="validationCustom05" placeholder="Referal Bonus " required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="validationCustom05">Whatsapp Number(leave empty if not available)</label>
                                <input type="text" name="whatsapp" value="<?php echo $whatsapp ?>" class="form-control" id="validationCustom05" placeholder="Whatsapp ">
                            </div>
                        </div>
                        
                    </div>
                    
                    <button class="btn btn-primary" name="save" type="submit">Save</button>
                </form>
            </div>
        </div>
        <!-- end card -->
    </div> <!-- end col -->
</div>

 <?php 
	include 'footer.php';
 ?> 