<?php 
$page = "Notification Desk";
include_once __DIR__ . '/header.php';

$msg = "";
$err_msg = "";

// Handle Sending Notification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_notification'])) {
    $target_type = mysqli_real_escape_string($link, trim($_POST['target_type'] ?? 'all'));
    $target_email = mysqli_real_escape_string($link, trim($_POST['target_email'] ?? ''));
    $title = mysqli_real_escape_string($link, trim($_POST['title'] ?? ''));
    $message = mysqli_real_escape_string($link, trim($_POST['message'] ?? ''));
    $type = mysqli_real_escape_string($link, trim($_POST['type'] ?? 'info'));
    $link_url = mysqli_real_escape_string($link, trim($_POST['link_url'] ?? ''));

    if (empty($title) || empty($message)) {
        $err_msg = "Please enter both a notification title and message body.";
    } elseif ($target_type === 'single' && empty($target_email)) {
        $err_msg = "Please select a specific recipient user.";
    } else {
        $target_user_id = 0;
        if ($target_type === 'single') {
            $q_u = mysqli_query($link, "SELECT id FROM users WHERE email = '$target_email' LIMIT 1");
            if ($q_u && $u_row = mysqli_fetch_assoc($q_u)) {
                $target_user_id = intval($u_row['id']);
            }
        } else {
            $target_email = '';
        }

        $ins = mysqli_query($link, "INSERT INTO notifications (target_type, target_email, target_user_id, title, message, type, link_url) 
            VALUES ('$target_type', '$target_email', '$target_user_id', '$title', '$message', '$type', '$link_url')");

        if ($ins) {
            if ($target_type === 'all') {
                $msg = "Broadcast notification dispatched to ALL registered users successfully!";
            } else {
                $msg = "Direct in-app notification sent to $target_email successfully!";
            }
        } else {
            $err_msg = "Error dispatching notification: " . mysqli_error($link);
        }
    }
}

// Handle Delete Notification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_notification'])) {
    $del_id = intval($_POST['notification_id']);
    $del = mysqli_query($link, "DELETE FROM notifications WHERE id = '$del_id'");
    if ($del) {
        // Also clean up read receipts
        mysqli_query($link, "DELETE FROM notification_reads WHERE notification_id = '$del_id'");
        $msg = "Notification #$del_id deleted successfully.";
    } else {
        $err_msg = "Error deleting notification: " . mysqli_error($link);
    }
}

// Fetch all registered users for target selector
$users_list = [];
$q_users = mysqli_query($link, "SELECT id, username, email, fullname FROM users ORDER BY username ASC");
if ($q_users && mysqli_num_rows($q_users) > 0) {
    while ($u = mysqli_fetch_assoc($q_users)) {
        $users_list[] = $u;
    }
}

// Pre-fill recipient if passed via GET
$prefill_user = isset($_GET['to']) ? trim($_GET['to']) : '';
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

    <!-- Send Notification Form Card -->
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="card-title text-white mb-0"><i class="mdi mdi-bell-ring-outline me-2"></i> Dispatch New Notification</h4>
            </div>
            <div class="card-body p-4">
                <form action="notifications.php" method="POST" id="sendNotifForm">

                    <!-- Target Recipient Selection -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Target Recipient</label>
                        <div class="d-flex gap-4 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="target_type" id="targetAll" value="all" <?php echo empty($prefill_user) ? 'checked' : ''; ?> onchange="toggleRecipientField()">
                                <label class="form-check-label fw-semibold text-primary" for="targetAll">
                                    <i class="mdi mdi-bullhorn-outline me-1"></i> All Users (Broadcast)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="target_type" id="targetSingle" value="single" <?php echo !empty($prefill_user) ? 'checked' : ''; ?> onchange="toggleRecipientField()">
                                <label class="form-check-label fw-semibold text-dark" for="targetSingle">
                                    <i class="mdi mdi-account-outline me-1"></i> Single User
                                </label>
                            </div>
                        </div>

                        <!-- Single User Picker -->
                        <div id="singleUserBox" style="<?php echo empty($prefill_user) ? 'display: none;' : 'display: block;'; ?>">
                            <select name="target_email" id="targetEmailSelect" class="form-select">
                                <option value="">-- Select Recipient User --</option>
                                <?php foreach ($users_list as $user_item) { 
                                    $is_sel = ($prefill_user === $user_item['email'] || $prefill_user === $user_item['username']);
                                ?>
                                    <option value="<?php echo htmlspecialchars($user_item['email']); ?>" <?php echo $is_sel ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($user_item['fullname'] ?: $user_item['username']); ?> (<?php echo htmlspecialchars($user_item['email']); ?>)
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <!-- Category / Type -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Notification Category / Style</label>
                        <select name="type" class="form-select" required>
                            <option value="info">System Info / Announcement (Blue)</option>
                            <option value="success">Bonus / Profit / Settlement (Green)</option>
                            <option value="market">Market &amp; Shares Update (Cyan)</option>
                            <option value="warning">Important Notice / KYC (Amber)</option>
                            <option value="danger">Urgent Alert / Security (Red)</option>
                        </select>
                    </div>

                    <!-- Title -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Notification Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Special Market Allocation Bonus Active" required>
                    </div>

                    <!-- Message Body -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Message Content <span class="text-danger">*</span></label>
                        <textarea name="message" rows="4" class="form-control" placeholder="Write the notification message here. Line breaks are preserved in the user's notification center." required></textarea>
                    </div>

                    <!-- Optional Action Link -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Action Button Destination <small class="text-muted fw-normal">(Optional)</small></label>
                        <select name="link_url" class="form-select">
                            <option value="">-- No Direct Button --</option>
                            <option value="shares.php">Stock Shares Trading Desk (shares.php)</option>
                            <option value="deposit.php">Deposit Funds Page (deposit.php)</option>
                            <option value="plans.php">Investment Yield Packages (plans.php)</option>
                            <option value="kyc.php">KYC Identity Verification (kyc.php)</option>
                            <option value="withdraw.php">Withdrawal Desk (withdraw.php)</option>
                            <option value="settings.php">Profile Settings (settings.php)</option>
                        </select>
                        <small class="text-muted">If selected, an action button linking directly to this page will appear on the notification card.</small>
                    </div>

                    <button type="submit" name="send_notification" class="btn btn-primary w-100 py-2 fw-bold">
                        <i class="mdi mdi-send me-1"></i> Send In-App Notification
                    </button>

                </form>
            </div>
        </div>
    </div>

    <!-- Sent Notifications Log Card -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-0">Sent Notifications Log</h4>
                    <p class="card-title-desc mb-0">All past notifications dispatched to investors. Deleting an entry removes it from user feeds.</p>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatable" class="table table-bordered dt-responsive nowrap w-100 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Target</th>
                                <th>Category</th>
                                <th>Title &amp; Message</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $select = mysqli_query($link, "SELECT * FROM notifications ORDER BY id DESC");
                            if ($select && mysqli_num_rows($select) > 0) {
                                while ($row = mysqli_fetch_assoc($select)) {
                                    $is_all = ($row['target_type'] === 'all');
                                    $cat = $row['type'];
                                    $badge_class = 'bg-info';
                                    if ($cat === 'success') $badge_class = 'bg-success';
                                    elseif ($cat === 'warning') $badge_class = 'bg-warning text-dark';
                                    elseif ($cat === 'danger') $badge_class = 'bg-danger';
                                    elseif ($cat === 'market') $badge_class = 'bg-primary';
                            ?>
                            <tr>
                                <td>
                                    <?php if ($is_all) { ?>
                                        <span class="badge bg-success font-size-12"><i class="mdi mdi-bullhorn me-1"></i> All Users</span>
                                    <?php } else { ?>
                                        <span class="badge bg-soft-primary text-primary font-size-11"><i class="mdi mdi-account me-1"></i> Single User</span>
                                        <div class="small text-truncate mt-1" style="max-width: 140px;" title="<?php echo htmlspecialchars($row['target_email']); ?>">
                                            <?php echo htmlspecialchars($row['target_email']); ?>
                                        </div>
                                    <?php } ?>
                                </td>
                                <td>
                                    <span class="badge <?php echo $badge_class; ?>"><?php echo strtoupper(htmlspecialchars($cat)); ?></span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($row['title']); ?></div>
                                    <div class="text-muted small text-truncate" style="max-width: 240px;" title="<?php echo htmlspecialchars($row['message']); ?>">
                                        <?php echo htmlspecialchars($row['message']); ?>
                                    </div>
                                    <?php if (!empty($row['link_url'])) { ?>
                                        <div class="small text-info mt-1"><i class="mdi mdi-link-variant"></i> <?php echo htmlspecialchars($row['link_url']); ?></div>
                                    <?php } ?>
                                </td>
                                <td>
                                    <small class="text-muted"><?php echo htmlspecialchars($row['created_at']); ?></small>
                                </td>
                                <td>
                                    <form action="notifications.php" method="POST" onsubmit="return confirm('Delete this notification? It will be removed from all user feeds.');">
                                        <input type="hidden" name="notification_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="delete_notification" class="btn btn-sm btn-outline-danger" title="Delete Notification">
                                            <i class="mdi mdi-trash-can"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php 
                                }
                            } else {
                            ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No notifications dispatched yet.</td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleRecipientField() {
    const isSingle = document.getElementById('targetSingle').checked;
    const box = document.getElementById('singleUserBox');
    if (isSingle) {
        box.style.display = 'block';
    } else {
        box.style.display = 'none';
    }
}
</script>

<?php 
include_once __DIR__ . '/footer.php';
?>
