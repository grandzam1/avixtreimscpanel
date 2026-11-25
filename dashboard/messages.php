<?php
include 'header.php';
require_once __DIR__ . '/../mail-helper.php';

$feedback = '';
$feedback_type = '';

$current_user_email = !empty($email) ? $email : '';
$current_user_id = !empty($id) ? (int)$id : 0;

// Handle User Reply
if (isset($_POST['send_dashboard_reply'])) {
    $thread_id = (int)($_POST['thread_id'] ?? 0);
    $reply_msg = trim($_POST['message'] ?? '');

    if ($thread_id > 0 && !empty($reply_msg)) {
        // Verify ownership
        $t_check = mysqli_query($link, "SELECT id FROM support_threads WHERE id = $thread_id AND (user_id = $current_user_id OR user_email = '" . mysqli_real_escape_string($link, $current_user_email) . "') LIMIT 1");
        if ($t_check && mysqli_num_rows($t_check) > 0) {
            if (post_thread_reply($link, $thread_id, 'user', $display_name, $current_user_email, $reply_msg)) {
                $feedback = "Your message has been delivered directly to the Executive Admin Support Desk!";
                $feedback_type = "success";
                $_GET['thread_id'] = $thread_id;
            } else {
                $feedback = "Could not send reply. Please try again.";
                $feedback_type = "error";
            }
        }
    }
}

// Handle User Starting New Conversation
if (isset($_POST['start_new_ticket'])) {
    $subj = trim($_POST['subject'] ?? '');
    $msg  = trim($_POST['message'] ?? '');

    if (empty($subj) || empty($msg)) {
        $feedback = "Please enter both a subject and message.";
        $feedback_type = "error";
    } else {
        $ticket_code = 'TRX-' . rand(10000, 99999);
        $reply_token = bin2hex(random_bytes(24));

        $s_subj = mysqli_real_escape_string($link, $subj);
        $s_msg  = mysqli_real_escape_string($link, $msg);
        $s_name = mysqli_real_escape_string($link, $display_name);
        $s_mail = mysqli_real_escape_string($link, $current_user_email);

        $q_th = "INSERT INTO support_threads (ticket_code, reply_token, user_id, user_email, user_name, subject, status, created_at, updated_at) 
                 VALUES ('$ticket_code', '$reply_token', $current_user_id, '$s_mail', '$s_name', '$s_subj', 'user_replied', NOW(), NOW())";
        if (mysqli_query($link, $q_th)) {
            $new_tid = mysqli_insert_id($link);
            mysqli_query($link, "INSERT INTO support_messages (thread_id, sender_type, sender_name, sender_email, message, is_read, created_at) 
                                 VALUES ($new_tid, 'user', '$s_name', '$s_mail', '$s_msg', 0, NOW())");
            $feedback = "Your support ticket #$ticket_code has been created! Admin will review and respond shortly.";
            $feedback_type = "success";
            $_GET['thread_id'] = $new_tid;
        } else {
            $feedback = "Failed to create support ticket.";
            $feedback_type = "error";
        }
    }
}

// Fetch User's Active Threads
$user_threads = [];
if (!empty($current_user_email) || $current_user_id > 0) {
    $q_list = mysqli_query($link, "SELECT * FROM support_threads 
        WHERE (user_id = $current_user_id OR user_email = '" . mysqli_real_escape_string($link, $current_user_email) . "') 
        ORDER BY updated_at DESC");
    while ($q_list && $r = mysqli_fetch_assoc($q_list)) {
        $user_threads[] = $r;
    }
}

// Active Thread
$active_tid = isset($_GET['thread_id']) ? (int)$_GET['thread_id'] : (!empty($user_threads) ? (int)$user_threads[0]['id'] : 0);
$selected_thread = null;
$selected_messages = [];

if ($active_tid > 0) {
    $s_q = mysqli_query($link, "SELECT * FROM support_threads WHERE id = $active_tid AND (user_id = $current_user_id OR user_email = '" . mysqli_real_escape_string($link, $current_user_email) . "') LIMIT 1");
    if ($s_q && mysqli_num_rows($s_q) > 0) {
        $selected_thread = mysqli_fetch_assoc($s_q);
        $m_q = mysqli_query($link, "SELECT * FROM support_messages WHERE thread_id = $active_tid ORDER BY created_at ASC");
        while ($m_q && $m = mysqli_fetch_assoc($m_q)) {
            $selected_messages[] = $m;
        }
    }
}
?>

<div class="dashboard-container">
    <!-- Breadcrumb & Action -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-bottom:24px;">
        <div>
            <h2 style="font-size:1.6rem; font-weight:800; color:#fff; margin-bottom:4px;">
                <i class="fa-solid fa-envelope-open-text" style="color:var(--primary-accent); margin-right:8px;"></i> Support Desk &amp; Mail
            </h2>
            <p style="color:var(--text-muted); font-size:0.9rem; margin:0;">
                Direct, end-to-end encrypted messaging with our executive portfolio management team.
            </p>
        </div>
        <button type="button" class="btn-pill-buy" style="border:none; cursor:pointer;" onclick="document.getElementById('newTicketModal').style.display='flex'">
            <i class="fa-solid fa-plus me-1"></i> New Support Ticket
        </button>
    </div>

    <?php if (!empty($feedback)) { ?>
        <div style="background:<?php echo ($feedback_type === 'success') ? 'rgba(46, 204, 113, 0.15)' : 'rgba(232, 33, 39, 0.15)'; ?>; border:1px solid <?php echo ($feedback_type === 'success') ? '#2ecc71' : '#e82127'; ?>; border-radius:16px; padding:16px 20px; color:#fff; margin-bottom:24px; display:flex; align-items:center; gap:12px;">
            <i class="fa-solid <?php echo ($feedback_type === 'success') ? 'fa-circle-check text-success' : 'fa-circle-exclamation text-danger'; ?> font-size-20"></i>
            <div><?php echo htmlspecialchars($feedback); ?></div>
        </div>
    <?php } ?>

    <!-- Two-Column Mailbox Layout -->
    <div style="display:grid; grid-template-columns: 320px 1fr; gap:24px; align-items:start;">
        
        <!-- Left: Conversations List -->
        <div class="screen-card" style="padding:20px !important;">
            <div style="font-size:0.82rem; font-weight:800; text-transform:uppercase; letter-spacing:1px; color:var(--text-muted); margin-bottom:14px;">
                Your Conversations
            </div>

            <div style="display:flex; flex-direction:column; gap:10px; max-height:560px; overflow-y:auto;">
                <?php if (empty($user_threads)) { ?>
                    <div style="text-align:center; padding:30px 10px; color:var(--text-muted); font-size:0.85rem;">
                        <i class="fa-solid fa-inbox" style="font-size:2rem; margin-bottom:10px; display:block; opacity:0.5;"></i>
                        No conversations yet.<br>Click "New Support Ticket" above to reach admin.
                    </div>
                <?php } else { ?>
                    <?php foreach ($user_threads as $th) { 
                        $isSel = ($th['id'] == $active_tid);
                    ?>
                        <a href="messages.php?thread_id=<?php echo $th['id']; ?>" 
                           style="display:block; padding:14px 16px; border-radius:14px; text-decoration:none; background:<?php echo $isSel ? '#1a2620' : '#0f1512'; ?>; border:1px solid <?php echo $isSel ? 'var(--primary-accent)' : 'var(--card-border)'; ?>; transition:all 0.2s ease;">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                                <span style="font-size:0.75rem; font-weight:800; color:var(--primary-accent);">
                                    <?php echo htmlspecialchars($th['ticket_code']); ?>
                                </span>
                                <span style="font-size:0.7rem; color:var(--text-muted);">
                                    <?php echo date('M j', strtotime($th['updated_at'])); ?>
                                </span>
                            </div>
                            <div style="font-size:0.9rem; font-weight:700; color:#fff; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-bottom:6px;">
                                <?php echo htmlspecialchars($th['subject']); ?>
                            </div>
                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                <span style="font-size:0.72rem; padding:3px 8px; border-radius:99px; background:<?php echo ($th['status'] === 'user_replied') ? 'rgba(255,255,255,0.08)' : 'rgba(204,255,0,0.15)'; ?>; color:<?php echo ($th['status'] === 'user_replied') ? '#94a3b8' : 'var(--primary-accent)'; ?>; font-weight:700;">
                                    <?php echo ($th['status'] === 'user_replied') ? 'Awaiting Admin' : 'Admin Replied'; ?>
                                </span>
                            </div>
                        </a>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>

        <!-- Right: Active Thread Messages -->
        <div class="screen-card" style="padding:0 !important; overflow:hidden;">
            <?php if (!$selected_thread) { ?>
                <div style="padding:60px 24px; text-align:center; color:var(--text-muted);">
                    <i class="fa-solid fa-envelope-open" style="font-size:3rem; margin-bottom:16px; opacity:0.4;"></i>
                    <h4 style="color:#fff;">No Conversation Selected</h4>
                    <p style="font-size:0.9rem;">Select a ticket from the left list or create a new one.</p>
                </div>
            <?php } else { ?>
                <!-- Thread Header -->
                <div style="padding:22px 28px; background:#18221c; border-bottom:1px solid var(--card-border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                    <div>
                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
                            <span style="background:var(--primary-accent); color:#000; font-weight:800; font-size:0.75rem; padding:2px 10px; border-radius:99px;">
                                <?php echo htmlspecialchars($selected_thread['ticket_code']); ?>
                            </span>
                            <span style="font-size:0.8rem; color:var(--text-muted);">
                                Status: <strong style="color:#fff;"><?php echo ($selected_thread['status'] === 'user_replied') ? 'Delivered to Admin Desk' : 'Executive Response Received'; ?></strong>
                            </span>
                        </div>
                        <h3 style="font-size:1.25rem; font-weight:800; color:#fff; margin:0;">
                            <?php echo htmlspecialchars($selected_thread['subject']); ?>
                        </h3>
                    </div>
                </div>

                <!-- Messages Stream -->
                <div style="padding:28px; display:flex; flex-direction:column; gap:18px; max-height:420px; overflow-y:auto; background:#101713;" id="dashMsgStream">
                    <?php foreach ($selected_messages as $msg) { 
                        $isAdmin = ($msg['sender_type'] === 'admin');
                    ?>
                        <div style="display:flex; flex-direction:column; max-width:85%; align-self:<?php echo $isAdmin ? 'flex-start' : 'flex-end'; ?>;">
                            <div style="font-size:0.75rem; color:var(--text-muted); margin-bottom:4px; display:flex; align-items:center; gap:6px; <?php echo $isAdmin ? '' : 'justify-content:flex-end;'; ?>">
                                <?php if ($isAdmin) { ?>
                                    <strong style="color:var(--primary-accent);"><i class="fa-solid fa-headset"></i> Executive Support</strong> &bull; <?php echo date('M j, g:i a', strtotime($msg['created_at'])); ?>
                                <?php } else { ?>
                                    <strong style="color:#38bdf8;">You</strong> &bull; <?php echo date('M j, g:i a', strtotime($msg['created_at'])); ?>
                                <?php } ?>
                            </div>
                            <div style="padding:16px 20px; border-radius:18px; font-size:0.92rem; line-height:1.65; <?php echo $isAdmin ? 'background:#1a2720; border:1px solid rgba(204,255,0,0.25); color:#f1f5f9; border-bottom-left-radius:4px;' : 'background:#24362c; border:1px solid rgba(255,255,255,0.1); color:#fff; border-bottom-right-radius:4px;'; ?>">
                                <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <!-- Reply Composer -->
                <div style="padding:20px 28px; background:#141c17; border-top:1px solid var(--card-border);">
                    <form action="messages.php?thread_id=<?php echo $active_tid; ?>" method="post">
                        <input type="hidden" name="thread_id" value="<?php echo $active_tid; ?>">
                        <textarea name="message" rows="3" style="width:100%; background:#0e1511; border:1px solid var(--card-border); color:#fff; border-radius:14px; padding:14px; font-size:0.92rem; font-family:inherit; outline:none; resize:vertical; margin-bottom:12px;" placeholder="Type your response to the support team here..." required></textarea>
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <span style="font-size:0.75rem; color:var(--text-muted);">
                                <i class="fa-solid fa-lock text-success me-1"></i> Messages are encrypted and notify admin immediately.
                            </span>
                            <button type="submit" name="send_dashboard_reply" class="btn-pill-buy" style="border:none; cursor:pointer;">
                                <i class="fa-solid fa-paper-plane me-1"></i> Send Reply
                            </button>
                        </div>
                    </form>
                </div>
            <?php } ?>
        </div>

    </div>
</div>

<!-- Start New Support Ticket Modal -->
<div id="newTicketModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:9999; align-items:center; justify-content:center; padding:16px; backdrop-filter:blur(10px);">
    <div style="background:#141c17; border:1px solid var(--card-border); border-radius:24px; max-width:540px; width:100%; padding:30px; box-shadow:0 20px 60px rgba(0,0,0,0.9);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h3 style="color:#fff; font-size:1.3rem; font-weight:800; margin:0;">
                <i class="fa-solid fa-ticket" style="color:var(--primary-accent); margin-right:8px;"></i> Open Support Ticket
            </h3>
            <button type="button" onclick="document.getElementById('newTicketModal').style.display='none'" style="background:none; border:none; color:#94a3b8; font-size:1.5rem; cursor:pointer;">&times;</button>
        </div>

        <form action="messages.php" method="post">
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:0.82rem; font-weight:700; color:#cbd5e1; margin-bottom:6px;">Subject / Topic:</label>
                <input type="text" name="subject" class="form-control" style="width:100%; background:#0e1511; border:1px solid var(--card-border); color:#fff; border-radius:12px; padding:12px 14px;" placeholder="e.g. Deposit Inquiry, Verification, Plan Allocation..." required>
            </div>
            <div style="margin-bottom:22px;">
                <label style="display:block; font-size:0.82rem; font-weight:700; color:#cbd5e1; margin-bottom:6px;">Your Message:</label>
                <textarea name="message" rows="5" style="width:100%; background:#0e1511; border:1px solid var(--card-border); color:#fff; border-radius:12px; padding:12px 14px; font-family:inherit; resize:vertical;" placeholder="Explain your inquiry in detail. Our executive desk will review and respond directly to your email and dashboard." required></textarea>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:12px;">
                <button type="button" onclick="document.getElementById('newTicketModal').style.display='none'" style="background:rgba(255,255,255,0.08); border:none; color:#cbd5e1; padding:10px 20px; border-radius:12px; cursor:pointer; font-weight:600;">Cancel</button>
                <button type="submit" name="start_new_ticket" class="btn-pill-buy" style="border:none; cursor:pointer;">Submit Ticket</button>
            </div>
        </form>
    </div>
</div>

<script>
    window.addEventListener('load', function() {
        const stream = document.getElementById('dashMsgStream');
        if (stream) stream.scrollTop = stream.scrollHeight;
    });
</script>

<?php include 'footer.php'; ?>
