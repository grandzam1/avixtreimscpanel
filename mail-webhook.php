<?php
/**
 * Avix Treims - Incoming Email Webhook & Mail Piper
 * Ingests incoming emails (from cPanel mail pipe, SendGrid, Mailgun, or HTTP Webhook)
 * and automatically matches them to support threads in the Admin Panel.
 */
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/mail-helper.php';

// Prevent direct accidental browser access
if (php_sapi_name() !== 'cli' && empty($_POST) && empty($_GET)) {
    http_response_code(200);
    echo "Avix Treims Ingestion Webhook Ready.";
    exit;
}

$raw_input = file_get_contents('php://input');
$subject = '';
$from_email = '';
$from_name = '';
$body_text = '';

// Mode A: HTTP Webhook Payload (JSON or POST multipart)
if (!empty($_POST['subject']) || !empty($_POST['body-plain']) || !empty($_POST['text'])) {
    $subject    = $_POST['subject'] ?? '';
    $from_email = $_POST['from'] ?? ($_POST['sender'] ?? '');
    $body_text  = $_POST['body-plain'] ?? ($_POST['text'] ?? ($_POST['message'] ?? ''));
} elseif (!empty($raw_input) && ($json = json_decode($raw_input, true))) {
    $subject    = $json['subject'] ?? '';
    $from_email = $json['from'] ?? ($json['sender'] ?? '');
    $body_text  = $json['body'] ?? ($json['text'] ?? ($json['message'] ?? ''));
} elseif (php_sapi_name() === 'cli' || !empty($raw_input)) {
    // Mode B: Standard Input Email Pipe (Raw RFC 822 Email Stream)
    $email_raw = !empty($raw_input) ? $raw_input : file_get_contents('php://stdin');
    if (!empty($email_raw)) {
        // Extract Subject
        if (preg_match('/^Subject:\s*(.*)$/mi', $email_raw, $matches)) {
            $subject = trim($matches[1]);
        }
        // Extract From
        if (preg_match('/^From:\s*(.*)$/mi', $email_raw, $matches)) {
            $from_line = trim($matches[1]);
            if (preg_match('/<([^>]+)>/', $from_line, $m2)) {
                $from_email = trim($m2[1]);
            } else {
                $from_email = $from_line;
            }
        }
        // Extract Body (after double newline)
        $parts = preg_split("/\r?\n\r?\n/", $email_raw, 2);
        if (isset($parts[1])) {
            $body_text = trim($parts[1]);
        }
    }
}

// Clean sender email
if (preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $from_email, $match_email)) {
    $from_email = $match_email[0];
}

// Check for Ticket Code in Subject or Body (e.g. TRX-12345)
$ticket_code = '';
if (preg_match('/TRX-\d{5}/i', $subject, $match_ticket)) {
    $ticket_code = strtoupper($match_ticket[0]);
} elseif (preg_match('/TRX-\d{5}/i', $body_text, $match_ticket)) {
    $ticket_code = strtoupper($match_ticket[0]);
}

if (!empty($ticket_code) && !empty($body_text) && $link) {
    $t_code_esc = mysqli_real_escape_string($link, $ticket_code);
    $th_q = mysqli_query($link, "SELECT id, user_name, user_email FROM support_threads WHERE ticket_code = '$t_code_esc' LIMIT 1");
    if ($th_q && $th = mysqli_fetch_assoc($th_q)) {
        $thread_id = (int)$th['id'];
        $sender_name = !empty($from_name) ? $from_name : (!empty($th['user_name']) ? $th['user_name'] : $from_email);

        post_thread_reply($link, $thread_id, 'user', $sender_name, $from_email, $body_text);
        http_response_code(200);
        echo json_encode(['success' => true, 'ticket' => $ticket_code, 'status' => 'ingested_to_admin_panel']);
        exit;
    }
}

http_response_code(200);
echo json_encode(['success' => false, 'message' => 'Processed. No matching open ticket identified.']);
