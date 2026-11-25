<?php 
$page = "Edit User";
include_once __DIR__ . '/header.php';
$msg = "";

if (isset($_GET['user'])) {
    $user = $_GET['user'];
} else {
    echo "<script>window.location.href = 'users.php'</script>";
}

$query = mysqli_query($link, "SELECT * FROM users WHERE username = '$user' ");
if (mysqli_num_rows($query) > 0) {
    $row = mysqli_fetch_assoc($query);
} else {
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
    $verification = test_input($_POST['verification']);
    $wpin = test_input($_POST['wpin']);

    ensure_column_exists($link, 'users', 'image', "VARCHAR(255) DEFAULT ''");
    ensure_column_exists($link, 'users', 'verification', "INT DEFAULT 0");
    ensure_column_exists($link, 'users', 'kyc_status', "VARCHAR(30) DEFAULT 'unverified'");

    $img_update_sql = "";
    if (isset($_FILES['user_image']) && $_FILES['user_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['user_image']['tmp_name'];
        $file_name = $_FILES['user_image']['name'];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed = ['png', 'jpg', 'jpeg', 'webp', 'gif'];
        if (in_array($ext, $allowed)) {
            $dest_dir = "../images/avatars/";
            if (!is_dir($dest_dir)) {
                @mkdir($dest_dir, 0777, true);
            }
            $target_file = $dest_dir . "user_" . intval($row['id']) . "_" . time() . "." . $ext;
            if (move_uploaded_file($file_tmp, $target_file)) {
                $db_img_path = "images/avatars/" . basename($target_file);
                $img_update_sql = ", image = '$db_img_path' ";
            }
        }
    }

    $kyc_status_val = ($verification == '1') ? 'verified' : 'unverified';
    $update = @mysqli_query($link, "UPDATE users SET wpin = '$wpin', mbalance = '$mbalance', username = '$username', fullname = '$fname', email = '$email', phone = '$phone', password = '$password', dbalance = '$balance', abonus = '$abonus', upgrade = '$upgrade', verification = '$verification', kyc_status = '$kyc_status_val' $img_update_sql WHERE username = '$user'");
    if (!$update) {
        @mysqli_query($link, "ALTER TABLE `users` ADD `verification` INT DEFAULT 0");
        @mysqli_query($link, "ALTER TABLE `users` ADD `kyc_status` VARCHAR(30) DEFAULT 'unverified'");
        $update = mysqli_query($link, "UPDATE users SET wpin = '$wpin', mbalance = '$mbalance', username = '$username', fullname = '$fname', email = '$email', phone = '$phone', password = '$password', dbalance = '$balance', abonus = '$abonus', upgrade = '$upgrade', verification = '$verification', kyc_status = '$kyc_status_val' $img_update_sql WHERE username = '$user'");
    }

    if ($update) {
        if ($verification == '1') {
            mysqli_query($link, "UPDATE kyc_applications SET status = 'APPROVED' WHERE email = '$email' OR username = '$username'");
        }
        echo "<script>
                alert('User details and display picture have been updated');
                window.location.href = 'edit-user.php?user=$username';
              </script>";
    }
}

if (isset($_POST['delete'])) {
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
        <?php if ($msg != "") { ?>
            <div class="alert alert-success"><?php echo $msg; ?></div>
        <?php } ?>
        <div class="card">
            <div class="card-body p-4">
                <form method="post" action="edit-user.php?user=<?php echo $row['username'] ?>" enctype="multipart/form-data">
                    <div class="row">
                        <div class="text-center mb-4">
                            <?php 
                            $u_pic = !empty($row['image']) ? '../' . $row['image'] : '';
                            if (!empty($u_pic) && file_exists(__DIR__ . '/' . $u_pic)) { ?>
                                <img src="<?php echo htmlspecialchars($u_pic); ?>" alt="User Avatar" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #ccff00; box-shadow: 0 4px 15px rgba(0,0,0,0.3); margin-bottom: 12px;">
                            <?php } else { ?>
                                <div style="width: 100px; height: 100px; border-radius: 50%; background: #2a343d; color: #ccff00; font-size: 2.5rem; font-weight: 800; display: inline-flex; align-items: center; justify-content: center; border: 3px solid rgba(204,255,0,0.3); margin-bottom: 12px;">
                                    <?php echo strtoupper(substr($row['fullname'] ?: $row['username'], 0, 1)); ?>
                                </div>
                            <?php } ?>
                            <h3 class="mb-1"><?php echo ucwords($row['fullname']) ?></h3>
                            <div class="d-flex justify-content-center mt-2">
                                <div style="max-width: 340px;">
                                    <label class="form-label font-size-12 text-muted">Upload / Update User Display Picture</label>
                                    <input type="file" name="user_image" class="form-control form-control-sm" accept="image/*">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input class="form-control" type="text" name="username" value="<?php echo $row['username'] ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input class="form-control" type="text" name="fname" value="<?php echo $row['fullname'] ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input class="form-control" type="email" name="email" value="<?php echo $row['email'] ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input class="form-control" type="text" name="password" value="<?php echo $row['password'] ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Registration Date</label>
                                <input class="form-control" type="text" value="<?php echo $row['date'] ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Last Access</label>
                                <input class="form-control" type="text" value="<?php echo $row['last_access'] ?>" readonly>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Added Bonus</label>
                                <input class="form-control" type="number" name="abonus" value="<?php echo $row['abonus'] ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input class="form-control" type="text" name="phone" value="<?php echo $row['phone'] ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Referral Balance</label>
                                <input class="form-control" name="ref_balance" readonly type="number" value="<?php echo $row['refbalance'] ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Deposited Balance</label>
                                <input type="number" class="form-control" name="balance" value="<?php echo $row['dbalance'] ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Main Balance</label>
                                <input type="number" class="form-control" name="mbalance" value="<?php echo $row['mbalance'] ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Withdrawal Pin</label>
                                <input type="number" class="form-control" name="wpin" value="<?php echo $row['wpin'] ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Upgrade Account</label>
                                <select class="form-control" name="upgrade">
                                    <option value="<?php echo $row['upgrade'] ?>">Current - <?php echo $row['upgrade'] == 1 ? "Upgraded" : "Not Upgraded" ?></option>
                                    <option value="1">Yes</option>
                                    <option value="2">No</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Account Verification</label>
                                <select class="form-control" name="verification">
                                    <option value="<?php echo $row['verification'] ?>">Current - <?php echo $row['verification'] == 1 ? "Verified" : "Not Verified" ?></option>
                                    <option value="1">Yes</option>
                                    <option value="2">No</option>
                                </select>
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
    </div>
</div>

<?php include_once __DIR__ . '/footer.php'; ?>
