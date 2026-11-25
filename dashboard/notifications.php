<?php 
include_once __DIR__ . '/header.php';

$msg = "";

// Handle Mark All as Read
if (isset($_GET['mark_all_read'])) {
    $ins = mysqli_query($link, "INSERT IGNORE INTO notification_reads (notification_id, user_email) 
        SELECT id, '$uemail' FROM notifications WHERE (target_type = 'all' OR target_email = '$uemail')");
    $msg = "All notifications have been marked as read.";
}

// Handle Mark Single as Read
$view_id = isset($_GET['view']) ? intval($_GET['view']) : 0;
if ($view_id > 0) {
    mysqli_query($link, "INSERT IGNORE INTO notification_reads (notification_id, user_email) VALUES ('$view_id', '$uemail')");
}

// Fetch all notifications for this user
$filter = isset($_GET['filter']) ? trim($_GET['filter']) : 'all';
$all_notifications = get_user_notifications($link, $uemail, 100);

// Filter list
$filtered_notifications = [];
foreach ($all_notifications as $notif) {
    if ($filter === 'unread' && $notif['is_read']) continue;
    if ($filter === 'market' && $notif['type'] !== 'market') continue;
    if ($filter === 'system' && !in_array($notif['type'], ['info', 'warning', 'danger'])) continue;
    $filtered_notifications[] = $notif;
}

$unread_count = get_user_unread_notif_count($link, $uemail);
?>

<div style="max-width: 900px; margin: 0 auto;">

    <!-- Page Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 14px;">
        <div>
            <h2 style="font-weight: 800; font-size: 1.8rem; color: #fff; margin-bottom: 4px; display: flex; align-items: center; gap: 10px;">
                <i class="fa-regular fa-bell" style="color: var(--primary-accent);"></i> Notification Center
                <?php if ($unread_count > 0) { ?>
                    <span style="font-size: 0.78rem; background: rgba(204,255,0,0.18); color: var(--primary-accent); padding: 3px 10px; border-radius: 12px; font-weight: 800;"><?php echo $unread_count; ?> Unread</span>
                <?php } ?>
            </h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0;">Institutional service bulletins, trading execution notices, and account alerts.</p>
        </div>

        <div style="display: flex; gap: 10px; align-items: center;">
            <?php if ($unread_count > 0) { ?>
                <a href="notifications.php?mark_all_read=1" class="btn-pill-ghost" style="text-decoration: none; padding: 10px 18px; font-size: 0.85rem; border-color: rgba(204,255,0,0.3); color: var(--primary-accent);">
                    <i class="fa-solid fa-check-double me-1"></i> Mark All Read
                </a>
            <?php } ?>
            <a href="customer.php" class="icon-btn-round" title="Back to Dashboard">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
        </div>
    </div>

    <?php if (!empty($msg)) { ?>
        <div style="background: rgba(46, 204, 113, 0.15); border: 1px solid rgba(46, 204, 113, 0.4); border-radius: 16px; padding: 14px 18px; color: #2ecc71; margin-bottom: 22px; display: flex; align-items: center; gap: 10px; font-size: 0.9rem;">
            <i class="fa-solid fa-circle-check"></i>
            <span><?php echo $msg; ?></span>
        </div>
    <?php } ?>

    <!-- Filter Tabs -->
    <div style="display: flex; gap: 8px; margin-bottom: 22px; overflow-x: auto; padding-bottom: 6px;">
        <a href="notifications.php" style="padding: 8px 16px; border-radius: 20px; font-size: 0.82rem; font-weight: 700; text-decoration: none; transition: all 0.2s; <?php echo ($filter === 'all') ? 'background: var(--primary-accent); color: #000;' : 'background: #141c17; color: var(--text-muted); border: 1px solid var(--card-border);'; ?>">
            All (<?php echo count($all_notifications); ?>)
        </a>
        <a href="notifications.php?filter=unread" style="padding: 8px 16px; border-radius: 20px; font-size: 0.82rem; font-weight: 700; text-decoration: none; transition: all 0.2s; <?php echo ($filter === 'unread') ? 'background: var(--primary-accent); color: #000;' : 'background: #141c17; color: var(--text-muted); border: 1px solid var(--card-border);'; ?>">
            Unread (<?php echo $unread_count; ?>)
        </a>
        <a href="notifications.php?filter=market" style="padding: 8px 16px; border-radius: 20px; font-size: 0.82rem; font-weight: 700; text-decoration: none; transition: all 0.2s; <?php echo ($filter === 'market') ? 'background: var(--primary-accent); color: #000;' : 'background: #141c17; color: var(--text-muted); border: 1px solid var(--card-border);'; ?>">
            Market &amp; Shares
        </a>
        <a href="notifications.php?filter=system" style="padding: 8px 16px; border-radius: 20px; font-size: 0.82rem; font-weight: 700; text-decoration: none; transition: all 0.2s; <?php echo ($filter === 'system') ? 'background: var(--primary-accent); color: #000;' : 'background: #141c17; color: var(--text-muted); border: 1px solid var(--card-border);'; ?>">
            System &amp; Security
        </a>
    </div>

    <!-- Notification Cards List -->
    <div style="display: flex; flex-direction: column; gap: 14px; margin-bottom: 30px;">
        <?php if (!empty($filtered_notifications)) { 
            foreach ($filtered_notifications as $item) { 
                $is_read = (bool)$item['is_read'];
                $is_active_view = ($view_id == $item['id']);
                $type = $item['type'];

                $icon_class = 'fa-solid fa-bell';
                $theme_color = 'var(--primary-accent)';
                if ($type === 'success') {
                    $icon_class = 'fa-solid fa-circle-check';
                    $theme_color = '#2ecc71';
                } elseif ($type === 'warning') {
                    $icon_class = 'fa-solid fa-triangle-exclamation';
                    $theme_color = '#f1c40f';
                } elseif ($type === 'danger') {
                    $icon_class = 'fa-solid fa-circle-exclamation';
                    $theme_color = '#ff5252';
                } elseif ($type === 'market') {
                    $icon_class = 'fa-solid fa-arrow-trend-up';
                    $theme_color = '#00e5ff';
                }
        ?>
            <div style="background: <?php echo $is_read ? '#141c17' : 'rgba(20, 28, 23, 0.98)'; ?>; border: 1px solid <?php echo $is_active_view ? 'var(--primary-accent)' : ($is_read ? 'var(--card-border)' : 'rgba(204,255,0,0.3)'); ?>; border-radius: 22px; padding: 22px 24px; transition: all 0.2s ease; box-shadow: <?php echo $is_read ? 'none' : '0 6px 25px rgba(0,0,0,0.4), 0 0 15px rgba(204,255,0,0.06)'; ?>; position: relative;">
                
                <div style="display: flex; gap: 18px; align-items: flex-start;">
                    <!-- Type Icon Box -->
                    <div style="width: 46px; height: 46px; border-radius: 14px; background: <?php echo $theme_color . '18'; ?>; border: 1px solid <?php echo $theme_color . '35'; ?>; color: <?php echo $theme_color; ?>; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;">
                        <i class="<?php echo $icon_class; ?>"></i>
                    </div>

                    <!-- Content -->
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; flex-wrap: wrap; gap: 8px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <h4 style="font-weight: 800; font-size: 1.05rem; color: #fff; margin: 0;">
                                    <?php echo htmlspecialchars($item['title']); ?>
                                </h4>
                                <?php if (!$is_read) { ?>
                                    <span style="font-size: 0.65rem; background: rgba(204,255,0,0.18); color: var(--primary-accent); padding: 2px 7px; border-radius: 6px; font-weight: 800;">NEW</span>
                                <?php } ?>
                                <?php if ($item['target_type'] === 'all') { ?>
                                    <span style="font-size: 0.65rem; background: rgba(255,255,255,0.06); color: var(--text-muted); padding: 2px 7px; border-radius: 6px; font-weight: 700;">BROADCAST</span>
                                <?php } ?>
                            </div>
                            <span style="font-size: 0.78rem; color: var(--text-dim);">
                                <?php echo htmlspecialchars($item['created_at']); ?> &bull; <?php echo time_ago_short($item['created_at']); ?>
                            </span>
                        </div>

                        <p style="color: #cbd5e1; font-size: 0.92rem; line-height: 1.6; margin: 0 0 14px 0; white-space: pre-line;">
                            <?php echo htmlspecialchars($item['message']); ?>
                        </p>

                        <!-- Optional Actions -->
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                            <div>
                                <?php if (!empty($item['link_url'])) { 
                                    $link_dest = $item['link_url'];
                                ?>
                                    <a href="<?php echo htmlspecialchars($link_dest); ?>" class="btn-pill-buy" style="text-decoration: none; padding: 7px 16px; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 6px;">
                                        <span>View Action</span>
                                        <i class="fa-solid fa-arrow-right" style="font-size: 0.7rem;"></i>
                                    </a>
                                <?php } ?>
                            </div>

                            <div>
                                <?php if (!$is_read) { ?>
                                    <a href="notifications.php?view=<?php echo $item['id']; ?>" style="font-size: 0.78rem; color: var(--text-muted); text-decoration: none; display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 8px; background: rgba(255,255,255,0.04); transition: color 0.2s;">
                                        <i class="fa-regular fa-circle-check"></i> Mark as read
                                    </a>
                                <?php } else { ?>
                                    <span style="font-size: 0.75rem; color: var(--text-dim); display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fa-solid fa-check"></i> Read
                                    </span>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        <?php } } else { ?>
            <div style="background: #141c17; border: 1px solid var(--card-border); border-radius: 28px; padding: 50px 30px; text-align: center; color: var(--text-muted);">
                <i class="fa-regular fa-bell-slash" style="font-size: 3rem; margin-bottom: 14px; display: block; opacity: 0.35;"></i>
                <h4 style="color: #fff; font-weight: 700; margin-bottom: 6px;">No Notifications Found</h4>
                <p style="font-size: 0.88rem; max-width: 440px; margin: 0 auto;">You're completely caught up! New institutional announcements, trading execution updates, and alerts will appear here.</p>
            </div>
        <?php } ?>
    </div>

</div>

<?php 
include_once __DIR__ . '/footer.php';
?>
