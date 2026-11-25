<?php
/**
 * Avix Treims - Direct 1-Click Support & Email Reply Portal
 * Allows users to review tickets and reply directly to Admin from email links.
 */
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/mail-helper.php';

$ticket = isset($_GET['ticket']) ? trim($_GET['ticket']) : (isset($_POST['ticket']) ? trim($_POST['ticket']) : '');
$token  = isset($_GET['token'])  ? trim($_GET['token'])  : (isset($_POST['token'])  ? trim($_POST['token'])  : '');

$error = '';
$success = '';
$thread = null;
$messages = [];

if (empty($ticket)) {
    $error = "Invalid or missing support ticket identifier.";
} else {
    $ticket_esc = mysqli_real_escape_string($link, $ticket);
    $query_str = "SELECT * FROM `support_threads` WHERE `ticket_code` = '$ticket_esc'";
    if (!empty($token)) {
        $token_esc = mysqli_real_escape_string($link, $token);
        $query_str .= " AND `reply_token` = '$token_esc'";
    }
    $query_str .= " LIMIT 1";

    $res = mysqli_query($link, $query_str);
    if ($res && mysqli_num_rows($res) > 0) {
        $thread = mysqli_fetch_assoc($res);
        $thread_id = (int)$thread['id'];

        // Handle user reply submission
        if (isset($_POST['send_reply'])) {
            $reply_msg = trim($_POST['message'] ?? '');
            if (empty($reply_msg)) {
                $error = "Please type a message before submitting.";
            } else {
                $user_name = !empty($thread['user_name']) ? $thread['user_name'] : $thread['user_email'];
                $user_email = $thread['user_email'];

                if (post_thread_reply($link, $thread_id, 'user', $user_name, $user_email, $reply_msg)) {
                    $success = "Your response has been delivered directly to the Executive Support Desk in the Admin Panel!";
                    // Refresh thread data
                    $res = mysqli_query($link, "SELECT * FROM `support_threads` WHERE `id` = $thread_id LIMIT 1");
                    $thread = mysqli_fetch_assoc($res);
                } else {
                    $error = "Failed to send response. Please try again.";
                }
            }
        }

        // Fetch thread messages
        $m_res = mysqli_query($link, "SELECT * FROM `support_messages` WHERE `thread_id` = $thread_id ORDER BY `created_at` ASC");
        while ($m_row = mysqli_fetch_assoc($m_res)) {
            $messages[] = $m_row;
        }
    } else {
        $error = "Support ticket could not be found or access token has expired.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Support Ticket <?php echo htmlspecialchars($ticket); ?> - <?php echo htmlspecialchars($sitename); ?></title>
    <link rel="shortcut icon" href="images/bitcoin31.png">
    
    <!-- PWA & Capabilities -->
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#090e0b">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- SweetAlert & jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <link rel="stylesheet" href="pwa.css">

    <style>
        :root {
            --primary-accent: #ccff00;
            --primary-accent-glow: rgba(204, 255, 0, 0.28);
            --bg-dark: #090e0b;
            --bg-card: #111814;
            --card-border: rgba(255, 255, 255, 0.09);
            --text-main: #ffffff;
            --text-muted: #94a3b8;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-main);
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            padding: 30px 16px 80px 16px;
            background-image: 
                radial-gradient(circle at 50% 10%, rgba(204, 255, 0, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(0, 229, 255, 0.05) 0%, transparent 40%);
            background-attachment: fixed;
        }

        .portal-container {
            max-width: 720px;
            margin: 0 auto;
        }

        .portal-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 12px 28px;
            background: #080c09;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.7), 0 0 16px rgba(204, 255, 0, 0.12);
            text-decoration: none;
            margin-bottom: 16px;
        }

        .ticket-card {
            background: var(--bg-card);
            border: 1px solid var(--card-border);
            border-radius: 26px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.7);
            overflow: hidden;
        }

        .ticket-top {
            padding: 24px 28px;
            background: #141d17;
            border-bottom: 1px solid var(--card-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 14px;
        }

        .ticket-tag {
            background: rgba(204, 255, 0, 0.15);
            color: var(--primary-accent);
            border: 1px solid var(--primary-accent);
            padding: 5px 14px;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 800;
            letter-spacing: 0.5px;
        }

        .ticket-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
            font-weight: 700;
            padding: 5px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
            color: #e2e8f0;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #2ecc71;
            box-shadow: 0 0 8px #2ecc71;
        }

        .messages-stream {
            padding: 28px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            max-height: 520px;
            overflow-y: auto;
        }

        .msg-bubble-wrap {
            display: flex;
            flex-direction: column;
            max-width: 85%;
        }

        .msg-bubble-wrap.admin {
            align-self: flex-start;
        }

        .msg-bubble-wrap.user {
            align-self: flex-end;
        }

        .msg-sender-meta {
            font-size: 0.76rem;
            color: var(--text-muted);
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .msg-bubble-wrap.user .msg-sender-meta {
            justify-content: flex-end;
        }

        .msg-bubble {
            padding: 16px 20px;
            border-radius: 18px;
            font-size: 0.94rem;
            line-height: 1.65;
            word-break: break-word;
        }

        .msg-bubble-wrap.admin .msg-bubble {
            background: #19251e;
            border: 1px solid rgba(204, 255, 0, 0.25);
            color: #f1f5f9;
            border-bottom-left-radius: 4px;
            box-shadow: 0 4px 18px rgba(0,0,0,0.4);
        }

        .msg-bubble-wrap.user .msg-bubble {
            background: #24342a;
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #ffffff;
            border-bottom-right-radius: 4px;
            box-shadow: 0 4px 18px rgba(0,0,0,0.4);
        }

        .reply-composer {
            padding: 24px 28px;
            background: #0d1410;
            border-top: 1px solid var(--card-border);
        }

        .reply-textarea {
            width: 100%;
            background: #141d17;
            border: 1px solid var(--card-border);
            color: #ffffff;
            border-radius: 16px;
            padding: 16px;
            font-size: 0.95rem;
            font-family: inherit;
            resize: vertical;
            min-height: 110px;
            outline: none;
            transition: all 0.2s ease;
        }

        .reply-textarea:focus {
            border-color: var(--primary-accent);
            box-shadow: 0 0 0 3px rgba(204, 255, 0, 0.2);
            background: #18221c;
        }

        .reply-btn {
            background: #ccff00;
            color: #000000;
            font-weight: 800;
            font-size: 0.92rem;
            border: none;
            padding: 14px 28px;
            border-radius: 999px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 14px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 16px rgba(204, 255, 0, 0.35);
        }

        .reply-btn:hover {
            background: #d8ff33;
            transform: translateY(-2px);
        }

        .reply-btn:active {
            transform: scale(0.98);
        }
    </style>
</head>
<body>

<div class="portal-container">
    <div class="portal-header">
        <a href="index.php" class="brand-badge">
            <img src="images/logo.png" alt="<?php echo htmlspecialchars($sitename); ?>" style="max-height: 40px; width: auto; object-fit: contain;">
        </a>
        <h2 style="font-size: 1.4rem; font-weight: 800; color: #fff; margin-bottom: 4px;">Direct Support & Reply Portal</h2>
        <p style="font-size: 0.88rem; color: var(--text-muted);">Encrypted two-way correspondence directly with Executive Portfolio Support</p>
    </div>

    <?php if (!empty($error) && !$thread) { ?>
        <div style="background: rgba(232,33,39,0.12); border: 1px solid #e82127; border-radius: 20px; padding: 28px; text-align: center;">
            <i class="fa-solid fa-circle-exclamation" style="font-size: 2.2rem; color: #e82127; margin-bottom: 12px; display: block;"></i>
            <h3 style="color: #fff; margin-bottom: 8px;">Access Error</h3>
            <p style="color: #cbd5e1; font-size: 0.95rem; line-height: 1.6;"><?php echo htmlspecialchars($error); ?></p>
            <a href="index.php" style="display: inline-block; margin-top: 16px; color: var(--primary-accent); text-decoration: none; font-weight: 700;">&larr; Return to Homepage</a>
        </div>
    <?php } elseif ($thread) { ?>
        <div class="ticket-card">
            <!-- Ticket Info Header -->
            <div class="ticket-top">
                <div>
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                        <span class="ticket-tag"><?php echo htmlspecialchars($thread['ticket_code']); ?></span>
                        <span class="ticket-status-pill">
                            <span class="status-dot"></span>
                            <?php echo ($thread['status'] == 'user_replied') ? 'User Replied' : 'Admin Replied'; ?>
                        </span>
                    </div>
                    <h3 style="font-size: 1.15rem; font-weight: 800; color: #fff;"><?php echo htmlspecialchars($thread['subject']); ?></h3>
                </div>
                <div style="font-size: 0.8rem; color: var(--text-muted); text-align: right;">
                    Recipient: <strong style="color:#fff;"><?php echo htmlspecialchars($thread['user_email']); ?></strong>
                </div>
            </div>

            <!-- Messages Stream -->
            <div class="messages-stream" id="messagesStream">
                <?php if (empty($messages)) { ?>
                    <div style="text-align: center; color: var(--text-muted); padding: 40px;">No messages in this conversation yet.</div>
                <?php } else { ?>
                    <?php foreach ($messages as $msg) { 
                        $isAdmin = ($msg['sender_type'] === 'admin');
                    ?>
                        <div class="msg-bubble-wrap <?php echo $isAdmin ? 'admin' : 'user'; ?>">
                            <div class="msg-sender-meta">
                                <?php if ($isAdmin) { ?>
                                    <i class="fa-solid fa-headset" style="color: var(--primary-accent);"></i>
                                    <strong>Executive Support Desk</strong>
                                <?php } else { ?>
                                    <strong><?php echo htmlspecialchars($msg['sender_name'] ?: 'You'); ?></strong>
                                    <i class="fa-solid fa-user" style="color: #38bdf8;"></i>
                                <?php } ?>
                                <span>&bull; <?php echo date('M j, Y, g:i a', strtotime($msg['created_at'])); ?></span>
                            </div>
                            <div class="msg-bubble">
                                <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>

            <!-- Reply Box -->
            <div class="reply-composer">
                <form action="reply.php" method="post" id="replyForm">
                    <input type="hidden" name="ticket" value="<?php echo htmlspecialchars($ticket); ?>">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    <label for="replyMessage" style="display: block; font-size: 0.85rem; font-weight: 700; color: #cbd5e1; margin-bottom: 8px;">
                        <i class="fa-solid fa-reply me-1"></i> Send Reply to Admin:
                    </label>
                    <textarea name="message" id="replyMessage" class="reply-textarea" placeholder="Type your response or message here. It will be sent immediately to the admin panel..." required></textarea>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                        <button type="submit" name="send_reply" class="reply-btn">
                            <i class="fa-solid fa-paper-plane"></i> Submit Response
                        </button>
                        <span style="font-size: 0.78rem; color: var(--text-muted);">
                            <i class="fa-solid fa-shield-halved text-success me-1"></i> End-to-End Encrypted Communication
                        </span>
                    </div>
                </form>
            </div>
        </div>
    <?php } ?>
</div>

<?php if (!empty($success)) { ?>
<script>
    $(document).ready(function() {
        swal({
            title: "Reply Sent!",
            text: "<?php echo addslashes($success); ?>",
            icon: "success",
            button: "Done"
        });
        // Auto scroll to bottom of stream
        const stream = document.getElementById('messagesStream');
        if (stream) stream.scrollTop = stream.scrollHeight;
    });
</script>
<?php } ?>

<?php if (!empty($error) && $thread) { ?>
<script>
    $(document).ready(function() {
        swal({
            title: "Submission Error",
            text: "<?php echo addslashes($error); ?>",
            icon: "error",
            button: "Try Again"
        });
    });
</script>
<?php } ?>

<script>
    // Auto-scroll stream to bottom on page load
    window.addEventListener('load', function() {
        const stream = document.getElementById('messagesStream');
        if (stream) stream.scrollTop = stream.scrollHeight;
    });
</script>

<script src="pwa.js"></script>
</body>
</html>
