<?php 
$page = "Edit User";
include 'header.php';
$msg = "";

	if (isset($_GET['user'])) {
		$user = $_GET['user'];
	}else{
		echo "<script>window.location.href = 'users.php'</script>";
	}

	$query = mysqli_query($link, "SELECT * FROM users WHERE username = '$user' ");
	if (mysqli_num_rows($query) > 0) {
		$row = mysqli_fetch_assoc($query);
	}else{
		echo "<script>window.location.href = 'users.php'</script>";
	}

	if (isset($_POST['update'])) {
		$username = test_input($_POST['username']);
		$fname = test_input($_POST['fname']);
		$email = test_input($_POST['email']);
		$phone = test_input($_POST['phone']);
		$password = test_input($_POST['password']);
		$mbalance = test_input($_POST['mbalance']);
		$balance = test_input($_POST['balance']);
		$abonus = test_input($_POST['abonus']);
		$upgrade = test_input($_POST['upgrade']);
		$wpin = test_input($_POST['wpin']);

		$update = mysqli_query($link, "UPDATE users SET wpin = '$wpin', mbalance = '$mbalance', username = '$username', fullname = '$fname', email = '$email', phone = '$phone', password = '$password', dbalance = '$balance', abonus = '$abonus', upgrade = '$upgrade' WHERE username = '$user' " );
		if ($update) {
            echo "<script>
                    alert('User details has been updated');
                    window.location.href = 'edit-user.php?user=$user';
                </script>";

		}

	}
	
	if(isset($_POST['delete'])){
		mysqli_query($link, "DELETE FROM users WHERE username = '$user' ");
		echo "<script>window.location.href = 'users.php'</script>";
	}

	 function test_input($data) {
	    $data = trim($data);
	    $data = stripslashes($data);
	    $data = htmlspecialchars($data);
	    return $data;
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
            	<form method="post" action="edit-user.php?user=<?php echo $row['username'] ?>">
                <div class="row">
                	<div class="text-center mb-3">
                		<h3 ><?php echo ucwords($row['fullname'])  ?></h3>
                		
                	</div>
                    <div class="col-lg-6">
                        <div>
                            <div class="mb-3">
                                <label for="example-text-input" class="form-label">Username</label>
                                <input class="form-control" type="text" name="username" value="<?php echo $row['username'] ?>" required id="example-text-input">
                            </div>
                             <div class="mb-3">
                                <label for="example-date-input" class="form-label">Full Name</label>
                                <input class="form-control" type="text" name="fname" value="<?php echo $row['fullname'] ?>" id="example-date-input">
                            </div>
             
                            <div class="mb-3">
                                <label for="example-email-input" class="form-label">Email</label>
                                <input class="form-control" type="email" name="email" value="<?php echo $row['email'] ?>" id="example-email-input">
                            </div>

                            <div class="mb-3">
                                <label for="example-month-input" class="form-label">Password</label>
                                <input class="form-control" type="text" name="password" value="<?php echo $row['password'] ?>" id="example-month-input">
                            </div>
							<div class="mb-3">
                                <label for="example-month-input" class="form-label">Registration Date</label>
                                <input class="form-control" type="text"  value="<?php echo $row['date'] ?>" readonly id="example-month-input">
                            </div>
							<div class="mb-3">
                                <label for="example-month-input" class="form-label">Last Access</label>
                                <input class="form-control" type="text"  value="<?php echo $row['last_access'] ?>" readonly id="example-month-input">
                            </div>
                            
                            
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="mt-3 mt-lg-0">
                            <div class="mb-3">
                                <label for="example-tel-input" class="form-label">Added Bonus</label>
                                <input class="form-control" type="number" name="abonus" value="<?php echo $row['abonus'] ?>" id="example-tel-input">
                            </div>
                            <div class="mb-3">
                                <label for="example-week-input" class="form-label">Phone</label>
                                <input class="form-control" type="text" name="phone" value="<?php echo $row['phone'] ?>" id="example-week-input">
                            </div>
                            <div class="mb-3">
                                <label for="example-time-input" class="form-label">Referral Balance </label>
                                <input class="form-control" name="ref_balance" readonly type="number" value="<?php echo $row['refbalance'] ?>" id="example-time-input">
                            </div>
                            <div class="mb-3">
                                <label for="example-color-input" class="form-label">Deposited Balance</label>
                                <input type="number" class="form-control" name="balance" id="example-color-input" value="<?php echo $row['dbalance'] ?>" title="Choose your color">
                            </div>
							<div class="mb-3">
                                <label for="example-color-input" class="form-label">Main Balance</label>
                                <input type="number" class="form-control" name="mbalance" id="example-color-input" value="<?php echo $row['mbalance'] ?>" title="Choose your color">
                            </div>
                            <div class="mb-3">
                                <label for="example-color-input" class="form-label">Withdrawal Pin</label>
                                <input type="number" class="form-control" name="wpin" id="example-color-input" value="<?php echo $row['wpin'] ?>" title="Choose your color">
                            </div>
                            <div class="mb-3">
                                <label for="example-color-input" class="form-label">Upgrade Account</label>
                                
                                <select class="form-control" name="upgrade">
                                    <option value="<?php echo $row['upgrade'] ?>" >Current - <?php echo $row['upgrade'] == 1 ? "Upgraded" : "Not Upgraded" ?> </option>
                                    <option value="1">Yes</option>
                                    <option value="2">No</option>
                                </select>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div class="text-center">
                	<button class="btn btn-info" type="submit" name="update">Update</button>
                </div>
				<br>
				<div class="text-center">
                	<button class="btn btn-danger" type="submit" name="delete">Delete User Account</button>
                </div>
            </form>
            </div>
        </div>
    </div> <!-- end col -->
</div>
<!-- end row -->


 <?php
include 'footer.php';
 ?>