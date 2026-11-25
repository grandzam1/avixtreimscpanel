<?php
 $page = "Add New Deposit";
 include 'header.php'; 
 $msg = "";
  use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


 	if (isset($_POST['add'])) {
 		$usermail = trim($_POST['user']);
 		$amt = trim($_POST['amt']);
 		$date = trim($_POST['date']);
 		$status = trim($_POST['status']);
 		if (!empty($usermail) && !empty($amt) && !empty($date) && !empty($status)) {
 			$insert = mysqli_query($link, "INSERT INTO deposits (email, amount, date, status) VALUES ('$usermail', '$amt', '$date', '$status') ");
 			if ($insert) {
 				mysqli_query($link, "UPDATE users SET dbalance = dbalance + '$amt' WHERE email = '$usermail' ");
 				$f = mysqli_query($link, "SELECT * FROM users WHERE email = '$usermail' ");
 				$u = mysqli_fetch_assoc($f);
 				$name = ucfirst($u['fullname']);

 				require_once "../PHPMailer/PHPMailer.php";
        		require_once '../PHPMailer/Exception.php';

        		$mail= new PHPMailer();
		        $mail->setFrom($sitemail);
		        $mail->FromName = $sitename;
		        $mail->addAddress($email);
		        $mail->Subject = "Deposit";
		        $mail->isHTML(true);

		        $mail->Body = '
					<div style="background: #f5f7f8;width: 100%;height: 100%; font-family: sans-serif; font-weight: 100;" class="be_container"> 

					<div style="background:#fff;max-width: 600px;margin: 0px auto;padding: 30px;"class="be_inner_containr"> <div class="be_header">

					      <div class="be_logo" style="float: left;"> <img src="'.$siteurl.'/logo.png"> </div>

					<div class="be_user" style="float: right"> <p>Dear: '.ucwords($name).'</p> </div> 

					<div style="clear: both;"></div> 

					<div class="be_bluebar" style="background: #1976d2; padding: 20px; color: #fff;margin-top: 10px;">

					<h1>Thank you for investing on '.$sitename.'</h1>

					</div> </div> 

					<div class="be_body" style="padding: 20px;"> <p style="line-height: 25px; color:#000;"> 

					Congratulations! Prior to your recent deposit of '.$amt.' USD, your account has been credited and the recent deposited amount has been added your previous balance.  


					</p>

					<div class="be_footer">
					<div style="border-bottom: 1px solid #ccc;"></div>


					<div class="be_bluebar" style="background: #1976d2; padding: 20px; color: #fff;margin-top: 10px;">

					<p> Please do not reply to this email. Emails sent to this address will not be answered. 
					Copyright ©'.date('Y').' '.$sitename.'. </p> <div class="be_logo" style=" width:60px;height:40px;float: right;"> </div> </div> </div> </div></div>' ;


					$mail->send();
					echo "<script> window.location.href = 'deposits.php', alert('Added Successfully') </script>";
					exit();
 			}
 		}
 	}
 ?>

 <div class="row">
    <div class="col-12">
    	<?php 
			if ($msg != "") {
		 ?>
		 <div class="alert alert-success"><?php echo $msg; ?></div>
		<?php } ?>
        <div class="card">
            <div class="card-body p-4">
            	<form method="post" action="new-deposit.php">
                <div class="row">
                    <div class="col-lg-6">
                        <div>
                            <div class="mb-3">
                                <label for="example-text-input" class="form-label">Select User</label>
                                <!-- <input class="form-control" type="text" name="username" value="" required id="example-text-input"> -->
                                <select class="form-control" name="user" required>
                                	<option>-Select Client -</option>
                                	<?php 
                                		$user = mysqli_query($link, "SELECT * FROM users");
                                		if (mysqli_num_rows($user) > 0 ) {
                                			while ($row = mysqli_fetch_assoc($user)) {
                                	 ?>
                                	<option value="<?php echo $row['email'] ?>"><?php echo $row['email'] ?></option>

                                <?php }} ?>
                                </select>
                            </div>
                             <div class="mb-3">
                                <label for="example-date-input" class="form-label">Date</label>
                                <input class="form-control" type="date" name="date" value="" id="example-date-input">
                            </div>
             
                                                        
                            
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="mt-3 mt-lg-0">
                            <div class="mb-3">
                                <label for="example-tel-input" class="form-label">Amount</label>
                                <input class="form-control" type="number" name="amt" value="" id="example-tel-input">
                            </div>
                            <div class="mb-3">
                                <label for="example-tel-input" class="form-label">Status</label>
                                <select class="form-control" name="status" required="">
                                	<option>PICK ONE</option>
                                	<!-- <option value="PENDING">PENDING</option> -->
                                	<option value="COMPLETED">COMPLETED</option>
                                </select>
                            </div>
                        
                        </div>
                    </div>
                </div>
                <div class="text-center">
                	<button class="btn btn-info" type="submit" name="add">ADD</button>
                </div>
				<br>
            </form>
            </div>
        </div>
    </div> <!-- end col -->
</div>
<!-- end row -->




<?php include 'footer.php'; ?>