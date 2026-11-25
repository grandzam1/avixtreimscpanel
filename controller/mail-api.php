<?php
/**
 * Avix Treims - Admin Mail Center Real-Time AJAX API
 */
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['adminid'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../mail-helper.php';

$action = $_REQUEST['action'] ?? '';

if ($action === 'send_reply') {
    $thread_id = (int)($_POST['thread_id'] ?? 0);
    $message   = trim($_POST['message'] ?? '');

    if ($thread_id <= 0 || empty($message)) {
        echo json_encode(['success' => false, 'error' => 'Missing thread ID or message body.']);
        exit;
    }

    if (post_thread_reply($link, $thread_id, 'admin', 'Executive Support', $sitemail, $message)) {
        $mail_err = $GLOBALS['last_mail_error'] ?? '';
        echo json_encode([
            'success' => true, 
            'message' => empty($mail_err) ? 'Reply recorded and email dispatched to user.' : 'Reply recorded, but mail warning: ' . $mail_err
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to save reply: ' . mysqli_error($link)]);
    }
    exit;
}

if ($action === 'check_updates') {
    $active_thread = (int)($_GET['active_thread'] ?? 0);
    $last_msg_id   = (int)($_GET['last_msg_id'] ?? 0);

    // Count unread threads (user replied)
    $unread_count = 0;
    $q_cnt = mysqli_query($link, "SELECT COUNT(*) as cnt FROM `support_threads` WHERE `status` = 'user_replied'");
    if ($q_cnt && $r_cnt = mysqli_fetch_assoc($q_cnt)) {
        $unread_count = (int)$r_cnt['cnt'];
    }

    $new_messages = [];
    if ($active_thread > 0) {
        $q_new = mysqli_query($link, "SELECT * FROM `support_messages` WHERE `thread_id` = $active_thread AND `id` > $last_msg_id ORDER BY `created_at` ASC");
        while ($q_new && $row = mysqli_fetch_assoc($q_new)) {
            $new_messages[] = [
                'id'          => (int)$row['id'],
                'sender_type' => $row['sender_type'],
                'sender_name' => $row['sender_name'],
                'message'     => nl2br(htmlspecialchars($row['message'])),
                'created_at'  => date('M j, Y, g:i a', strtotime($row['created_at']))
            ];
            // Mark as read if user sent it
            if ($row['sender_type'] === 'user') {
                mysqli_query($link, "UPDATE `support_messages` SET `is_read` = 1 WHERE `id` = " . (int)$row['id']);
            }
        }
    }

    echo json_encode([
        'success'      => true,
        'unread_count' => $unread_count,
        'new_messages' => $new_messages
    ]);
    exit;
}

echo json_encode(['success' => false, 'error' => 'Invalid action']);
