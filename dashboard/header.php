<?php 
include_once __DIR__ . '/session.php'; 

// Time-based greeting
$hour = date('H');
if ($hour < 12) {
    $greeting = "Good morning!";
} elseif ($hour < 18) {
    $greeting = "Good afternoon!";
} else {
    $greeting = "Good evening!";
}

// Balance calculations
$total_balance = (float)$mbalance + (float)$dbalance;
$display_name = !empty($fullname) ? $fullname : (!empty($username) ? $username : 'Investor');
$current_page = basename($_SERVER['PHP_SELF']);

// Load User In-App Notifications
$user_unread_notif_count = function_exists('get_user_unread_notif_count') ? get_user_unread_notif_count($link, $uemail) : 0;
$user_recent_notifications = function_exists('get_user_notifications') ? get_user_notifications($link, $uemail, 5) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title><?php echo htmlspecialchars($sitename); ?> - Member Dashboard</title>
    <link rel="shortcut icon" href="../images/bitcoin31.png">
    
    <!-- Progressive Web App (PWA) & Native Mobile App Meta -->
    <link rel="manifest" href="../manifest.json">
    <meta name="theme-color" content="#090e0b">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="<?php echo htmlspecialchars($sitename); ?>">
    <link rel="apple-touch-icon" href="../images/icons/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="180x180" href="../images/icons/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="192x192" href="../images/icons/icon-192.png">
    <link rel="apple-touch-icon" sizes="512x512" href="../images/icons/icon-512.png">
    <link rel="stylesheet" href="../pwa.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons & Scripts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="style/jquery.js"></script>

    <!-- Chart.js for real live dynamic charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- TradingView Widget Script from original script -->
    <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>

    <!-- Custom Fintech Theme -->
    <link rel="stylesheet" href="style/fintech-theme.css">

    <!-- Dynamic Theme Styling from Admin Settings -->
    <style>
        :root {
            --primary-accent: <?php echo !empty($theme_color) ? htmlspecialchars($theme_color) : '#ccff00'; ?>;
            --primary-accent-glow: <?php echo !empty($theme_color) ? htmlspecialchars($theme_color) . '40' : 'rgba(204, 255, 0, 0.25)'; ?>;
            --bg-dark: <?php echo !empty($bg_color) ? htmlspecialchars($bg_color) : '#0a0e0c'; ?>;
        }

        /* Responsive Dashboard Styling (Not a screenshot mockup) */
        .dashboard-container {
            width: 100%;
        }

        .screen-card {
            max-width: 100% !important;
            border-radius: 26px !important;
            padding: 26px 28px !important;
            background: #141c17 !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5) !important;
        }

        .chart-canvas-wrap {
            position: relative;
            width: 100%;
            height: 240px;
            margin-bottom: 16px;
        }
    </style>
</head>
<body class="fintech-body">

<div class="app-container">
    <!-- Desktop Sidebar carrying ALL original functions & pages -->
    <aside class="app-sidebar">
        <a href="customer.php" class="sidebar-brand">
            <?php 
            $dash_logo = "../images/logo.png";
            if (!empty($logo)) {
                if (strpos($logo, 'http') === 0) {
                    $dash_logo = $logo;
                } elseif (file_exists(__DIR__ . '/../' . $logo)) {
                    $dash_logo = "../" . $logo;
                } elseif (file_exists(__DIR__ . '/' . $logo)) {
                    $dash_logo = $logo;
                }
            }
            if (!empty($dash_logo)) { ?>
                <img src="<?php echo htmlspecialchars($dash_logo); ?>" alt="<?php echo htmlspecialchars($sitename); ?>" style="max-height: 38px; width: auto; object-fit: contain;">
            <?php } else { ?>
                <span class="brand-dot"></span>
                <span class="brand-title"><?php echo htmlspecialchars($sitename); ?></span>
            <?php } ?>
        </a>

        <ul class="sidebar-nav">
            <li>
                <a href="customer.php" class="sidebar-link <?php echo $current_page == 'customer.php' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-chart-pie"></i> Overview
                </a>
            </li>
            <li>
                <a href="plans.php" class="sidebar-link <?php echo $current_page == 'plans.php' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-layer-group"></i> Investment Plans
                </a>
            </li>
            <li>
                <a href="shares.php" class="sidebar-link <?php echo $current_page == 'shares.php' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-arrow-trend-up" style="color:var(--primary-accent);"></i> Buy Stock Shares
                </a>
            </li>
            <li>
                <a href="deposit.php" class="sidebar-link <?php echo $current_page == 'deposit.php' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-circle-down"></i> Deposit
                </a>
            </li>
            <li>
                <a href="deposit_history.php" class="sidebar-link <?php echo $current_page == 'deposit_history.php' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-clock-rotate-left"></i> Deposit History
                </a>
            </li>
            <li>
                <a href="withdraw.php" class="sidebar-link <?php echo $current_page == 'withdraw.php' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-wallet"></i> Withdraw Funds
                </a>
            </li>
            <li>
                <a href="referals.php" class="sidebar-link <?php echo $current_page == 'referals.php' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-users"></i> Referrals
                </a>
            </li>
            <li>
                <a href="messages.php" class="sidebar-link <?php echo $current_page == 'messages.php' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-envelope-open-text"></i> Support &amp; Mail
                </a>
            </li>
            <li>
                <a href="notifications.php" class="sidebar-link <?php echo $current_page == 'notifications.php' ? 'active' : ''; ?>">
                    <i class="fa-regular fa-bell"></i> Notifications
                    <?php if ($user_unread_notif_count > 0) { ?>
                        <span style="font-size:0.65rem; background:rgba(204,255,0,0.2); color:var(--primary-accent); padding:2px 7px; border-radius:6px; font-weight:800; margin-left:auto;"><?php echo $user_unread_notif_count; ?></span>
                    <?php } ?>
                </a>
            </li>
            <li>
                <a href="kyc.php" class="sidebar-link <?php echo $current_page == 'kyc.php' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-id-card"></i> KYC Verification
                    <?php if (isset($verification) && $verification == '1') { ?>
                        <span style="font-size:0.65rem; background:rgba(46,204,113,0.2); color:#2ecc71; padding:2px 7px; border-radius:6px; font-weight:800; margin-left:auto;">VERIFIED</span>
                    <?php } else { ?>
                        <span style="font-size:0.65rem; background:rgba(241,196,15,0.2); color:#f1c40f; padding:2px 7px; border-radius:6px; font-weight:800; margin-left:auto;">VERIFY</span>
                    <?php } ?>
                </a>
            </li>
            <li>
                <a href="settings.php" class="sidebar-link <?php echo $current_page == 'settings.php' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-user-gear"></i> Profile &amp; Settings
                </a>
            </li>
            <li>
                <a href="navigation.php" class="sidebar-link <?php echo $current_page == 'navigation.php' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-bars-staggered"></i> All Navigation
                </a>
            </li>
            <li>
                <a href="../index.php" class="sidebar-link" target="_blank">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Visit Website
                </a>
            </li>
            <li>
                <a href="logout.php" class="sidebar-link" style="color: #ff5252;">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </a>
            </li>
        </ul>

        <?php 
        $avatar_img_src = "";
        if (!empty($user_image)) {
            if (strpos($user_image, 'http') === 0) {
                $avatar_img_src = $user_image;
            } elseif (file_exists(__DIR__ . '/../' . $user_image)) {
                $avatar_img_src = '../' . $user_image;
            } elseif (file_exists(__DIR__ . '/' . $user_image)) {
                $avatar_img_src = $user_image;
            }
        }
        ?>
        <a href="settings.php" class="sidebar-user" style="text-decoration:none; color:inherit;" title="Manage Profile & Display Picture">
            <?php if (!empty($avatar_img_src)) { ?>
                <img src="<?php echo htmlspecialchars($avatar_img_src); ?>" alt="<?php echo htmlspecialchars($display_name); ?>" class="user-avatar-sm" style="object-fit:cover; border-radius:50%; border:2px solid var(--primary-accent);">
            <?php } else { ?>
                <div class="user-avatar-sm">
                    <?php echo strtoupper(substr($display_name, 0, 1)); ?>
                </div>
            <?php } ?>
            <div style="line-height: 1.2; overflow: hidden;">
                <div style="font-size: 0.9rem; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo htmlspecialchars($display_name); ?></div>
                <div style="font-size: 0.75rem; color: var(--text-muted);">$<?php echo number_format($total_balance, 2); ?></div>
            </div>
        </a>
    </aside>

    <!-- Main Content Shell -->
    <main class="app-main">
        <!-- Mobile Top Navigation Header -->
        <div class="mobile-top-bar">
            <a href="customer.php" style="display:flex; align-items:center; gap:8px; text-decoration:none; color:#fff;">
                <?php if (!empty($dash_logo)) { ?>
                    <img src="<?php echo htmlspecialchars($dash_logo); ?>" alt="<?php echo htmlspecialchars($sitename); ?>" style="max-height: 32px; width: auto; object-fit: contain;">
                <?php } else { ?>
                    <span class="brand-dot"></span>
                    <span style="font-weight:800; font-size:1.15rem;"><?php echo htmlspecialchars($sitename); ?></span>
                <?php } ?>
            </a>
            <div style="display:flex; gap:10px; align-items:center;">
                <a href="settings.php" class="icon-btn-round" title="Profile Settings" style="overflow:hidden; padding:0; display:flex; align-items:center; justify-content:center;">
                    <?php if (!empty($avatar_img_src)) { ?>
                        <img src="<?php echo htmlspecialchars($avatar_img_src); ?>" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
                    <?php } else { ?>
                        <i class="fa-solid fa-user"></i>
                    <?php } ?>
                </a>

                <!-- Notification Bell with Hover Popover -->
                <div class="notification-dropdown-wrapper">
                    <a href="notifications.php" class="icon-btn-round notification-bell-btn" title="Notifications">
                        <i class="fa-regular fa-bell"></i>
                        <?php if ($user_unread_notif_count > 0) { ?>
                            <span class="notif-badge-pill"><?php echo $user_unread_notif_count > 99 ? '99+' : $user_unread_notif_count; ?></span>
                        <?php } ?>
                    </a>
                    <div class="notification-hover-menu">
                        <div class="notif-menu-header">
                            <h5><i class="fa-solid fa-bell" style="color:var(--primary-accent);"></i> Notifications</h5>
                            <?php if ($user_unread_notif_count > 0) { ?>
                                <a href="notifications.php?mark_all_read=1" class="notif-mark-read-link">Mark all read</a>
                            <?php } ?>
                        </div>
                        <div class="notif-menu-body">
                            <?php if (!empty($user_recent_notifications)) { 
                                foreach ($user_recent_notifications as $n_item) { 
                                    $n_is_read = $n_item['is_read'];
                                    $n_ico = 'fa-solid fa-bell';
                                    $n_color = 'var(--primary-accent)';
                                    if ($n_item['type'] === 'success') { $n_ico = 'fa-solid fa-circle-check'; $n_color = '#2ecc71'; }
                                    elseif ($n_item['type'] === 'warning') { $n_ico = 'fa-solid fa-triangle-exclamation'; $n_color = '#f1c40f'; }
                                    elseif ($n_item['type'] === 'danger') { $n_ico = 'fa-solid fa-circle-exclamation'; $n_color = '#e74c3c'; }
                                    elseif ($n_item['type'] === 'market') { $n_ico = 'fa-solid fa-chart-line'; $n_color = '#00e5ff'; }
                            ?>
                                <a href="notifications.php?view=<?php echo $n_item['id']; ?>" class="notif-menu-item <?php echo $n_is_read ? 'read' : 'unread'; ?>">
                                    <div class="notif-item-icon" style="color: <?php echo $n_color; ?>; background: <?php echo $n_color . '18'; ?>;">
                                        <i class="<?php echo $n_ico; ?>"></i>
                                    </div>
                                    <div class="notif-item-content">
                                        <div class="notif-item-title"><?php echo htmlspecialchars($n_item['title']); ?></div>
                                        <div class="notif-item-text"><?php echo htmlspecialchars(mb_strimwidth($n_item['message'], 0, 70, '...')); ?></div>
                                        <div class="notif-item-time"><?php echo time_ago_short($n_item['created_at']); ?></div>
                                    </div>
                                    <?php if (!$n_is_read) { ?>
                                        <span class="notif-unread-dot"></span>
                                    <?php } ?>
                                </a>
                            <?php } } else { ?>
                                <div style="padding: 24px 16px; text-align: center; color: var(--text-muted); font-size: 0.82rem;">
                                    <i class="fa-regular fa-bell-slash" style="font-size: 1.6rem; margin-bottom: 6px; display: block; opacity: 0.4;"></i>
                                    No notifications yet
                                </div>
                            <?php } ?>
                        </div>
                        <div class="notif-menu-footer">
                            <a href="notifications.php" class="notif-view-all-btn">
                                View All Notifications <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <a href="navigation.php" class="icon-btn-round" title="Menu"><i class="fa-solid fa-bars"></i></a>
                <a href="deposit.php" class="icon-btn-round" title="Deposit"><i class="fa-solid fa-plus"></i></a>
                <a href="logout.php" class="icon-btn-round" title="Logout"><i class="fa-solid fa-power-off"></i></a>
            </div>
        </div>
