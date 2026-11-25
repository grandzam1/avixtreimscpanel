<?php 
$page = "Manage Users";
include_once __DIR__ . '/header.php';

$msg = "";
$err_msg = "";

// Ensure required KYC columns exist in users table
$user_cols = [];
if (isset($link) && $link) {
    if (function_exists('ensure_column_exists')) {
        ensure_column_exists($link, 'users', 'verification', "INT DEFAULT 0");
        ensure_column_exists($link, 'users', 'kyc_status', "VARCHAR(30) DEFAULT 'unverified'");
    }
    $col_q = @mysqli_query($link, "SHOW COLUMNS FROM `users`");
    if ($col_q) {
        while ($col_r = mysqli_fetch_assoc($col_q)) {
            $user_cols[strtolower($col_r['Field'])] = true;
        }
    }
    if (!isset($user_cols['verification'])) {
        @mysqli_query($link, "ALTER TABLE `users` ADD `verification` INT DEFAULT 0");
        $user_cols['verification'] = true;
    }
    if (!isset($user_cols['kyc_status'])) {
        @mysqli_query($link, "ALTER TABLE `users` ADD `kyc_status` VARCHAR(30) DEFAULT 'unverified'");
        $user_cols['kyc_status'] = true;
    }
}

// Handle Quick KYC Actions from User List
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['quick_verify_kyc'])) {
        $u_id = intval($_POST['user_id']);
        $u_email = mysqli_real_escape_string($link, trim($_POST['user_email']));
        
        $set_parts = [];
        if (!empty($user_cols['verification'])) {
            $set_parts[] = "`verification` = '1'";
        }
        if (!empty($user_cols['kyc_status'])) {
            $set_parts[] = "`kyc_status` = 'verified'";
        }
        if (empty($set_parts)) {
            @mysqli_query($link, "ALTER TABLE `users` ADD `verification` INT DEFAULT 0");
            $set_parts[] = "`verification` = '1'";
        }

        $sql = "UPDATE users SET " . implode(', ', $set_parts) . " WHERE id = '$u_id'";
        $up = @mysqli_query($link, $sql);
        if (!$up) {
            @mysqli_query($link, "ALTER TABLE `users` ADD `verification` INT DEFAULT 0");
            @mysqli_query($link, "ALTER TABLE `users` ADD `kyc_status` VARCHAR(30) DEFAULT 'unverified'");
            $up = mysqli_query($link, "UPDATE users SET `verification` = '1' WHERE id = '$u_id'");
        }

        if ($up) {
            // Also approve any pending KYC applications for this user
            @mysqli_query($link, "UPDATE kyc_applications SET status = 'APPROVED' WHERE user_id = '$u_id' OR email = '$u_email'");
            $msg = "KYC verified successfully for user #$u_id ($u_email). Account now has full Level 2 verification clearance!";
        } else {
            $err_msg = "Error verifying KYC: " . mysqli_error($link);
        }
    }

    if (isset($_POST['revoke_kyc'])) {
        $u_id = intval($_POST['user_id']);
        $u_email = mysqli_real_escape_string($link, trim($_POST['user_email']));
        
        $set_parts = [];
        if (!empty($user_cols['verification'])) {
            $set_parts[] = "`verification` = '2'";
        }
        if (!empty($user_cols['kyc_status'])) {
            $set_parts[] = "`kyc_status` = 'unverified'";
        }
        if (empty($set_parts)) {
            @mysqli_query($link, "ALTER TABLE `users` ADD `verification` INT DEFAULT 0");
            $set_parts[] = "`verification` = '2'";
        }

        $sql = "UPDATE users SET " . implode(', ', $set_parts) . " WHERE id = '$u_id'";
        $up = @mysqli_query($link, $sql);
        if (!$up) {
            @mysqli_query($link, "ALTER TABLE `users` ADD `verification` INT DEFAULT 0");
            @mysqli_query($link, "ALTER TABLE `users` ADD `kyc_status` VARCHAR(30) DEFAULT 'unverified'");
            $up = mysqli_query($link, "UPDATE users SET `verification` = '2' WHERE id = '$u_id'");
        }

        if ($up) {
            $msg = "KYC clearance revoked for user #$u_id ($u_email). Account status set to Unverified.";
        } else {
            $err_msg = "Error revoking KYC: " . mysqli_error($link);
        }
    }
}
?>

<div class="row">
    <?php if ($msg != "") { ?>
        <div class="col-12">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="mdi mdi-check-all me-2"></i> <?php echo $msg; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php } ?>
    <?php if ($err_msg != "") { ?>
        <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="mdi mdi-alert-circle me-2"></i> <?php echo $err_msg; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php } ?>

    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title">Manage Registered Users</h4>
                    <p class="card-title-desc mb-0">View user portfolios, adjust balances, issue withdrawal PINs, and grant 1-click KYC clearance.</p>
                </div>
                <a href="kyc.php" class="btn btn-outline-primary">
                    <i class="mdi mdi-shield-account me-1"></i> Open KYC Review Desk
                </a>
            </div>
            <div class="card-body">
                <div class="table-rep-plugin">
                    <div class="table-responsive mb-0" data-pattern="priority-columns">
                        <table id="datatable" class="table table-bordered dt-responsive nowrap w-100 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Photo</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Fullname</th>
                                    <th>Main Balance</th>
                                    <th>Withdrawal PIN</th>
                                    <th>KYC Clearance</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $select = mysqli_query($link, "SELECT * FROM users ORDER BY id DESC");
                                if ($select && mysqli_num_rows($select) > 0) {
                                    while ($row = mysqli_fetch_assoc($select)) {
                                        $u_img = !empty($row['image']) ? '../' . $row['image'] : '';
                                        $has_img = !empty($u_img) && file_exists(__DIR__ . '/' . $u_img);
                                        $is_verified = (string)($row['verification'] ?? '') === '1' || strtolower((string)($row['kyc_status'] ?? '')) === 'verified';

                                        // Check if user has pending KYC submission
                                        $u_email_clean = mysqli_real_escape_string($link, $row['email']);
                                        $q_kyc = mysqli_query($link, "SELECT id, status FROM kyc_applications WHERE email = '$u_email_clean' ORDER BY id DESC LIMIT 1");
                                        $kyc_app = ($q_kyc && mysqli_num_rows($q_kyc) > 0) ? mysqli_fetch_assoc($q_kyc) : null;
                                        $has_pending_kyc = (!$is_verified && $kyc_app && $kyc_app['status'] === 'PENDING');
                                ?>
                                <tr>
                                    <td>
                                        <?php if ($has_img) { ?>
                                            <img src="<?php echo htmlspecialchars($u_img); ?>" alt="Avatar" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 2px solid #556ee6;">
                                        <?php } else { ?>
                                            <div style="width: 38px; height: 38px; border-radius: 50%; background: #2a343d; color: #ccff00; font-weight: bold; display: inline-flex; align-items: center; justify-content: center; font-size: 14px;">
                                                <?php echo strtoupper(substr($row['fullname'] ?: $row['username'], 0, 1)); ?>
                                            </div>
                                        <?php } ?>
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($row['username']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                                    <td><span class="badge bg-success font-size-13">$<?php echo number_format((float)$row['mbalance'], 2); ?></span></td>
                                    <td>
                                        <span class="badge <?php echo !empty($row['wpin']) ? 'bg-success' : 'bg-secondary'; ?>" style="font-size: 0.85rem; letter-spacing: 1px;">
                                            <?php echo !empty($row['wpin']) ? htmlspecialchars($row['wpin']) : 'Not Issued'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <?php if ($is_verified) { ?>
                                                <span class="badge bg-success font-size-12"><i class="mdi mdi-shield-check me-1"></i> Verified</span>
                                                <form action="users.php" method="post" style="display:inline;" onsubmit="return confirm('Revoke KYC verification clearance for <?php echo htmlspecialchars(addslashes($row['username'])); ?>?');">
                                                    <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                                    <input type="hidden" name="user_email" value="<?php echo htmlspecialchars($row['email']); ?>">
                                                    <button type="submit" name="revoke_kyc" class="btn btn-outline-warning btn-sm py-0 px-2 font-size-11" title="Revoke KYC Clearance">
                                                        <i class="mdi mdi-close"></i> Revoke
                                                    </button>
                                                </form>
                                            <?php } else { ?>
                                                <?php if ($has_pending_kyc) { ?>
                                                    <span class="badge bg-warning text-dark font-size-12"><i class="mdi mdi-clock-outline me-1"></i> Docs Pending</span>
                                                <?php } else { ?>
                                                    <span class="badge bg-secondary font-size-12">Unverified</span>
                                                <?php } ?>

                                                <form action="users.php" method="post" style="display:inline;">
                                                    <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                                    <input type="hidden" name="user_email" value="<?php echo htmlspecialchars($row['email']); ?>">
                                                    <button type="submit" name="quick_verify_kyc" class="btn btn-success btn-sm py-0 px-2 font-size-11" title="Instantly grant KYC verification to this user">
                                                        <i class="mdi mdi-shield-check me-1"></i> Verify KYC
                                                    </button>
                                                </form>
                                            <?php } ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1">
                                            <a class="btn btn-info btn-sm" href="edit-user.php?user=<?php echo urlencode($row['username']); ?>" title="Edit User">
                                                <i class="mdi mdi-pencil me-1"></i> Edit
                                            </a>
                                            <a class="btn btn-outline-warning btn-sm" href="notifications.php?to=<?php echo urlencode($row['email']); ?>" title="Send In-App Notification to this user">
                                                <i class="mdi mdi-bell-outline"></i>
                                            </a>
                                            <?php if ($kyc_app) { ?>
                                                <a class="btn btn-outline-primary btn-sm" href="kyc.php?search=<?php echo urlencode($row['email']); ?>" title="View submitted KYC documents">
                                                    <i class="mdi mdi-file-document-outline"></i> Docs
                                                </a>
                                            <?php } ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php 
                                    }
                                } else {
                                ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">No registered users found.</td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
include_once __DIR__ . '/footer.php';
?>
