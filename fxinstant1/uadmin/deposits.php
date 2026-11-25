<?php 
$page = "Deposits";
include 'header.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
$msg = "";
	
	if (isset($_POST['approve'])) {
		$depost_id = $_POST['deposit_id'];
		$email = $_POST['email'];
		$name = $_POST['name'];
		$amount = $_POST['amount'];
		$method = $_POST['method'];
		$status = $_POST['status'];
		$username = $_POST['username'];

		if (isset($status) && $status == "COMPLETED") {
			$msg = "This transaction has been Approved already";
		}else{
			$query = mysqli_query($link, "UPDATE deposits SET status = 'COMPLETED' WHERE id = '$depost_id' ");

			if ($query) {
				mysqli_query($link, "UPDATE users SET balance = balance + $amount  WHERE username = '$username' ");

				require_once "../PHPMailer/PHPMailer.php";
        		require_once '../PHPMailer/Exception.php';

        		$mail= new PHPMailer();
		        $mail->setFrom($sitemail);
		        $mail->FromName = $sitename;
		        $mail->addAddress($email);
		        $mail->Subject = "Deposit Approval!";
		        $mail->isHTML(true);

		        $mail->Body = '
					<div style="background: #f5f7f8;width: 100%;height: 100%; font-family: sans-serif; font-weight: 100;" class="be_container"> 

					<div style="background:#fff;max-width: 600px;margin: 0px auto;padding: 30px;"class="be_inner_containr"> <div class="be_header">

					      <div class="be_logo" style="float: left;"> <img src="https://'.$siteurl.'/dashboard/logo/logo.png"> </div>

					<div class="be_user" style="float: right"> <p>Dear: '.ucwords($name).'</p> </div> 

					<div style="clear: both;"></div> 

					<div class="be_bluebar" style="background: #1976d2; padding: 20px; color: #fff;margin-top: 10px;">

					<h1>Thank you for investing on '.$sitename.'</h1>

					</div> </div> 

					<div class="be_body" style="padding: 20px;"> <p style="line-height: 25px; color:#000;"> 

					Congratulation! Your deposit of '.$amount.' USD worth of '.$method.' has been approved successfully and your account balance has been updated.  


					</p>

					<div class="be_footer">
					<div style="border-bottom: 1px solid #ccc;"></div>


					<div class="be_bluebar" style="background: #1976d2; padding: 20px; color: #fff;margin-top: 10px;">

					<p> Please do not reply to this email. Emails sent to this address will not be answered. 
					Copyright ©'.date('Y').' '.$sitename.'. </p> <div class="be_logo" style=" width:60px;height:40px;float: right;"> </div> </div> </div> </div></div>' ;


					$mail->send();
			
			}
			$msg = "Transaction has been Approved successfully";
		}
	}

	if(isset($_POST['delete'])){
		$depost_id = $_POST['deposit_id'];
		mysqli_query($link, "DELETE FROM deposits WHERE id = '$depost_id' ");
		$msg = "Deposit Transaction deleted successfully";
	}

?>
<div class="row">
	<?php 
			if ($msg != "") {
		 ?>
		 <div class="alert alert-success"><?php echo $msg; ?></div>
		<?php } ?>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
							<div class="table-rep-plugin">
                                <div class="table-responsive mb-0" data-pattern="priority-columns">
                                	<a href="new-deposit.php" class="btn btn-primary" >
									  Add new deposit
									</a>

		                            <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
		                                <thead>
		                                <tr>
		                                    <th>Email</th>
		                                    <th>Name</th>
		                                    <th>Amount</th>
		                                    <th>Status</th>
		                                    <th>Date</th>
		                                    <th>Delete</th>
		                                </tr>
		                                </thead>


		                                <tbody>
		                                	<?php
		                                		$select = mysqli_query($link, "SELECT * FROM deposits ORDER BY id DESC ");
		                                		if (mysqli_num_rows($select)) {
		                                			while ($row = mysqli_fetch_assoc($select)) {
		                                				$usr = $row['email'];
		                                				$user = mysqli_query($link, "SELECT * FROM users WHERE email = '$usr' ");
												$user_r = mysqli_fetch_assoc($user);
		                                			
		                                	 ?>
		                                	 <form action="deposits.php" method="post">
				                                <tr>
				                                	<input type="hidden" name="name" value="<?php echo $user_r['fullname'] ?>">
				                                	<input type="hidden" name="deposit_id" value="<?php echo $row['id'] ?>">
				                                	
				                                	<input type="hidden" name="amount" value="<?php echo $row['amount'] ?>">
				                                	
				                                	<input type="hidden" name="email" value="<?php echo $row['email'] ?>">
				                                
				                                    <td><?php echo $row['email'] ?></td>
				                                    <td><?php echo ucfirst($user_r['fullname']) ?></td>
				                                    <td>$<?php echo $row['amount'] ?></td>
				                                    
				                                    <td>
				                                    	<?php 
				                                    		if ($row['status'] == "PENDING") {
				                                    	 ?>
				                                    	 <button type="button" class="btn btn-warning"><?php echo $row['status'] ?></button>
				                                    	<?php }else{ ?>
				                                    	<button type="button" class="btn btn-success"><?php echo $row['status'] ?></button>
				                                    <?php } ?>
				                                    </td>
				                                    <td><?php echo $row['date'] ?></td>

				                                    <td>
														<button type="submit" onclick="return confirm('Do you want to delete this transaction ?')" name="delete" class="btn btn-danger">Delete</button></td>
													
				                                    </td>
				                                </tr>
			                                </form>
			                                <?php }
		                                		} ?>
		                            	</tbody>
		                            </table>
		                        </div>
		                    </div>
                        </div>
                    </div>
                </div>

            </div>



<?php 
include 'footer.php';
?>