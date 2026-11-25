<?php
include 'header.php';
include 'sidebar.php';

require_once __DIR__ . '/../mail-helper.php';

$feedback = '';
$feedback_type = '';

// Handle Direct Form Submission for New Email / Broadcast
if (isset($_POST['send_new_email'])) {
    $recipient_mode = $_POST['recipient_mode'] ?? 'single';
    $subject        = trim($_POST['subject'] ?? '');
    $message        = trim($_POST['message'] ?? '');

    if (empty($subject) || empty($message)) {
        $feedback = "Please fill in both the subject and the message body.";
        $feedback_type = "error";
    } elseif ($recipient_mode === 'broadcast') {
        // Broadcast to All Users
        $users_q = mysqli_query($link, "SELECT id, email, username FROM users WHERE email != ''");
        $sent_count = 0;
        if ($users_q && mysqli_num_rows($users_q) > 0) {
            while ($u = mysqli_fetch_assoc($users_q)) {
                $u_name = !empty($u['username']) ? $u['username'] : $u['email'];
                create_support_thread($link, (int)$u['id'], $u['email'], $u_name, $subject, $message, 1);
                $sent_count++;
            }
            $feedback = "Broadcast email successfully dispatched to all $sent_count registered users!";
            $feedback_type = "success";
        } else {
            $feedback = "No registered users found to broadcast to.";
            $feedback_type = "error";
        }
    } else {
        // Single User Email
        $user_id = (int)($_POST['user_id'] ?? 0);
        $manual_email = trim($_POST['manual_email'] ?? '');

        $target_email = '';
        $target_name = '';

        if ($user_id > 0) {
            $u_check = mysqli_query($link, "SELECT id, email, username FROM users WHERE id = $user_id LIMIT 1");
            if ($u_check && $u_row = mysqli_fetch_assoc($u_check)) {
                $target_email = $u_row['email'];
                $target_name = $u_row['username'];
            }
        } elseif (!empty($manual_email) && filter_var($manual_email, FILTER_VALIDATE_EMAIL)) {
            $target_email = $manual_email;
            $target_name = $manual_email;
        }

        if (empty($target_email)) {
            $feedback = "Please select a registered user or enter a valid email address.";
            $feedback_type = "error";
        } else {
            $res = create_support_thread($link, $user_id, $target_email, $target_name, $subject, $message, 0);
            if ($res) {
                if (!empty($res['mail_sent'])) {
                    $feedback = "Email sent successfully to $target_email with ticket code " . $res['ticket_code'] . "! When they reply, you will receive it right here.";
                    $feedback_type = "success";
                } else {
                    $err_detail = !empty($res['mail_error']) ? " (Notice: " . htmlspecialchars($res['mail_error']) . ")" : "";
                    $feedback = "Ticket " . $res['ticket_code'] . " recorded in system, but outgoing email delivery could not reach $target_email.$err_detail Please verify your SMTP settings in <a href='settings.php' class='alert-link'>Settings &rarr;</a>";
                    $feedback_type = "warning";
                }
                // Auto-select the newly created thread
                $_GET['thread_id'] = $res['thread_id'];
            } else {
                $feedback = "Failed to create support thread. Please check your database connection.";
                $feedback_type = "error";
            }
        }
    }
}

// Active Selected Thread
$active_thread_id = isset($_GET['thread_id']) ? (int)$_GET['thread_id'] : 0;
$active_thread = null;
$thread_messages = [];

// Filter tab
$filter = isset($_GET['filter']) ? trim($_GET['filter']) : 'all';
$filter_sql = "";
if ($filter === 'needs_reply') {
    $filter_sql = "WHERE `status` = 'user_replied'";
} elseif ($filter === 'admin_replied') {
    $filter_sql = "WHERE `status` = 'admin_replied'";
} elseif ($filter === 'broadcasts') {
    $filter_sql = "WHERE `is_broadcast` = 1";
}

// Search
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
if (!empty($search)) {
    $s_esc = mysqli_real_escape_string($link, $search);
    $filter_sql .= (empty($filter_sql) ? "WHERE" : " AND") . " (`ticket_code` LIKE '%$s_esc%' OR `user_email` LIKE '%$s_esc%' OR `user_name` LIKE '%$s_esc%' OR `subject` LIKE '%$s_esc%')";
}

// Fetch Threads List
$threads_query = "SELECT t.*, 
    (SELECT message FROM support_messages WHERE thread_id = t.id ORDER BY id DESC LIMIT 1) as last_msg,
    (SELECT sender_type FROM support_messages WHERE thread_id = t.id ORDER BY id DESC LIMIT 1) as last_sender,
    (SELECT COUNT(*) FROM support_messages WHERE thread_id = t.id) as msg_count
    FROM support_threads t $filter_sql ORDER BY t.updated_at DESC LIMIT 50";
$threads_res = mysqli_query($link, $threads_query);

// If no thread selected but list has items, auto-select first
if ($active_thread_id <= 0 && $threads_res && mysqli_num_rows($threads_res) > 0) {
    mysqli_data_seek($threads_res, 0);
    $first_row = mysqli_fetch_assoc($threads_res);
    $active_thread_id = (int)$first_row['id'];
    mysqli_data_seek($threads_res, 0); // reset pointer
}

// Fetch Active Thread Data
if ($active_thread_id > 0) {
    $t_res = mysqli_query($link, "SELECT * FROM support_threads WHERE id = $active_thread_id LIMIT 1");
    if ($t_res && mysqli_num_rows($t_res) > 0) {
        $active_thread = mysqli_fetch_assoc($t_res);

        // Fetch user financial balance if registered
        $u_info = null;
        if ((int)$active_thread['user_id'] > 0) {
            $u_q = mysqli_query($link, "SELECT id, username, email, mbalance, dbalance FROM users WHERE id = " . (int)$active_thread['user_id'] . " LIMIT 1");
            if ($u_q) $u_info = mysqli_fetch_assoc($u_q);
        }

        // Fetch messages for active thread
        $m_res = mysqli_query($link, "SELECT * FROM support_messages WHERE thread_id = $active_thread_id ORDER BY created_at ASC");
        while ($m_res && $m = mysqli_fetch_assoc($m_res)) {
            $thread_messages[] = $m;
        }

        // Mark user messages as read
        mysqli_query($link, "UPDATE support_messages SET is_read = 1 WHERE thread_id = $active_thread_id AND sender_type = 'user'");
    }
}

// Summary Statistics
$total_threads = 0;
$unread_replies = 0;
$broadcasts_count = 0;

$stat_q1 = mysqli_query($link, "SELECT COUNT(*) as c FROM support_threads");
if ($stat_q1 && $r = mysqli_fetch_assoc($stat_q1)) $total_threads = (int)$r['c'];

$stat_q2 = mysqli_query($link, "SELECT COUNT(*) as c FROM support_threads WHERE status = 'user_replied'");
if ($stat_q2 && $r = mysqli_fetch_assoc($stat_q2)) $unread_replies = (int)$r['c'];

$stat_q3 = mysqli_query($link, "SELECT COUNT(*) as c FROM support_threads WHERE is_broadcast = 1");
if ($stat_q3 && $r = mysqli_fetch_assoc($stat_q3)) $broadcasts_count = (int)$r['c'];

// Fetch Users for Compose Dropdown
$all_users = [];
$u_list = mysqli_query($link, "SELECT id, email, username FROM users WHERE email != '' ORDER BY username ASC");
while ($u_list && $urow = mysqli_fetch_assoc($u_list)) {
    $all_users[] = $urow;
}
?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <!-- Page Title Header -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">
                            <i class="bx bx-envelope text-primary me-2"></i> Mail Center & Support Desk
                        </h4>
                        <div class="page-title-right">
                            <button type="button" class="btn btn-primary btn-sm waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#composeEmailModal">
                                <i class="bx bx-plus font-size-16 align-middle me-1"></i> Compose Email / Broadcast
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feedback Alerts -->
            <?php if (!empty($feedback)) { ?>
                <div class="alert alert-<?php echo ($feedback_type === 'success') ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-check-circle-outline me-2 font-size-16"></i>
                    <?php echo htmlspecialchars($feedback); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php } ?>

            <!-- Metrics / Counters Strip -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="card mini-stats-wid mb-0 border shadow-none">
                        <div class="card-body py-3">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium font-size-12 mb-1">Total Conversations</p>
                                    <h4 class="mb-0 font-size-20"><?php echo number_format($total_threads); ?></h4>
                                </div>
                                <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                    <span class="avatar-title rounded-circle bg-primary"><i class="bx bx-conversation font-size-20"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mini-stats-wid mb-0 border shadow-none" style="border-left: 4px solid #2ecc71 !important;">
                        <div class="card-body py-3">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium font-size-12 mb-1">Awaiting Admin Reply</p>
                                    <h4 class="mb-0 font-size-20 text-success">
                                        <span id="unreadRepliesCount"><?php echo number_format($unread_replies); ?></span>
                                        <?php if ($unread_replies > 0) { ?>
                                            <span class="badge badge-soft-success font-size-11 ms-1">New User Replies</span>
                                        <?php } ?>
                                    </h4>
                                </div>
                                <div class="avatar-sm rounded-circle bg-success align-self-center mini-stat-icon">
                                    <span class="avatar-title rounded-circle bg-success"><i class="bx bx-bell font-size-20"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mini-stats-wid mb-0 border shadow-none">
                        <div class="card-body py-3">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium font-size-12 mb-1">System Broadcasts</p>
                                    <h4 class="mb-0 font-size-20"><?php echo number_format($broadcasts_count); ?></h4>
                                </div>
                                <div class="avatar-sm rounded-circle bg-info align-self-center mini-stat-icon">
                                    <span class="avatar-title rounded-circle bg-info"><i class="bx bx-broadcast font-size-20"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Master Two-Column Mailbox Layout -->
            <div class="card border">
                <div class="card-body p-0">
                    <div class="row g-0">

                        <!-- LEFT COLUMN: THREAD LIST & SEARCH -->
                        <div class="col-lg-4 col-md-5 border-end" style="min-height: 650px; display: flex; flex-direction: column;">
                            <!-- Search & Filter Controls -->
                            <div class="p-3 border-bottom bg-light">
                                <form method="get" action="mail-center.php" class="mb-2">
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="q" class="form-control" placeholder="Search user, email, subject..." value="<?php echo htmlspecialchars($search); ?>">
                                        <button class="btn btn-primary" type="submit"><i class="bx bx-search"></i></button>
                                        <?php if (!empty($search)) { ?>
                                            <a href="mail-center.php" class="btn btn-outline-secondary"><i class="bx bx-x"></i></a>
                                        <?php } ?>
                                    </div>
                                    <?php if ($filter !== 'all') { ?>
                                        <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
                                    <?php } ?>
                                </form>

                                <!-- Filter Nav Pills -->
                                <div class="btn-group btn-group-sm w-100" role="group">
                                    <a href="mail-center.php?filter=all<?php echo !empty($search) ? '&q=' . urlencode($search) : ''; ?>" class="btn btn-outline-primary <?php echo ($filter === 'all') ? 'active' : ''; ?>">All</a>
                                    <a href="mail-center.php?filter=needs_reply<?php echo !empty($search) ? '&q=' . urlencode($search) : ''; ?>" class="btn btn-outline-success <?php echo ($filter === 'needs_reply') ? 'active' : ''; ?>">
                                        Replied <?php if ($unread_replies > 0) { ?><span class="badge bg-danger rounded-pill"><?php echo $unread_replies; ?></span><?php } ?>
                                    </a>
                                    <a href="mail-center.php?filter=admin_replied<?php echo !empty($search) ? '&q=' . urlencode($search) : ''; ?>" class="btn btn-outline-secondary <?php echo ($filter === 'admin_replied') ? 'active' : ''; ?>">Awaiting</a>
                                    <a href="mail-center.php?filter=broadcasts<?php echo !empty($search) ? '&q=' . urlencode($search) : ''; ?>" class="btn btn-outline-info <?php echo ($filter === 'broadcasts') ? 'active' : ''; ?>">Broadcasts</a>
                                </div>
                            </div>

                            <!-- Scrollable Thread List -->
                            <div class="list-group list-group-flush overflow-auto flex-grow-1" style="max-height: 570px;" id="threadsListContainer">
                                <?php if (!$threads_res || mysqli_num_rows($threads_res) == 0) { ?>
                                    <div class="p-4 text-center text-muted">
                                        <i class="bx bx-inbox font-size-24 mb-2 d-block"></i>
                                        No messages found in this category. Click <strong>Compose Email</strong> above to start a conversation.
                                    </div>
                                <?php } else { ?>
                                    <?php while ($t = mysqli_fetch_assoc($threads_res)) { 
                                        $isSelected = ($t['id'] == $active_thread_id);
                                        $isNeedsReply = ($t['status'] === 'user_replied');
                                    ?>
                                        <a href="mail-center.php?thread_id=<?php echo $t['id']; ?>&filter=<?php echo urlencode($filter); ?><?php echo !empty($search) ? '&q=' . urlencode($search) : ''; ?>" 
                                           class="list-group-item list-group-item-action p-3 <?php echo $isSelected ? 'bg-light border-primary' : ''; ?>"
                                           style="<?php echo $isSelected ? 'border-left: 4px solid #00b074 !important;' : ''; ?>">
                                            <div class="d-flex align-items-start">
                                                <div class="avatar-xs me-3 flex-shrink-0">
                                                    <span class="avatar-title rounded-circle <?php echo $isNeedsReply ? 'bg-success text-white' : 'bg-soft-primary text-primary'; ?> font-weight-bold">
                                                        <?php echo strtoupper(substr(!empty($t['user_name']) ? $t['user_name'] : $t['user_email'], 0, 1)); ?>
                                                    </span>
                                                </div>
                                                <div class="flex-grow-1 min-w-0">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <h6 class="font-size-13 text-truncate mb-0 fw-bold">
                                                            <?php echo htmlspecialchars($t['user_name'] ?: $t['user_email']); ?>
                                                        </h6>
                                                        <small class="text-muted font-size-11">
                                                            <?php echo date('M j', strtotime($t['updated_at'])); ?>
                                                        </small>
                                                    </div>
                                                    <div class="text-truncate font-size-12 fw-semibold text-dark mb-1">
                                                        <?php echo htmlspecialchars($t['subject']); ?>
                                                    </div>
                                                    <div class="text-truncate text-muted font-size-12">
                                                        <?php if ($t['last_sender'] === 'user') { ?>
                                                            <strong class="text-success">&crarr; User:</strong>
                                                        <?php } else { ?>
                                                            <span class="text-muted">Admin:</span>
                                                        <?php } ?>
                                                        <?php echo htmlspecialchars(mb_strimwidth($t['last_msg'] ?? '', 0, 48, '...')); ?>
                                                    </div>
                                                    <div class="mt-2 d-flex gap-1 align-items-center">
                                                        <span class="badge bg-soft-secondary text-secondary font-size-10"><?php echo htmlspecialchars($t['ticket_code']); ?></span>
                                                        <?php if ($isNeedsReply) { ?>
                                                            <span class="badge bg-success font-size-10"><i class="bx bx-bell me-1"></i> User Replied</span>
                                                        <?php } else { ?>
                                                            <span class="badge bg-light text-muted font-size-10">Admin Replied</span>
                                                        <?php } ?>
                                                        <?php if ($t['is_broadcast'] == 1) { ?>
                                                            <span class="badge bg-info font-size-10">Broadcast</span>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>

                        <!-- RIGHT COLUMN: ACTIVE THREAD CONVERSATION & QUICK REPLY -->
                        <div class="col-lg-8 col-md-7 d-flex flex-column" style="min-height: 650px;">
                            <?php if (!$active_thread) { ?>
                                <div class="d-flex flex-column align-items-center justify-content-center flex-grow-1 p-5 text-center text-muted">
                                    <div class="avatar-lg rounded-circle bg-light d-flex align-items-center justify-content-center mb-3">
                                        <i class="bx bx-envelope-open font-size-36 text-primary"></i>
                                    </div>
                                    <h5 class="fw-bold">No Conversation Selected</h5>
                                    <p class="text-muted font-size-13 max-w-sm">
                                        Select a message thread from the left or click <strong>Compose Email</strong> above to email a user directly.
                                    </p>
                                </div>
                            <?php } else { ?>
                                <!-- Active Thread Top Bar -->
                                <div class="p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2 bg-white">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="badge bg-primary font-size-11"><?php echo htmlspecialchars($active_thread['ticket_code']); ?></span>
                                            <h5 class="font-size-15 fw-bold mb-0 text-dark"><?php echo htmlspecialchars($active_thread['subject']); ?></h5>
                                            <?php if ($active_thread['status'] === 'user_replied') { ?>
                                                <span class="badge bg-success">User Replied &bull; Direct Response</span>
                                            <?php } else { ?>
                                                <span class="badge bg-soft-secondary text-secondary">Awaiting User Reply</span>
                                            <?php } ?>
                                        </div>
                                        <div class="font-size-12 text-muted">
                                            Recipient: <strong><?php echo htmlspecialchars($active_thread['user_name'] ?: 'Investor'); ?></strong> 
                                            (&lt;<?php echo htmlspecialchars($active_thread['user_email']); ?>&gt;)
                                            <?php if ($u_info) { ?>
                                                &bull; Balance: <strong class="text-success">$<?php echo number_format((float)$u_info['mbalance'] + (float)$u_info['dbalance'], 2); ?></strong>
                                                &bull; <a href="edit-user.php?id=<?php echo $u_info['id']; ?>" class="text-primary text-decoration-none" target="_blank"><i class="bx bx-user-circle"></i> Edit User</a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="../reply.php?ticket=<?php echo urlencode($active_thread['ticket_code']); ?>&token=<?php echo urlencode($active_thread['reply_token']); ?>" 
                                           target="_blank" class="btn btn-outline-secondary btn-sm" title="Preview what the user sees when clicking Reply in their email">
                                            <i class="bx bx-link-external me-1"></i> Preview User Reply Portal
                                        </a>
                                    </div>
                                </div>

                                <!-- Thread Messages Stream -->
                                <div class="p-4 flex-grow-1 overflow-auto bg-light" style="max-height: 440px;" id="adminStreamContainer">
                                    <div class="d-flex flex-column gap-3" id="adminMessagesList">
                                        <?php 
                                        $last_message_id = 0;
                                        foreach ($thread_messages as $m) { 
                                            $last_message_id = max($last_message_id, (int)$m['id']);
                                            $isAdmin = ($m['sender_type'] === 'admin');
                                        ?>
                                            <div class="d-flex <?php echo $isAdmin ? 'justify-content-end' : 'justify-content-start'; ?>" data-msg-id="<?php echo $m['id']; ?>">
                                                <div style="max-width: 80%;">
                                                    <div class="d-flex align-items-center gap-1 font-size-11 text-muted mb-1 <?php echo $isAdmin ? 'justify-content-end' : 'justify-content-start'; ?>">
                                                        <?php if ($isAdmin) { ?>
                                                            <strong class="text-primary"><i class="bx bx-shield-quarter"></i> Admin Support</strong> &bull; <?php echo date('M j, g:i a', strtotime($m['created_at'])); ?>
                                                        <?php } else { ?>
                                                            <strong class="text-success"><i class="bx bx-user"></i> <?php echo htmlspecialchars($m['sender_name'] ?: 'User'); ?></strong> &bull; <?php echo date('M j, g:i a', strtotime($m['created_at'])); ?>
                                                        <?php } ?>
                                                    </div>
                                                    <div class="p-3 rounded-3 shadow-sm font-size-13 <?php echo $isAdmin ? 'bg-primary text-white' : 'bg-white text-dark border'; ?>">
                                                        <?php echo nl2br(htmlspecialchars($m['message'])); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>

                                <!-- Quick Reply Box -->
                                <div class="p-3 border-top bg-white">
                                    <form id="adminReplyForm" onsubmit="submitAdminReply(event)">
                                        <input type="hidden" name="thread_id" value="<?php echo $active_thread_id; ?>">
                                        <input type="hidden" id="lastMsgId" value="<?php echo $last_message_id; ?>">
                                        <div class="mb-2">
                                            <textarea name="message" id="adminReplyText" class="form-control" rows="3" placeholder="Type your response to the user... (Sent directly to their email with an instant reply button)" required></textarea>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted font-size-12">
                                                <i class="bx bx-mail-send text-primary me-1"></i> Submitting sends an email to <strong><?php echo htmlspecialchars($active_thread['user_email']); ?></strong>
                                            </span>
                                            <button type="submit" id="btnSendReply" class="btn btn-primary btn-sm px-4 waves-effect waves-light">
                                                <i class="bx bx-send me-1"></i> Send Reply & Email User
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            <?php } ?>
                        </div>

                    </div>
                </div>
            </div>

        </div> <!-- container-fluid -->
    </div> <!-- page-content -->

    <!-- ================= COMPOSE NEW EMAIL MODAL ================= -->
    <div class="modal fade" id="composeEmailModal" tabindex="-1" aria-labelledby="composeEmailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form action="mail-center.php" method="post">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title font-size-16" id="composeEmailModalLabel">
                            <i class="bx bx-envelope text-primary me-1"></i> Compose Email / Broadcast
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <!-- Mode Selection: Single vs Broadcast -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Select Distribution Target:</label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="recipient_mode" id="modeSingle" value="single" checked onchange="toggleRecipientMode()">
                                    <label class="form-check-label fw-semibold" for="modeSingle">
                                        Send to Specific User (Two-Way Support Thread)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="recipient_mode" id="modeBroadcast" value="broadcast" onchange="toggleRecipientMode()">
                                    <label class="form-check-label fw-semibold text-danger" for="modeBroadcast">
                                        <i class="bx bx-broadcast"></i> Broadcast to All Registered Users
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- User Select Dropdown -->
                        <div class="mb-3" id="userSelectGroup">
                            <label for="composeUserSelect" class="form-label fw-semibold">Choose Registered User:</label>
                            <select name="user_id" id="composeUserSelect" class="form-select">
                                <option value="0">-- Select Registered User --</option>
                                <?php foreach ($all_users as $usr) { ?>
                                    <option value="<?php echo $usr['id']; ?>">
                                        <?php echo htmlspecialchars($usr['username']); ?> &lt;<?php echo htmlspecialchars($usr['email']); ?>&gt;
                                    </option>
                                <?php } ?>
                            </select>
                            <div class="form-text">Or enter a custom email below if sending to an external recipient:</div>
                            <input type="email" name="manual_email" class="form-control mt-2" placeholder="optional_external@email.com">
                        </div>

                        <div class="alert alert-info py-2 font-size-12 d-none" id="broadcastNotice">
                            <i class="bx bx-info-circle me-1 font-size-14 align-middle"></i>
                            <strong>Broadcast Mode:</strong> This email will be dispatched to all <strong><?php echo count($all_users); ?></strong> registered investor accounts simultaneously.
                        </div>

                        <!-- Subject -->
                        <div class="mb-3">
                            <label for="composeSubject" class="form-label fw-semibold">Subject Line:</label>
                            <input type="text" name="subject" id="composeSubject" class="form-control" placeholder="e.g. Portfolio Allocation Update, Account Verification, Quarterly Performance Review..." required>
                        </div>

                        <!-- Body -->
                        <div class="mb-3">
                            <label for="composeMessage" class="form-label fw-semibold">Message Body:</label>
                            <textarea name="message" id="composeMessage" class="form-control" rows="6" placeholder="Type the full message content here. It will be styled into the luxury Avix Treims email template with an instant reply button..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="send_new_email" class="btn btn-primary px-4">
                            <i class="bx bx-send me-1"></i> Send Email
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div> <!-- main-content -->

<?php include 'footer.php'; ?>

<!-- Real-time Poller & AJAX Scripts -->
<script>
function toggleRecipientMode() {
    const isBroadcast = document.getElementById('modeBroadcast').checked;
    const userGroup = document.getElementById('userSelectGroup');
    const notice = document.getElementById('broadcastNotice');

    if (isBroadcast) {
        userGroup.classList.add('d-none');
        notice.classList.remove('d-none');
    } else {
        userGroup.classList.remove('d-none');
        notice.classList.add('d-none');
    }
}

// Auto-scroll admin stream to bottom
function scrollAdminStream() {
    const container = document.getElementById('adminStreamContainer');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
}
window.addEventListener('load', scrollAdminStream);

// AJAX Submit Admin Reply
function submitAdminReply(e) {
    e.preventDefault();
    const form = document.getElementById('adminReplyForm');
    const btn = document.getElementById('btnSendReply');
    const textEl = document.getElementById('adminReplyText');
    const message = textEl.value.trim();
    if (!message) return;

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Sending...';

    const formData = new FormData(form);
    formData.append('action', 'send_reply');

    fetch('mail-api.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bx bx-send me-1"></i> Send Reply & Email User';

        if (res.success) {
            // Append message to stream immediately
            const list = document.getElementById('adminMessagesList');
            const newMsgEl = document.createElement('div');
            newMsgEl.className = 'd-flex justify-content-end';
            newMsgEl.innerHTML = `
                <div style="max-width: 80%;">
                    <div class="d-flex align-items-center gap-1 font-size-11 text-muted mb-1 justify-content-end">
                        <strong class="text-primary"><i class="bx bx-shield-quarter"></i> Admin Support</strong> &bull; Just now
                    </div>
                    <div class="p-3 rounded-3 shadow-sm font-size-13 bg-primary text-white">
                        ${message.replace(/\n/g, '<br>')}
                    </div>
                </div>
            `;
            list.appendChild(newMsgEl);
            textEl.value = '';
            scrollAdminStream();
        } else {
            alert('Error: ' + (res.error || 'Failed to send reply.'));
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bx bx-send me-1"></i> Send Reply & Email User';
        alert('Transmission error: ' + err);
    });
}

// Real-Time Polling: Checks for new user replies every 7 seconds
let pollActiveThread = <?php echo (int)$active_thread_id; ?>;
setInterval(function() {
    const lastIdEl = document.getElementById('lastMsgId');
    const lastId = lastIdEl ? parseInt(lastIdEl.value, 10) : 0;

    fetch(`mail-api.php?action=check_updates&active_thread=${pollActiveThread}&last_msg_id=${lastId}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                // Update counter badge
                const unreadEl = document.getElementById('unreadRepliesCount');
                if (unreadEl) {
                    unreadEl.innerText = data.unread_count;
                }

                // If new messages for active thread
                if (data.new_messages && data.new_messages.length > 0) {
                    const list = document.getElementById('adminMessagesList');
                    let maxId = lastId;

                    data.new_messages.forEach(msg => {
                        maxId = Math.max(maxId, msg.id);
                        const msgEl = document.createElement('div');
                        msgEl.className = msg.sender_type === 'admin' ? 'd-flex justify-content-end' : 'd-flex justify-content-start';
                        msgEl.innerHTML = `
                            <div style="max-width: 80%;">
                                <div class="d-flex align-items-center gap-1 font-size-11 text-muted mb-1 ${msg.sender_type === 'admin' ? 'justify-content-end' : 'justify-content-start'}">
                                    ${msg.sender_type === 'admin' ? 
                                        `<strong class="text-primary"><i class="bx bx-shield-quarter"></i> Admin Support</strong> &bull; ${msg.created_at}` : 
                                        `<strong class="text-success"><i class="bx bx-user"></i> ${msg.sender_name || 'User'}</strong> &bull; ${msg.created_at}`}
                                </div>
                                <div class="p-3 rounded-3 shadow-sm font-size-13 ${msg.sender_type === 'admin' ? 'bg-primary text-white' : 'bg-white text-dark border'}">
                                    ${msg.message}
                                </div>
                            </div>
                        `;
                        list.appendChild(msgEl);
                    });

                    if (lastIdEl) lastIdEl.value = maxId;
                    scrollAdminStream();
                }
            }
        })
        .catch(() => {});
}, 7000);
</script>
