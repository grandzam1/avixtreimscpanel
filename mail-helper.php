<?php
/**
 * Avix Treims - Bidirectional Support & Mail Engine
 * Handles dual-mode transmission (PHPMailer SMTP / Native PHP Mail)
 * and generates luxury responsive email templates with 1-click reply integration.
 */

// PHP 8+ Compatibility Polyfills for PHPMailer
if (!defined('FILTER_FLAG_HOST_REQUIRED')) {
    define('FILTER_FLAG_HOST_REQUIRED', 0);
}
if (!defined('FILTER_FLAG_SCHEME_REQUIRED')) {
    define('FILTER_FLAG_SCHEME_REQUIRED', 0);
}

if (file_exists(__DIR__ . '/config.local.php')) {
    include_once __DIR__ . '/config.local.php';
}
$GLOBALS['smtp_fallback_pass'] = $GLOBALS['smtp_fallback_pass'] ?? ($smtp_fallback_pass ?? (getenv('SMTP_PASS') ?: 'CHANGE_ME_SMTP_PASS'));

if (!defined('AVIX_MAIL_HELPER_LOADED')) {
    define('AVIX_MAIL_HELPER_LOADED', true);

    /**
     * Send formatted HTML email with 1-click reply capability
     */
    function send_platform_email($to_email, $to_name, $subject, $message_text, $ticket_code = null, $reply_token = null, $is_broadcast = false) {
        global $sitename, $sitemail, $siteurl, $theme_color, $link, $mail_host, $mail_user, $mail_pass, $mail_port;

        $active_host = !empty($_SERVER['HTTP_HOST']) ? preg_replace('/^www\./i', '', $_SERVER['HTTP_HOST']) : 'avixtreimsco.com';

        $site_name = !empty($sitename) ? $sitename : 'Avix Treims';
        $site_mail = (!empty($sitemail) && strpos($sitemail, 'avixtreims.com') === false) ? $sitemail : ('noreply@' . $active_host);
        $site_url  = !empty($siteurl)  ? rtrim($siteurl, '/') : ('https://' . $active_host);
        $accent    = !empty($theme_color) ? $theme_color : '#ccff00';

        $smtp_host = (!empty($mail_host) && $mail_host !== 'mail.avixtreims.com') ? $mail_host : ('mail.' . $active_host);
        $smtp_user = (!empty($mail_user) && $mail_user !== 'noreply@avixtreims.com') ? $mail_user : ('noreply@' . $active_host);
        $smtp_pass = !empty($mail_pass) ? $mail_pass : $GLOBALS['smtp_fallback_pass'];
        $smtp_port = !empty($mail_port) ? (int)$mail_port : 465;

        // Direct 1-click reply link
        $reply_url = '';
        if (!empty($ticket_code) && !empty($reply_token)) {
            $reply_url = $site_url . '/reply.php?ticket=' . urlencode($ticket_code) . '&token=' . urlencode($reply_token);
        } elseif (!empty($ticket_code)) {
            $reply_url = $site_url . '/reply.php?ticket=' . urlencode($ticket_code);
        }

        // Format email subject with ticket code for easy email client threading
        $full_subject = !empty($ticket_code) ? "[" . $ticket_code . "] " . $subject : $subject;

        // Clean message paragraphs
        $message_html = nl2br(htmlspecialchars($message_text));

        // Luxury Obsidian Email Template
        $email_body = '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>' . htmlspecialchars($full_subject) . '</title>
        </head>
        <body style="margin:0; padding:0; background-color:#090e0b; font-family:-apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif; color:#ffffff;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#090e0b; padding:30px 15px;">
                <tr>
                    <td align="center">
                        <table role="presentation" width="100%" max-width="600" cellspacing="0" cellpadding="0" border="0" style="max-width:600px; background-color:#111814; border:1px solid rgba(255,255,255,0.1); border-radius:24px; overflow:hidden; box-shadow:0 20px 50px rgba(0,0,0,0.8);">
                            <!-- Top Brand Header Bar -->
                            <tr>
                                <td style="background:linear-gradient(90deg, #ccff00, #00e5ff, #e82127); height:4px; line-height:4px; font-size:1px;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td style="padding:32px 36px 20px 36px; text-align:center; background-color:#0d1410; border-bottom:1px solid rgba(255,255,255,0.06);">
                                    <div style="display:inline-block; padding:10px 24px; background:#080c09; border:1px solid rgba(255,255,255,0.12); border-radius:18px; box-shadow:0 6px 20px rgba(0,0,0,0.6);">
                                        <span style="font-size:24px; font-weight:900; letter-spacing:2px; color:#ffffff;">AVIX <span style="color:#ccff00;">TREIMS</span></span>
                                    </div>
                                    <div style="font-size:10px; font-weight:800; letter-spacing:3px; color:#94a3b8; margin-top:8px; text-transform:uppercase;">
                                        DIGITAL ASSETS &bull; ADVANCED TECHNOLOGY PLATFORM
                                    </div>
                                </td>
                            </tr>

                            <!-- Email Content Body -->
                            <tr>
                                <td style="padding:36px 36px 28px 36px;">
                                    <div style="font-size:13px; color:' . $accent . '; font-weight:800; text-transform:uppercase; letter-spacing:1.5px; margin-bottom:8px;">
                                        ' . ($is_broadcast ? 'Official System Announcement' : (!empty($ticket_code) ? 'Executive Support Desk &bull; ' . htmlspecialchars($ticket_code) : 'Direct Member Notification')) . '
                                    </div>
                                    <h1 style="font-size:22px; font-weight:900; color:#ffffff; margin:0 0 20px 0; line-height:1.3;">
                                        ' . htmlspecialchars($subject) . '
                                    </h1>

                                    <p style="font-size:15px; color:#cbd5e1; line-height:1.7; margin:0 0 24px 0;">
                                        Dear <strong>' . htmlspecialchars($to_name ?: 'Investor') . '</strong>,
                                    </p>

                                    <!-- Main Message Card -->
                                    <div style="background-color:#16201a; border-left:4px solid ' . $accent . '; border-radius:0 16px 16px 0; padding:22px 24px; margin-bottom:28px; font-size:15px; line-height:1.75; color:#f1f5f9;">
                                        ' . $message_html . '
                                    </div>';

        // If direct 1-click reply is available, show the interactive reply button!
        if (!empty($reply_url) && !$is_broadcast) {
            $email_body .= '
                                    <!-- 1-Click Reply Action Banner -->
                                    <div style="background:rgba(204,255,0,0.06); border:1px solid rgba(204,255,0,0.25); border-radius:18px; padding:24px; text-align:center; margin-bottom:28px;">
                                        <div style="font-size:14px; color:#ffffff; font-weight:700; margin-bottom:6px;">
                                            Need to respond or send information back?
                                        </div>
                                        <p style="font-size:13px; color:#94a3b8; margin:0 0 16px 0;">
                                            You can reply directly to our executive support desk without logging in. Your response will appear immediately in the admin panel.
                                        </p>
                                        <a href="' . htmlspecialchars($reply_url) . '" target="_blank" style="display:inline-block; background-color:#ccff00; color:#000000; font-weight:800; font-size:14px; text-decoration:none; padding:13px 32px; border-radius:999px; box-shadow:0 6px 20px rgba(204,255,0,0.35); text-transform:uppercase; letter-spacing:0.5px;">
                                            &crarr; Reply Directly to Admin
                                        </a>
                                    </div>';
        }

        $email_body .= '
                                    <p style="font-size:13px; color:#64748b; line-height:1.6; margin:0;">
                                        If you have an active account, you can also view and manage this conversation directly inside your <a href="' . htmlspecialchars($site_url) . '/dashboard/customer.php" style="color:' . $accent . '; text-decoration:none;">Member Dashboard</a>.
                                    </p>
                                </td>
                            </tr>

                            <!-- Email Footer -->
                            <tr>
                                <td style="padding:24px 36px; background-color:#0b100d; border-top:1px solid rgba(255,255,255,0.06); text-align:center; font-size:12px; color:#64748b; line-height:1.6;">
                                    <p style="margin:0 0 6px 0;">
                                        &copy; ' . date('Y') . ' ' . htmlspecialchars($site_name) . '. All rights reserved.
                                    </p>
                                    <p style="margin:0; font-size:11px; color:#475569;">
                                        This is an authenticated message from ' . htmlspecialchars($site_name) . ' Wealth Management. Never share your password or withdrawal PIN with anyone.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>';

        $sent = false;
        $error_log_msg = "";

        // 1. Primary Attempt: PHPMailer SMTP Connection
        $phpmailer_path = __DIR__ . '/PHPMailer/PHPMailer.php';
        if (file_exists($phpmailer_path)) {
            require_once $phpmailer_path;
            require_once __DIR__ . '/PHPMailer/Exception.php';
            require_once __DIR__ . '/PHPMailer/SMTP.php';

            try {
                $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                $mail->CharSet = 'UTF-8';
                $mail->isSMTP();
                $mail->Host    = $smtp_host;
                $mail->Timeout = 10;

                // Handle localhost/local relay vs authenticated remote SMTP
                if ($smtp_host === 'localhost' || $smtp_host === '127.0.0.1') {
                    $mail->SMTPAuth   = false;
                    $mail->SMTPSecure = '';
                    $mail->SMTPAutoTLS = false;
                    $mail->Port       = ($smtp_port && $smtp_port != 465) ? (int)$smtp_port : 25;
                } else {
                    $mail->SMTPAuth   = true;
                    $mail->Username   = $smtp_user;
                    $mail->Password   = $smtp_pass;
                    $mail->Port       = (int)$smtp_port ?: 465;

                    if ($mail->Port === 465) {
                        $mail->SMTPSecure = 'ssl';
                    } elseif ($mail->Port === 587) {
                        $mail->SMTPSecure = 'tls';
                    } else {
                        $mail->SMTPSecure = '';
                        $mail->SMTPAutoTLS = true;
                    }

                    // Avoid fatal SSL handshake failure on cPanel shared/self-signed certificates
                    $mail->SMTPOptions = [
                        'ssl' => [
                            'verify_peer'       => false,
                            'verify_peer_name'  => false,
                            'allow_self_signed' => true
                        ]
                    ];
                }

                // Determine valid From address
                $from_addr = filter_var($smtp_user, FILTER_VALIDATE_EMAIL) ? $smtp_user : $site_mail;
                if (!filter_var($from_addr, FILTER_VALIDATE_EMAIL)) {
                    $from_addr = 'noreply@' . $active_host;
                }

                $mail->setFrom($from_addr, $site_name);
                $mail->addAddress($to_email, $to_name ?: $to_email);

                if (!empty($site_mail) && filter_var($site_mail, FILTER_VALIDATE_EMAIL)) {
                    $mail->addReplyTo($site_mail, $site_name . ' Support');
                }

                $mail->isHTML(true);
                $mail->Subject = $full_subject;
                $mail->Body    = $email_body;
                $mail->AltBody = strip_tags($message_text) . (!empty($reply_url) ? "\n\nReply directly at: " . $reply_url : '');

                $mail->send();
                $sent = true;
            } catch (\Throwable $e) {
                $error_log_msg = "PHPMailer ($smtp_host:$smtp_port) failed: " . $e->getMessage();
                error_log("Avix Treims PHPMailer Error: " . $error_log_msg);
            }
        }

        // 2. Secondary Attempt: Local Sendmail / Native mail() Fallback
        if (!$sent) {
            $envelope_from = filter_var($site_mail, FILTER_VALIDATE_EMAIL) ? $site_mail : ('noreply@' . $active_host);
            $headers  = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= "From: " . $site_name . " <" . $envelope_from . ">\r\n";
            $headers .= "Reply-To: " . $site_name . " Support <" . $envelope_from . ">\r\n";
            $headers .= "Return-Path: <" . $envelope_from . ">\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

            // cPanel Exim requires envelope sender flag -f to accept mail without dropping
            $native_sent = @mail($to_email, $full_subject, $email_body, $headers, "-f" . $envelope_from);
            if (!$native_sent) {
                $native_sent = @mail($to_email, $full_subject, $email_body, $headers);
            }

            if ($native_sent) {
                $sent = true;
            } else {
                $error_log_msg .= ($error_log_msg ? " | " : "") . "Native mail() failed to dispatch.";
            }
        }

        $GLOBALS['last_mail_error'] = $error_log_msg;
        return $sent;
    }

    /**
     * Diagnostic testing tool for SMTP and mail dispatch
     */
    function test_mail_connection($test_to_email, $custom_host = null, $custom_port = null, $custom_user = null, $custom_pass = null) {
        global $sitename, $sitemail, $mail_host, $mail_user, $mail_pass, $mail_port;

        $active_host = !empty($_SERVER['HTTP_HOST']) ? preg_replace('/^www\./i', '', $_SERVER['HTTP_HOST']) : 'avixtreimsco.com';

        $host = $custom_host !== null ? $custom_host : ((!empty($mail_host) && $mail_host !== 'mail.avixtreims.com') ? $mail_host : ('mail.' . $active_host));
        $port = $custom_port !== null ? (int)$custom_port : (!empty($mail_port) ? (int)$mail_port : 465);
        $user = $custom_user !== null ? $custom_user : ((!empty($mail_user) && $mail_user !== 'noreply@avixtreims.com') ? $mail_user : ('noreply@' . $active_host));
        $pass = $custom_pass !== null ? $custom_pass : (!empty($mail_pass) ? $mail_pass : $GLOBALS['smtp_fallback_pass']);

        $log_buffer = [];
        $debug_fn = function($str) use (&$log_buffer) {
            $log_buffer[] = trim($str);
        };

        $phpmailer_path = __DIR__ . '/PHPMailer/PHPMailer.php';
        if (!file_exists($phpmailer_path)) {
            return [
                'success' => false,
                'log'     => 'PHPMailer files not found in ' . __DIR__ . '/PHPMailer/',
                'error'   => 'PHPMailer library missing.'
            ];
        }

        require_once $phpmailer_path;
        require_once __DIR__ . '/PHPMailer/Exception.php';
        require_once __DIR__ . '/PHPMailer/SMTP.php';

        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->CharSet = 'UTF-8';
            $mail->SMTPDebug = 2; // Full client and server conversation
            $mail->Debugoutput = $debug_fn;

            $mail->isSMTP();
            $mail->Host    = $host;
            $mail->Timeout = 10;

            if ($host === 'localhost' || $host === '127.0.0.1') {
                $mail->SMTPAuth   = false;
                $mail->SMTPSecure = '';
                $mail->SMTPAutoTLS = false;
                $mail->Port       = ($port && $port != 465) ? (int)$port : 25;
            } else {
                $mail->SMTPAuth   = true;
                $mail->Username   = $user;
                $mail->Password   = $pass;
                $mail->Port       = (int)$port ?: 465;

                if ($mail->Port === 465) {
                    $mail->SMTPSecure = 'ssl';
                } elseif ($mail->Port === 587) {
                    $mail->SMTPSecure = 'tls';
                } else {
                    $mail->SMTPSecure = '';
                    $mail->SMTPAutoTLS = true;
                }

                $mail->SMTPOptions = [
                    'ssl' => [
                        'verify_peer'       => false,
                        'verify_peer_name'  => false,
                        'allow_self_signed' => true
                    ]
                ];
            }

            $from_addr = filter_var($user, FILTER_VALIDATE_EMAIL) ? $user : ('noreply@' . $active_host);
            $mail->setFrom($from_addr, 'Avix Treims Mail Diagnostic');
            $mail->addAddress($test_to_email);

            $mail->isHTML(true);
            $mail->Subject = 'Avix Treims SMTP Verification Test - ' . date('Y-m-d H:i:s');
            $mail->Body    = '<h3>SMTP Connection Confirmed!</h3><p>Your Avix Treims mail server gateway is successfully delivering outgoing emails.</p><p><strong>Server Host:</strong> ' . htmlspecialchars($host) . '<br><strong>Port:</strong> ' . (int)$port . '<br><strong>Account:</strong> ' . htmlspecialchars($user) . '</p>';

            $mail->send();
            return [
                'success' => true,
                'log'     => implode("\n", $log_buffer),
                'error'   => null
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'log'     => implode("\n", $log_buffer),
                'error'   => $e->getMessage()
            ];
        }
    }

    /**
     * Create a new support thread and send initial email
     */
    function create_support_thread($link, $user_id, $user_email, $user_name, $subject, $message, $is_broadcast = 0) {
        if (!$link) return false;

        $ticket_code = 'TRX-' . rand(10000, 99999);
        $reply_token = bin2hex(random_bytes(24));

        $u_id    = (int)$user_id;
        $u_email = mysqli_real_escape_string($link, trim($user_email));
        $u_name  = mysqli_real_escape_string($link, trim($user_name));
        $subj    = mysqli_real_escape_string($link, trim($subject));
        $msg     = mysqli_real_escape_string($link, trim($message));
        $bcast   = (int)$is_broadcast;

        $sql_thread = "INSERT INTO `support_threads` 
            (`ticket_code`, `reply_token`, `user_id`, `user_email`, `user_name`, `subject`, `is_broadcast`, `status`, `created_at`, `updated_at`) 
            VALUES ('$ticket_code', '$reply_token', $u_id, '$u_email', '$u_name', '$subj', $bcast, 'admin_replied', NOW(), NOW())";

        if (mysqli_query($link, $sql_thread)) {
            $thread_id = mysqli_insert_id($link);

            // Record initial message
            $sql_msg = "INSERT INTO `support_messages` 
                (`thread_id`, `sender_type`, `sender_name`, `sender_email`, `message`, `is_read`, `created_at`) 
                VALUES ($thread_id, 'admin', 'Executive Support', '$u_email', '$msg', 1, NOW())";
            mysqli_query($link, $sql_msg);

            // Send real email to recipient
            $mail_ok = send_platform_email($user_email, $user_name, $subject, $message, $ticket_code, $reply_token, (bool)$is_broadcast);

            return [
                'thread_id'   => $thread_id,
                'ticket_code' => $ticket_code,
                'reply_token' => $reply_token,
                'mail_sent'   => $mail_ok,
                'mail_error'  => $GLOBALS['last_mail_error'] ?? ''
            ];
        }

        return false;
    }

    /**
     * Post a response to an existing thread
     */
    function post_thread_reply($link, $thread_id, $sender_type, $sender_name, $sender_email, $message) {
        if (!$link) return false;

        $t_id   = (int)$thread_id;
        $s_type = ($sender_type === 'user') ? 'user' : 'admin';
        $s_name = mysqli_real_escape_string($link, trim($sender_name));
        $s_mail = mysqli_real_escape_string($link, trim($sender_email));
        $msg    = mysqli_real_escape_string($link, trim($message));

        $sql_msg = "INSERT INTO `support_messages` 
            (`thread_id`, `sender_type`, `sender_name`, `sender_email`, `message`, `is_read`, `created_at`) 
            VALUES ($t_id, '$s_type', '$s_name', '$s_mail', '$msg', " . ($s_type === 'user' ? 0 : 1) . ", NOW())";

        if (mysqli_query($link, $sql_msg)) {
            $new_status = ($s_type === 'user') ? 'user_replied' : 'admin_replied';
            mysqli_query($link, "UPDATE `support_threads` SET `status` = '$new_status', `updated_at` = NOW() WHERE `id` = $t_id");

            // If Admin replied, dispatch email to user
            if ($s_type === 'admin') {
                $q = mysqli_query($link, "SELECT * FROM `support_threads` WHERE `id` = $t_id LIMIT 1");
                if ($q && $t_row = mysqli_fetch_assoc($q)) {
                    send_platform_email(
                        $t_row['user_email'],
                        $t_row['user_name'],
                        "Re: " . $t_row['subject'],
                        $message,
                        $t_row['ticket_code'],
                        $t_row['reply_token'],
                        (bool)$t_row['is_broadcast']
                    );
                }
            }

            return true;
        }

        return false;
    }
}
