<?php 
include_once __DIR__ . '/header.php';
?>

<div style="max-width: 900px; margin: 0 auto;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h2 style="font-weight: 800; font-size: 1.8rem; color: #fff; margin-bottom: 4px;">Deposit History</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0;">Comprehensive log of all completed and pending account funding transactions.</p>
        </div>
        <a href="deposit.php" class="btn-pill-buy" style="padding: 10px 20px; font-size: 0.88rem; text-decoration: none;">
            <i class="fa-solid fa-plus me-1"></i> Make Deposit
        </a>
    </div>

    <!-- Summary Metrics Card -->
    <?php 
    $sql211 = "SELECT SUM(amount) as sum_amount FROM deposits WHERE email = '$uemail' AND status = 'COMPLETED'";
    $result211 = mysqli_query($link, $sql211);
    $row11 = mysqli_fetch_assoc($result211);
    $sum_completed = !empty($row11['sum_amount']) ? $row11['sum_amount'] : "0.00";

    $sql_pend = "SELECT SUM(amount) as pend_amount FROM deposits WHERE email = '$uemail' AND status = 'PENDING'";
    $res_pend = mysqli_query($link, $sql_pend);
    $row_p = mysqli_fetch_assoc($res_pend);
    $sum_pending = !empty($row_p['pend_amount']) ? $row_p['pend_amount'] : "0.00";
    ?>
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-bottom: 24px;">
        <div style="background: #141c17; border: 1px solid var(--card-border); border-radius: 20px; padding: 18px 22px;">
            <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Total Approved Deposits</div>
            <div style="font-size: 1.8rem; font-weight: 800; color: #fff;">$<?php echo number_format((float)$sum_completed, 2); ?></div>
        </div>
        <div style="background: #141c17; border: 1px solid var(--card-border); border-radius: 20px; padding: 18px 22px;">
            <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Pending Verification</div>
            <div style="font-size: 1.8rem; font-weight: 800; color: var(--primary-accent);">$<?php echo number_format((float)$sum_pending, 2); ?></div>
        </div>
    </div>

    <!-- History Table -->
    <div style="background: #141c17; border: 1px solid var(--card-border); border-radius: 28px; padding: 24px;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--card-border); color: var(--text-muted);">
                        <th style="padding: 12px;">ID</th>
                        <th style="padding: 12px;">Date</th>
                        <th style="padding: 12px;">Amount</th>
                        <th style="padding: 12px;">Currency</th>
                        <th style="padding: 12px;">Payment Proof</th>
                        <th style="padding: 12px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $de = mysqli_query($link, "SELECT * FROM deposits WHERE email = '$uemail' ORDER BY id DESC");
                    if ($de && mysqli_num_rows($de) > 0) {
                        while ($ed = mysqli_fetch_assoc($de)) {
                            $is_comp = ($ed['status'] == "COMPLETED");
                            $is_rej = ($ed['status'] == "REJECTED");
                            $p_img = !empty($ed['proof_image']) ? $ed['proof_image'] : '';
                            $p_url = '';
                            if (!empty($p_img)) {
                                if (strpos($p_img, 'http') === 0) {
                                    $p_url = $p_img;
                                } elseif (file_exists(__DIR__ . '/../' . $p_img)) {
                                    $p_url = '../' . $p_img;
                                } elseif (file_exists(__DIR__ . '/' . $p_img)) {
                                    $p_url = $p_img;
                                }
                            }
                    ?>
                    <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.04);">
                        <td style="padding: 14px 12px; color: var(--text-dim);">#<?php echo $ed['id']; ?></td>
                        <td style="padding: 14px 12px; color: var(--text-muted);"><?php echo htmlspecialchars($ed['date']); ?></td>
                        <td style="padding: 14px 12px; font-weight: 800; color: #fff;">$<?php echo number_format((float)$ed['amount'], 2); ?></td>
                        <td style="padding: 14px 12px; color: #fff;">
                            <?php echo htmlspecialchars(!empty($ed['crypto']) ? $ed['crypto'] : 'Bitcoin'); ?>
                            <?php if (!empty($ed['txn_hash'])) { ?>
                                <div style="font-size: 0.72rem; color: var(--text-dim); margin-top: 2px;">TX: <?php echo htmlspecialchars(substr($ed['txn_hash'], 0, 14)) . '...'; ?></div>
                            <?php } ?>
                        </td>
                        <td style="padding: 14px 12px;">
                            <?php if (!empty($p_url)) { ?>
                                <a href="<?php echo htmlspecialchars($p_url); ?>" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; background: rgba(255,255,255,0.06); border: 1px solid var(--card-border); padding: 5px 10px; border-radius: 10px; text-decoration: none; color: #fff; font-size: 0.78rem; transition: background 0.2s ease;">
                                    <img src="<?php echo htmlspecialchars($p_url); ?>" style="width: 22px; height: 22px; border-radius: 4px; object-fit: cover;">
                                    <span>Receipt</span>
                                    <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 0.65rem; color: var(--text-muted);"></i>
                                </a>
                            <?php } else { ?>
                                <span style="color: var(--text-dim); font-size: 0.78rem;">—</span>
                            <?php } ?>
                        </td>
                        <td style="padding: 14px 12px;">
                            <?php if ($is_comp) { ?>
                                <span style="background: rgba(46, 204, 113, 0.15); color: var(--color-green); padding: 4px 12px; border-radius: 14px; font-size: 0.78rem; font-weight: 700;">COMPLETED</span>
                            <?php } elseif ($is_rej) { ?>
                                <span style="background: rgba(231, 76, 60, 0.15); color: #e74c3c; padding: 4px 12px; border-radius: 14px; font-size: 0.78rem; font-weight: 700;">REJECTED</span>
                            <?php } else { ?>
                                <span style="background: rgba(241, 196, 15, 0.15); color: #f1c40f; padding: 4px 12px; border-radius: 14px; font-size: 0.78rem; font-weight: 700;">PENDING</span>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                    ?>
                    <tr>
                        <td colspan="6" style="padding: 30px; text-align: center; color: var(--text-muted);">
                            No deposit transactions found.
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php 
include_once __DIR__ . '/footer.php';
?>
