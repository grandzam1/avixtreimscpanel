<?php 
$page = "Shares Orders";
include_once __DIR__ . '/header.php';

$msg = "";
$err_msg = "";

// Ensure table exists
ensure_table_exists($link, 'shares_orders', "
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_code` VARCHAR(50) NOT NULL UNIQUE,
    `user_id` INT NOT NULL,
    `username` VARCHAR(191) NOT NULL,
    `user_email` VARCHAR(191) NOT NULL,
    `symbol` VARCHAR(30) NOT NULL,
    `asset_name` VARCHAR(100) NOT NULL,
    `shares_qty` DECIMAL(18, 4) NOT NULL,
    `purchase_price` DECIMAL(18, 2) NOT NULL,
    `total_amount` DECIMAL(18, 2) NOT NULL,
    `current_price` DECIMAL(18, 2) NOT NULL,
    `current_value` DECIMAL(18, 2) NOT NULL,
    `profit_loss` DECIMAL(18, 2) DEFAULT 0.00,
    `profit_loss_pct` DECIMAL(10, 2) DEFAULT 0.00,
    `status` VARCHAR(30) DEFAULT 'Pending',
    `payment_status` VARCHAR(30) DEFAULT 'Pending Payment',
    `deposit_id` INT DEFAULT 0,
    `admin_note` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY `idx_user_email` (`user_email`),
    KEY `idx_symbol` (`symbol`),
    KEY `idx_status` (`status`)
");

// 1. Approve Shares Order
if (isset($_POST['approve_order'])) {
    $order_id = intval($_POST['order_id']);
    $q_ord = mysqli_query($link, "SELECT * FROM shares_orders WHERE id = '$order_id' LIMIT 1");
    if ($q_ord && $o_data = mysqli_fetch_assoc($q_ord)) {
        $upd = mysqli_query($link, "UPDATE shares_orders SET status = 'Approved', payment_status = 'Verified & Active' WHERE id = '$order_id'");
        if ($upd) {
            // Also approve associated deposit if present
            if (!empty($o_data['deposit_id']) && intval($o_data['deposit_id']) > 0) {
                $depo_id = intval($o_data['deposit_id']);
                mysqli_query($link, "UPDATE deposits SET status = 'COMPLETED' WHERE id = '$depo_id'");
            }
            $msg = "Shares Order <strong>#" . htmlspecialchars($o_data['order_code']) . "</strong> for " . htmlspecialchars($o_data['user_email']) . " has been successfully APPROVED!";
        } else {
            $err_msg = "Error approving order: " . mysqli_error($link);
        }
    }
}

// 2. Reject Shares Order
if (isset($_POST['reject_order'])) {
    $order_id = intval($_POST['order_id']);
    $upd = mysqli_query($link, "UPDATE shares_orders SET status = 'Rejected', payment_status = 'Rejected' WHERE id = '$order_id'");
    if ($upd) {
        $msg = "Shares Order #$order_id has been REJECTED.";
    } else {
        $err_msg = "Error rejecting order: " . mysqli_error($link);
    }
}

// 3. Control / Adjust Profit or Loss
if (isset($_POST['save_pnl_adjustment'])) {
    $order_id    = intval($_POST['order_id']);
    $pnl_mode    = trim($_POST['pnl_mode'] ?? 'profit'); // profit or loss
    $adj_amount  = floatval($_POST['adj_amount'] ?? 0);
    $admin_notes = mysqli_real_escape_string($link, trim($_POST['admin_notes'] ?? ''));

    $q_ord = mysqli_query($link, "SELECT * FROM shares_orders WHERE id = '$order_id' LIMIT 1");
    if ($q_ord && $o_data = mysqli_fetch_assoc($q_ord)) {
        $principal = floatval($o_data['total_amount']);
        if ($principal <= 0) $principal = 1;

        if ($pnl_mode === 'loss') {
            $pnl_val = -1 * abs($adj_amount);
        } else {
            $pnl_val = abs($adj_amount);
        }

        $pnl_pct = round(($pnl_val / $principal) * 100, 2);
        $new_value = round($principal + $pnl_val, 2);
        if ($new_value < 0) $new_value = 0;

        $upd_pnl = mysqli_query($link, "UPDATE shares_orders SET 
            profit_loss = '$pnl_val', 
            profit_loss_pct = '$pnl_pct', 
            current_value = '$new_value',
            admin_note = '$admin_notes'
            WHERE id = '$order_id'");

        if ($upd_pnl) {
            $formatted_pnl = ($pnl_val >= 0 ? '+$' : '-$') . number_format(abs($pnl_val), 2);
            $msg = "Profit/Loss for Order <strong>#" . htmlspecialchars($o_data['order_code']) . "</strong> updated to <strong>$formatted_pnl ($pnl_pct%)</strong>! Updated valuation: $" . number_format($new_value, 2);
        } else {
            $err_msg = "Error updating Profit/Loss: " . mysqli_error($link);
        }
    }
}

// 4. Delete Shares Order
if (isset($_POST['delete_order'])) {
    $order_id = intval($_POST['order_id']);
    mysqli_query($link, "DELETE FROM shares_orders WHERE id = '$order_id'");
    $msg = "Shares Order record #$order_id has been deleted.";
}

// Metrics
$cnt_total = 0;
$cnt_pending = 0;
$cnt_approved = 0;
$total_volume = 0;

$q_m = mysqli_query($link, "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) as approved,
    SUM(total_amount) as volume
    FROM shares_orders");
if ($q_m && $m_row = mysqli_fetch_assoc($q_m)) {
    $cnt_total    = (int)($m_row['total'] ?? 0);
    $cnt_pending  = (int)($m_row['pending'] ?? 0);
    $cnt_approved = (int)($m_row['approved'] ?? 0);
    $total_volume = floatval($m_row['volume'] ?? 0);
}

// Fetch all orders
$orders = [];
$filter_sql = "";
if (isset($_GET['status']) && in_array($_GET['status'], ['Pending', 'Approved', 'Rejected'])) {
    $st_f = mysqli_real_escape_string($link, $_GET['status']);
    $filter_sql = "WHERE status = '$st_f'";
}

$q_all = mysqli_query($link, "SELECT * FROM shares_orders $filter_sql ORDER BY id DESC");
if ($q_all) {
    while ($r = mysqli_fetch_assoc($q_all)) {
        $orders[] = $r;
    }
}
?>

<div class="row">
    <!-- Top Header & Actions -->
    <div class="col-12 mb-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="mb-1"><i class="mdi mdi-trending-up text-primary me-2"></i> Stock Shares Orders & Profit/Loss Control</h4>
                <p class="text-muted mb-0">Approve or reject customer equity purchases (Technology, Silicon, Aerospace Indices) and directly control their profit or loss.</p>
            </div>
            <div>
                <a href="shares.php" class="btn btn-outline-secondary btn-sm me-1 <?php echo empty($_GET['status']) ? 'active' : ''; ?>">All Orders (<?php echo $cnt_total; ?>)</a>
                <a href="shares.php?status=Pending" class="btn btn-outline-warning btn-sm me-1 <?php echo ($_GET['status'] ?? '') == 'Pending' ? 'active' : ''; ?>">Pending (<?php echo $cnt_pending; ?>)</a>
                <a href="shares.php?status=Approved" class="btn btn-outline-success btn-sm <?php echo ($_GET['status'] ?? '') == 'Approved' ? 'active' : ''; ?>">Approved (<?php echo $cnt_approved; ?>)</a>
            </div>
        </div>
    </div>

    <!-- Feedback Alerts -->
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

    <!-- Summary Metrics Cards -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Shares Orders</span>
                <h3 class="mb-1"><span><?php echo number_format($cnt_total); ?></span></h3>
                <div class="text-nowrap"><span class="badge bg-soft-info text-info">All Time</span></div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <span class="text-muted mb-3 lh-1 d-block text-truncate">Pending Approval</span>
                <h3 class="mb-1 text-warning"><span><?php echo number_format($cnt_pending); ?></span></h3>
                <div class="text-nowrap"><span class="badge bg-soft-warning text-warning">Awaiting Admin Verification</span></div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <span class="text-muted mb-3 lh-1 d-block text-truncate">Approved Holdings</span>
                <h3 class="mb-1 text-success"><span><?php echo number_format($cnt_approved); ?></span></h3>
                <div class="text-nowrap"><span class="badge bg-soft-success text-success">Active Portfolios</span></div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Shares Volume</span>
                <h3 class="mb-1 text-primary"><span>$<?php echo number_format($total_volume, 2); ?></span></h3>
                <div class="text-nowrap"><span class="badge bg-soft-primary text-primary">USD Cumulative</span></div>
            </div>
        </div>
    </div>

    <!-- Main Orders & Profit/Loss Management Table -->
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-transparent border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Shares Orders Registry & Profit/Loss Controls</h5>
                    <span class="text-muted font-size-12">Click "Adjust PnL" to increase profit or apply loss to any order</span>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($orders)) { ?>
                    <div class="text-center py-5 text-muted">
                        <i class="mdi mdi-package-variant-closed display-4 mb-3 d-block text-secondary"></i>
                        <h5>No Shares Orders Found</h5>
                        <p class="mb-0">When clients purchase institutional shares in their dashboard, orders appear here for approval and PnL management.</p>
                    </div>
                <?php } else { ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Order Code</th>
                                    <th>Investor Email</th>
                                    <th>Asset / Equity</th>
                                    <th class="text-end">Shares Qty</th>
                                    <th class="text-end">Purchase Price</th>
                                    <th class="text-end">Total Invested</th>
                                    <th class="text-end">Current Value</th>
                                    <th class="text-end">Profit / Loss</th>
                                    <th class="text-center">Order Status</th>
                                    <th class="text-center" style="min-width: 220px;">Admin Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $ord) { 
                                    $pnl = floatval($ord['profit_loss']);
                                    $is_pos = $pnl >= 0;
                                    $pnl_color = $is_pos ? 'text-success' : 'text-danger';
                                    $st = $ord['status'];
                                    $st_badge = 'bg-warning text-dark';
                                    if ($st === 'Approved') $st_badge = 'bg-success';
                                    if ($st === 'Rejected') $st_badge = 'bg-danger';
                                ?>
                                <tr>
                                    <td>
                                        <strong class="font-monospace text-primary">#<?php echo htmlspecialchars($ord['order_code']); ?></strong>
                                        <div class="font-size-11 text-muted"><?php echo date('M d, Y H:i', strtotime($ord['created_at'])); ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?php echo htmlspecialchars($ord['user_email']); ?></div>
                                        <div class="font-size-11 text-muted">User: <?php echo htmlspecialchars($ord['username']); ?></div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <?php if ($ord['symbol'] == 'TSLA') { ?>
                                                <span class="badge bg-danger">TSLA</span>
                                            <?php } elseif ($ord['symbol'] == 'AAPL') { ?>
                                                <span class="badge bg-dark">AAPL</span>
                                            <?php } else { ?>
                                                <span class="badge bg-info">SPACEX</span>
                                            <?php } ?>
                                            <strong><?php echo htmlspecialchars($ord['asset_name']); ?></strong>
                                        </div>
                                    </td>
                                    <td class="text-end fw-bold">
                                        <?php echo number_format($ord['shares_qty'], 2); ?>
                                    </td>
                                    <td class="text-end text-muted">
                                        $<?php echo number_format($ord['purchase_price'], 2); ?>
                                    </td>
                                    <td class="text-end fw-bold">
                                        $<?php echo number_format($ord['total_amount'], 2); ?>
                                    </td>
                                    <td class="text-end fw-bold font-size-15 text-primary">
                                        $<?php echo number_format($ord['current_value'], 2); ?>
                                    </td>
                                    <td class="text-end <?php echo $pnl_color; ?> fw-bold">
                                        <div><?php echo ($is_pos ? '▲ +$' : '▼ -$') . number_format(abs($pnl), 2); ?></div>
                                        <small class="font-size-11 opacity-75">(<?php echo ($is_pos ? '+' : '') . number_format($ord['profit_loss_pct'], 2); ?>%)</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge <?php echo $st_badge; ?> px-2 py-1 font-size-12">
                                            <?php echo htmlspecialchars($ord['status']); ?>
                                        </span>
                                        <div class="font-size-11 text-muted mt-1"><?php echo htmlspecialchars($ord['payment_status']); ?></div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1 flex-wrap">
                                            <!-- Approve Button -->
                                            <?php if ($ord['status'] !== 'Approved') { ?>
                                            <form method="post" action="shares.php" onsubmit="return confirm('Approve shares purchase order #<?php echo $ord['order_code']; ?>?');" style="display:inline;">
                                                <input type="hidden" name="order_id" value="<?php echo $ord['id']; ?>">
                                                <button type="submit" name="approve_order" class="btn btn-success btn-sm" title="Approve Shares Order">
                                                    <i class="mdi mdi-check"></i> Approve
                                                </button>
                                            </form>
                                            <?php } ?>

                                            <!-- Reject Button -->
                                            <?php if ($ord['status'] === 'Pending') { ?>
                                            <form method="post" action="shares.php" onsubmit="return confirm('Reject shares purchase order #<?php echo $ord['order_code']; ?>?');" style="display:inline;">
                                                <input type="hidden" name="order_id" value="<?php echo $ord['id']; ?>">
                                                <button type="submit" name="reject_order" class="btn btn-warning btn-sm text-dark" title="Reject Shares Order">
                                                    <i class="mdi mdi-close"></i> Reject
                                                </button>
                                            </form>
                                            <?php } ?>

                                            <!-- Control Profit / Loss Modal Trigger -->
                                            <button type="button" class="btn btn-primary btn-sm" onclick="openPnLModal(<?php echo htmlspecialchars(json_encode($ord)); ?>)" title="Control Profit or Loss for this Order">
                                                <i class="mdi mdi-chart-bell-curve-cumulative"></i> Adjust PnL
                                            </button>

                                            <!-- Delete Button -->
                                            <form method="post" action="shares.php" onsubmit="return confirm('Delete order #<?php echo $ord['order_code']; ?>?');" style="display:inline;">
                                                <input type="hidden" name="order_id" value="<?php echo $ord['id']; ?>">
                                                <button type="submit" name="delete_order" class="btn btn-outline-danger btn-sm" title="Delete">
                                                    <i class="mdi mdi-trash-can-outline"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal to Control Profit or Loss -->
<div class="modal fade" id="pnlModal" tabindex="-1" aria-labelledby="pnlModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="pnlModalLabel"><i class="mdi mdi-chart-line-stacked me-1"></i> Control Profit or Loss for Shares</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="shares.php">
                <div class="modal-body p-4">
                    <input type="hidden" name="order_id" id="modalPnlOrderId">

                    <div class="p-3 bg-light rounded mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Order Code:</span>
                            <strong id="modalPnlOrderCode"></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Investor:</span>
                            <span id="modalPnlUser"></span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Stock / Asset:</span>
                            <span id="modalPnlAsset" class="fw-bold"></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Total Principal Invested:</span>
                            <strong id="modalPnlPrincipal" class="text-primary font-size-15"></strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Direction of Adjustment</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="pnl_mode" id="modeProfit" value="profit" checked onchange="updatePnLPreview()">
                            <label class="btn btn-outline-success fw-bold" for="modeProfit"><i class="mdi mdi-trending-up me-1"></i> Add Profit (+)</label>

                            <input type="radio" class="btn-check" name="pnl_mode" id="modeLoss" value="loss" onchange="updatePnLPreview()">
                            <label class="btn btn-outline-danger fw-bold" for="modeLoss"><i class="mdi mdi-trending-down me-1"></i> Set Loss (-)</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold" for="adjAmount">Profit / Loss Dollar Amount ($ USD)</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0" class="form-control form-control-lg fw-bold" name="adj_amount" id="adjAmount" placeholder="e.g. 500.00" oninput="updatePnLPreview()" required>
                        </div>
                        <small class="text-muted">Enter the dollar return to credit or deduct from this shares holding.</small>
                    </div>

                    <!-- Quick Preset Buttons -->
                    <div class="mb-3">
                        <label class="form-label font-size-12 text-muted mb-1">Quick Percentage Shortcuts:</label>
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" onclick="setPresetPct(5)">5%</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" onclick="setPresetPct(10)">10%</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" onclick="setPresetPct(20)">20%</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" onclick="setPresetPct(50)">50%</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" onclick="setPresetPct(100)">100%</button>
                        </div>
                    </div>

                    <!-- Computed Real-Time Outcome Box -->
                    <div class="p-3 border rounded bg-white mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Adjusted Return (PnL):</span>
                            <strong id="previewPnlTag" class="text-success font-size-14">+$0.00</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>New User Portfolio Value:</span>
                            <strong id="previewNewVal" class="font-size-16 text-primary">$0.00</strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="adminNotes">Admin Internal Note (Optional)</label>
                        <input type="text" class="form-control" name="admin_notes" id="adminNotes" placeholder="e.g. Q3 technology yield adjustment">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="save_pnl_adjustment" class="btn btn-primary px-4">
                        <i class="mdi mdi-check-circle me-1"></i> Apply Profit / Loss Adjustment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentModalPrincipal = 0;

function openPnLModal(ord) {
    document.getElementById('modalPnlOrderId').value = ord.id;
    document.getElementById('modalPnlOrderCode').innerText = '#' + ord.order_code;
    document.getElementById('modalPnlUser').innerText = ord.user_email;
    document.getElementById('modalPnlAsset').innerText = ord.asset_name + ' (' + ord.symbol + ') — ' + parseFloat(ord.shares_qty).toFixed(2) + ' shares';
    
    currentModalPrincipal = parseFloat(ord.total_amount) || 0;
    document.getElementById('modalPnlPrincipal').innerText = '$' + currentModalPrincipal.toLocaleString(undefined, {minimumFractionDigits: 2});

    const currentPnl = parseFloat(ord.profit_loss) || 0;
    if (currentPnl < 0) {
        document.getElementById('modeLoss').checked = true;
        document.getElementById('adjAmount').value = Math.abs(currentPnl).toFixed(2);
    } else {
        document.getElementById('modeProfit').checked = true;
        document.getElementById('adjAmount').value = currentPnl > 0 ? currentPnl.toFixed(2) : '';
    }

    document.getElementById('adminNotes').value = ord.admin_note || '';

    updatePnLPreview();

    const modalEl = document.getElementById('pnlModal');
    const bsModal = new bootstrap.Modal(modalEl);
    bsModal.show();
}

function setPresetPct(pct) {
    if (currentModalPrincipal > 0) {
        const amt = (currentModalPrincipal * (pct / 100)).toFixed(2);
        document.getElementById('adjAmount').value = amt;
        updatePnLPreview();
    }
}

function updatePnLPreview() {
    const isLoss = document.getElementById('modeLoss').checked;
    const amt = parseFloat(document.getElementById('adjAmount').value) || 0;
    const pnl = isLoss ? (-1 * amt) : amt;
    const pct = currentModalPrincipal > 0 ? ((pnl / currentModalPrincipal) * 100).toFixed(2) : '0.00';
    const newVal = Math.max(0, currentModalPrincipal + pnl);

    const tag = document.getElementById('previewPnlTag');
    if (isLoss) {
        tag.className = 'text-danger font-size-14';
        tag.innerText = '-$' + amt.toLocaleString(undefined, {minimumFractionDigits: 2}) + ' (' + pct + '%)';
    } else {
        tag.className = 'text-success font-size-14';
        tag.innerText = '+$' + amt.toLocaleString(undefined, {minimumFractionDigits: 2}) + ' (+' + pct + '%)';
    }

    document.getElementById('previewNewVal').innerText = '$' + newVal.toLocaleString(undefined, {minimumFractionDigits: 2});
}
</script>

<?php 
include_once __DIR__ . '/footer.php';
?>
