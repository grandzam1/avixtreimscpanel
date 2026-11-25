<?php 
include_once __DIR__ . '/header.php';

$msg = "";
$err = "";

// Ensure shares_orders table exists
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

// Handle Stock Purchase Submission
if (isset($_POST['place_shares_order'])) {
    $symbol = strtoupper(trim($_POST['stock_symbol'] ?? 'TSLA'));
    $shares_qty = floatval($_POST['shares_qty'] ?? 1);
    $live_price = floatval($_POST['market_price'] ?? 248.60);

    $assets = [
        'TSLA'   => ['name' => 'Tech & Mobility Index (TSLA)', 'default_price' => 248.60],
        'AAPL'   => ['name' => 'Silicon & Technology Index (AAPL)', 'default_price' => 228.40],
        'SPACEX' => ['name' => 'Aerospace Syndicate Index (SPACEX)', 'default_price' => 210.00]
    ];

    if (!isset($assets[$symbol])) {
        $symbol = 'TSLA';
    }

    $asset_name = $assets[$symbol]['name'];
    if ($live_price <= 0) {
        $live_price = $assets[$symbol]['default_price'];
    }

    if ($shares_qty <= 0) {
        $err = "Please enter a valid quantity of shares (minimum 1 share).";
    } else {
        $total_cost = round($shares_qty * $live_price, 2);
        $order_code = 'SH-' . $symbol . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

        $q_ins = mysqli_query($link, "INSERT INTO shares_orders 
            (order_code, user_id, username, user_email, symbol, asset_name, shares_qty, purchase_price, total_amount, current_price, current_value, profit_loss, profit_loss_pct, status, payment_status)
            VALUES 
            ('$order_code', '$id', '$username', '$uemail', '$symbol', '$asset_name', '$shares_qty', '$live_price', '$total_cost', '$live_price', '$total_cost', 0.00, 0.00, 'Pending', 'Pending Payment')");

        if ($q_ins) {
            // Forward directly to deposit gateway to pay the calculated total
            $redir = "deposit.php?type=shares&order_code=" . urlencode($order_code) . "&symbol=" . urlencode($symbol) . "&shares=" . urlencode($shares_qty) . "&amount=" . urlencode($total_cost);
            echo "<script>window.location.href = '$redir';</script>";
            exit;
        } else {
            $err = "Database error creating shares order: " . mysqli_error($link);
        }
    }
}

// Fetch user's active shares portfolio & history
$user_orders = [];
$total_shares_invested = 0;
$total_shares_valuation = 0;
$total_shares_pnl = 0;

$q_history = mysqli_query($link, "SELECT * FROM shares_orders WHERE user_email = '$uemail' ORDER BY id DESC");
if ($q_history && mysqli_num_rows($q_history) > 0) {
    while ($row_ord = mysqli_fetch_assoc($q_history)) {
        $user_orders[] = $row_ord;
        if ($row_ord['status'] === 'Approved') {
            $total_shares_invested += floatval($row_ord['total_amount']);
            $total_shares_valuation += floatval($row_ord['current_value']);
            $total_shares_pnl += floatval($row_ord['profit_loss']);
        }
    }
}
?>

<div style="max-width: 1100px; margin: 0 auto;">

    <!-- Page Title & Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 14px;">
        <div>
            <div style="display:flex; align-items:center; gap:10px;">
                <h2 style="font-weight: 800; font-size: 1.8rem; color: #fff; margin: 0;">Institutional Stock Shares Desk</h2>
                <span style="background: rgba(204,255,0,0.12); color: var(--primary-accent); border: 1px solid rgba(204,255,0,0.3); font-size: 0.75rem; font-weight: 800; padding: 3px 10px; border-radius: 20px;">
                    <i class="fa-solid fa-bolt"></i> LIVE DESK
                </span>
            </div>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 4px;">Direct equity shares allocation in Technology, Clean Energy, and Aerospace Indices with live market quotes.</p>
        </div>
        <div style="display:flex; gap:10px;">
            <button onclick="openBuyModal('TSLA')" class="btn-deposit-gold" style="padding: 12px 24px; font-size: 0.92rem; border-radius: 16px; display:inline-flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-cart-shopping"></i> Buy Shares Now
            </button>
            <a href="customer.php" class="icon-btn-round" title="Back to Dashboard">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
        </div>
    </div>

    <!-- Error/Notice Alerts -->
    <?php if ($err != "") { ?>
    <div style="background: rgba(255, 82, 82, 0.15); border: 1px solid var(--color-red); color: var(--color-red); padding: 14px 18px; border-radius: 16px; margin-bottom: 20px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-circle-exclamation font-size-18"></i>
        <span><?php echo $err; ?></span>
    </div>
    <?php } ?>

    <!-- Summary Stats Metrics -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 18px; margin-bottom: 28px;">
        <div style="background: #141c17; border: 1px solid var(--card-border); border-radius: 22px; padding: 22px; box-shadow: 0 10px 25px rgba(0,0,0,0.3);">
            <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; text-transform: uppercase;">Active Shares Value</div>
            <div style="font-size: 1.7rem; font-weight: 800; color: #fff;">$<?php echo number_format($total_shares_valuation, 2); ?></div>
            <div style="font-size: 0.78rem; color: #64748b; margin-top: 4px;">Approved & holding portfolios</div>
        </div>

        <div style="background: #141c17; border: 1px solid var(--card-border); border-radius: 22px; padding: 22px; box-shadow: 0 10px 25px rgba(0,0,0,0.3);">
            <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; text-transform: uppercase;">Total Invested In Shares</div>
            <div style="font-size: 1.7rem; font-weight: 800; color: var(--text-main);">$<?php echo number_format($total_shares_invested, 2); ?></div>
            <div style="font-size: 0.78rem; color: #64748b; margin-top: 4px;">Principal acquisition cost</div>
        </div>

        <div style="background: #141c17; border: 1px solid var(--card-border); border-radius: 22px; padding: 22px; box-shadow: 0 10px 25px rgba(0,0,0,0.3);">
            <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; text-transform: uppercase;">Net Shares Return / Profit</div>
            <div style="font-size: 1.7rem; font-weight: 800; color: <?php echo $total_shares_pnl >= 0 ? 'var(--primary-accent)' : '#ff5252'; ?>;">
                <?php echo ($total_shares_pnl >= 0 ? '+$' : '-$') . number_format(abs($total_shares_pnl), 2); ?>
            </div>
            <div style="font-size: 0.78rem; color: #64748b; margin-top: 4px;">Compounded equity dividend & growth</div>
        </div>
    </div>

    <!-- Featured Stocks Showcase Cards -->
    <h3 style="font-weight: 800; font-size: 1.25rem; color: #fff; margin-bottom: 16px;">
        <i class="fa-solid fa-chart-line" style="color:var(--primary-accent); margin-right:8px;"></i> Live Market Equities
    </h3>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; margin-bottom: 36px;">
        
        <!-- 1. Tech & Mobility Index (TSLA) -->
        <div style="background: #141c17; border: 1px solid rgba(232, 33, 39, 0.3); border-radius: 26px; padding: 24px; position: relative; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.4); display: flex; flex-direction: column; justify-content: space-between;">
            <div style="position:absolute; top:0; right:0; width:120px; height:120px; background:radial-gradient(circle, rgba(232,33,39,0.15) 0%, transparent 70%); pointer-events:none;"></div>
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 46px; height: 46px; border-radius: 14px; background: rgba(232,33,39,0.12); color: #e82127; border: 1px solid rgba(232,33,39,0.25); display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                        <div>
                            <div style="font-weight: 800; font-size: 1.15rem; color: #fff;">Tech & Mobility Index</div>
                            <div style="font-size: 0.75rem; color: #94a3b8;">GLOBAL INDEX &bull; TSLA &bull; Energy Infrastructure</div>
                        </div>
                    </div>
                    <span style="font-size: 0.72rem; background: rgba(46,204,113,0.12); color: #2ecc71; border: 1px solid rgba(46,204,113,0.25); padding: 3px 8px; border-radius: 8px; font-weight: 800;">LIVE</span>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 16px;">
                    <div style="font-size: 1.9rem; font-weight: 900; color: #fff;" id="deskPriceTSLA">$248.60</div>
                    <div style="font-size: 0.95rem; font-weight: 800; color: #2ecc71;" id="deskChangeTSLA">▲ +4.35%</div>
                </div>

                <p style="font-size: 0.84rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 20px;">
                    Autonomous EV mobility fleet, Megapack grid energy contracts, and Dojo supercomputer arbitrage.
                </p>
            </div>

            <button onclick="openBuyModal('TSLA', 'Tech & Mobility Index', 248.60)" style="width: 100%; background: #e82127; color: #fff; font-weight: 800; font-size: 0.94rem; border: none; padding: 14px; border-radius: 16px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 8px 24px rgba(232,33,39,0.3); transition: all 0.2s ease;">
                <i class="fa-solid fa-cart-plus"></i> Buy TSLA Index Shares
            </button>
        </div>

        <!-- 2. Apple Inc (AAPL) -->
        <div style="background: #141c17; border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 26px; padding: 24px; position: relative; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.4); display: flex; flex-direction: column; justify-content: space-between;">
            <div style="position:absolute; top:0; right:0; width:120px; height:120px; background:radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%); pointer-events:none;"></div>
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 46px; height: 46px; border-radius: 14px; background: rgba(255,255,255,0.08); color: #fff; border: 1px solid rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                            <i class="fa-brands fa-apple"></i>
                        </div>
                        <div>
                            <div style="font-weight: 800; font-size: 1.15rem; color: #fff;">Apple, Inc.</div>
                            <div style="font-size: 0.75rem; color: #94a3b8;">NASDAQ &bull; AAPL &bull; Consumer Silicon</div>
                        </div>
                    </div>
                    <span style="font-size: 0.72rem; background: rgba(46,204,113,0.12); color: #2ecc71; border: 1px solid rgba(46,204,113,0.25); padding: 3px 8px; border-radius: 8px; font-weight: 800;">LIVE</span>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 16px;">
                    <div style="font-size: 1.9rem; font-weight: 900; color: #fff;" id="deskPriceAAPL">$228.40</div>
                    <div style="font-size: 0.95rem; font-weight: 800; color: #2ecc71;" id="deskChangeAAPL">▲ +1.82%</div>
                </div>

                <p style="font-size: 0.84rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 20px;">
                    Apple Intelligence on-device neural processing, services cash flows, and global digital ecosystem hardware.
                </p>
            </div>

            <button onclick="openBuyModal('AAPL', 'Apple, Inc.', 228.40)" style="width: 100%; background: #ffffff; color: #000; font-weight: 800; font-size: 0.94rem; border: none; padding: 14px; border-radius: 16px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 8px 24px rgba(255,255,255,0.2); transition: all 0.2s ease;">
                <i class="fa-solid fa-cart-plus"></i> Buy AAPL Shares
            </button>
        </div>

        <!-- 3. SpaceX Syndicate (SPACEX) -->
        <div style="background: #141c17; border: 1px solid rgba(2, 132, 199, 0.3); border-radius: 26px; padding: 24px; position: relative; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.4); display: flex; flex-direction: column; justify-content: space-between;">
            <div style="position:absolute; top:0; right:0; width:120px; height:120px; background:radial-gradient(circle, rgba(2,132,199,0.15) 0%, transparent 70%); pointer-events:none;"></div>
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 46px; height: 46px; border-radius: 14px; background: rgba(2,132,199,0.12); color: #0284c7; border: 1px solid rgba(2,132,199,0.25); display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                            <i class="fa-solid fa-rocket"></i>
                        </div>
                        <div>
                            <div style="font-weight: 800; font-size: 1.15rem; color: #fff;">SpaceX Syndicate</div>
                            <div style="font-size: 0.75rem; color: #94a3b8;">PRE-IPO &bull; Starlink Satellite Fleet</div>
                        </div>
                    </div>
                    <span style="font-size: 0.72rem; background: rgba(2,132,199,0.15); color: #0284c7; border: 1px solid rgba(2,132,199,0.3); padding: 3px 8px; border-radius: 8px; font-weight: 800;">PRE-IPO</span>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 16px;">
                    <div style="font-size: 1.9rem; font-weight: 900; color: #fff;" id="deskPriceSPACEX">$210.00</div>
                    <div style="font-size: 0.95rem; font-weight: 800; color: #0284c7;" id="deskChangeSPACEX">▲ +15.80%</div>
                </div>

                <p style="font-size: 0.84rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 20px;">
                    Direct access to secondary pre-IPO venture rounds, Starship lunar launch contracts, and global high-speed satellite broadband.
                </p>
            </div>

            <button onclick="openBuyModal('SPACEX', 'SpaceX Syndicate', 210.00)" style="width: 100%; background: #0284c7; color: #fff; font-weight: 800; font-size: 0.94rem; border: none; padding: 14px; border-radius: 16px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 8px 24px rgba(2,132,199,0.3); transition: all 0.2s ease;">
                <i class="fa-solid fa-cart-plus"></i> Buy SpaceX Shares
            </button>
        </div>

    </div>

    <!-- User's Shares Portfolio & Transaction History Table -->
    <div style="background: #141c17; border: 1px solid var(--card-border); border-radius: 28px; padding: 28px; box-shadow: 0 10px 30px rgba(0,0,0,0.4);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
            <div>
                <h3 style="font-weight: 800; font-size: 1.25rem; color: #fff; margin: 0;">
                    <i class="fa-solid fa-clock-rotate-left" style="color:var(--primary-accent); margin-right:8px;"></i> Shares Transaction History
                </h3>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin: 4px 0 0;">Real-time tracking of orders, approved shares holdings, and administrator profit/loss adjustments.</p>
            </div>
            <span style="font-size: 0.82rem; color: var(--text-muted);">Total Orders: <strong><?php echo count($user_orders); ?></strong></span>
        </div>

        <?php if (empty($user_orders)) { ?>
            <div style="text-align: center; padding: 48px 16px; color: var(--text-muted);">
                <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(255,255,255,0.04); display: inline-flex; align-items: center; justify-content: center; font-size: 1.8rem; margin-bottom: 12px; color: var(--primary-accent);">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <h5 style="color: #fff; font-weight: 700; margin-bottom: 6px;">No Shares Orders Yet</h5>
                <p style="font-size: 0.85rem; max-width: 420px; margin: 0 auto 18px;">You currently have no equity shares purchases. Select Tech, Silicon, or Aerospace indices above to acquire institutional shares.</p>
                <button onclick="openBuyModal('TSLA')" class="btn-deposit-gold" style="padding: 10px 22px; font-size: 0.88rem; border-radius: 14px;">
                    Start With Tech Shares
                </button>
            </div>
        <?php } else { ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.88rem;">
                    <thead>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.08); color: #94a3b8; font-size: 0.78rem; text-transform: uppercase;">
                            <th style="padding: 14px 10px;">Order Code</th>
                            <th style="padding: 14px 10px;">Asset / Stock</th>
                            <th style="padding: 14px 10px; text-align: right;">Shares</th>
                            <th style="padding: 14px 10px; text-align: right;">Entry Price</th>
                            <th style="padding: 14px 10px; text-align: right;">Invested</th>
                            <th style="padding: 14px 10px; text-align: right;">Current Value</th>
                            <th style="padding: 14px 10px; text-align: right;">Profit / Loss</th>
                            <th style="padding: 14px 10px; text-align: center;">Status</th>
                            <th style="padding: 14px 10px; text-align: right;">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($user_orders as $ord) { 
                            $pnl = floatval($ord['profit_loss']);
                            $is_profit = $pnl >= 0;
                            $st = $ord['status'];
                            $badge_bg = 'rgba(241,196,15,0.15)';
                            $badge_color = '#f1c40f';
                            if ($st === 'Approved') {
                                $badge_bg = 'rgba(46,204,113,0.15)';
                                $badge_color = '#2ecc71';
                            } elseif ($st === 'Rejected') {
                                $badge_bg = 'rgba(231,76,60,0.15)';
                                $badge_color = '#e74c3c';
                            }
                        ?>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.04); transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 14px 10px; font-weight: 700; color: #fff; font-family: monospace;">
                                <?php echo htmlspecialchars($ord['order_code']); ?>
                            </td>
                            <td style="padding: 14px 10px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <?php if ($ord['symbol'] == 'TSLA') { ?>
                                        <i class="fa-solid fa-bolt" style="color:#e82127;"></i>
                                    <?php } elseif ($ord['symbol'] == 'AAPL') { ?>
                                        <i class="fa-brands fa-apple" style="color:#fff;"></i>
                                    <?php } else { ?>
                                        <i class="fa-solid fa-rocket" style="color:#0284c7;"></i>
                                    <?php } ?>
                                    <strong><?php echo htmlspecialchars($ord['asset_name']); ?></strong>
                                    <span style="font-size:0.75rem; color:var(--text-muted);">(<?php echo htmlspecialchars($ord['symbol']); ?>)</span>
                                </div>
                            </td>
                            <td style="padding: 14px 10px; text-align: right; font-weight: 700;">
                                <?php echo number_format($ord['shares_qty'], 2); ?>
                            </td>
                            <td style="padding: 14px 10px; text-align: right; color: #94a3b8;">
                                $<?php echo number_format($ord['purchase_price'], 2); ?>
                            </td>
                            <td style="padding: 14px 10px; text-align: right; font-weight: 700; color: #fff;">
                                $<?php echo number_format($ord['total_amount'], 2); ?>
                            </td>
                            <td style="padding: 14px 10px; text-align: right; font-weight: 800; color: #fff;">
                                $<?php echo number_format($ord['current_value'], 2); ?>
                            </td>
                            <td style="padding: 14px 10px; text-align: right; font-weight: 800; color: <?php echo $is_profit ? 'var(--primary-accent)' : '#ff5252'; ?>;">
                                <?php echo ($is_profit ? '▲ +$' : '▼ -$') . number_format(abs($pnl), 2); ?>
                                <span style="font-size:0.75rem; display:block; opacity:0.85;">(<?php echo ($is_profit ? '+' : '') . number_format($ord['profit_loss_pct'], 2); ?>%)</span>
                            </td>
                            <td style="padding: 14px 10px; text-align: center;">
                                <span style="background: <?php echo $badge_bg; ?>; color: <?php echo $badge_color; ?>; padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 800;">
                                    <?php echo htmlspecialchars($ord['status']); ?>
                                </span>
                            </td>
                            <td style="padding: 14px 10px; text-align: right; color: #64748b; font-size: 0.8rem;">
                                <?php echo date('M d, Y', strtotime($ord['created_at'])); ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>
    </div>

</div>

<!-- Interactive Buy Shares Modal -->
<div id="buySharesModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 16px;">
    <div style="background: #141c17; border: 1px solid var(--card-border); border-radius: 28px; max-width: 500px; width: 100%; padding: 32px 28px; box-shadow: 0 20px 60px rgba(0,0,0,0.8); position: relative;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div>
                <h3 style="font-weight: 800; font-size: 1.4rem; color: #fff; margin: 0;">Buy Stock Shares</h3>
                <p style="font-size: 0.82rem; color: var(--text-muted); margin: 3px 0 0;">Enter desired shares. Calculated total will be funded via deposit gateway.</p>
            </div>
            <button onclick="closeBuyModal()" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); color: #fff; width: 36px; height: 36px; border-radius: 50%; cursor: pointer;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form method="post" action="shares.php" id="modalBuyForm">
            <!-- Stock Asset Selector -->
            <div style="margin-bottom: 18px;">
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #cbd5e1; margin-bottom: 7px;">Select Target Equity</label>
                <select name="stock_symbol" id="modalStockSelect" onchange="onStockSelectChange()" style="width: 100%; background: #0b100d; border: 1px solid rgba(255,255,255,0.12); color: #fff; padding: 13px 16px; border-radius: 14px; font-size: 0.95rem; font-weight: 700; outline: none;">
                    <option value="TSLA" data-price="248.60" data-name="Tech & Mobility Index">Tech & Mobility Index (TSLA) — $248.60</option>
                    <option value="AAPL" data-price="228.40" data-name="Silicon & Technology Index">Silicon & Technology Index (AAPL) — $228.40</option>
                    <option value="SPACEX" data-price="210.00" data-name="Aerospace Syndicate">Aerospace Syndicate (SPACEX) — $210.00</option>
                </select>
            </div>

            <!-- Current Price Quote Box -->
            <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 16px; padding: 14px 18px; margin-bottom: 18px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Live Share Price</div>
                    <div style="font-size: 1.3rem; font-weight: 900; color: #fff;" id="modalPriceDisplay">$248.60</div>
                </div>
                <input type="hidden" name="market_price" id="modalPriceInput" value="248.60">
                <span style="font-size: 0.78rem; background: rgba(0,176,116,0.12); color: #2ecc71; border: 1px solid rgba(0,176,116,0.25); padding: 4px 10px; border-radius: 10px; font-weight: 800;">
                    <i class="fa-solid fa-chart-line"></i> REAL-TIME
                </span>
            </div>

            <!-- Number of Shares Input -->
            <div style="margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 7px;">
                    <label style="font-size: 0.82rem; font-weight: 700; color: #cbd5e1; margin: 0;">Quantity of Shares</label>
                    <span style="font-size: 0.75rem; color: var(--primary-accent); cursor: pointer;" onclick="setPresetShares(10)">Preset 10</span>
                </div>
                <div style="display: flex; gap: 8px;">
                    <input type="number" name="shares_qty" id="modalSharesQty" value="5" min="1" step="1" oninput="calculateTotalCost()" style="width: 100%; background: #0b100d; border: 1px solid rgba(255,255,255,0.12); color: #fff; padding: 13px 16px; border-radius: 14px; font-size: 1.1rem; font-weight: 800; outline: none;" required>
                </div>
                <!-- Quick Preset Chips -->
                <div style="display: flex; gap: 8px; margin-top: 8px;">
                    <button type="button" onclick="setPresetShares(1)" style="flex:1; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); color: #cbd5e1; padding: 6px; border-radius: 8px; font-size: 0.78rem; font-weight: 700; cursor: pointer;">1 Share</button>
                    <button type="button" onclick="setPresetShares(5)" style="flex:1; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); color: #cbd5e1; padding: 6px; border-radius: 8px; font-size: 0.78rem; font-weight: 700; cursor: pointer;">5 Shares</button>
                    <button type="button" onclick="setPresetShares(10)" style="flex:1; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); color: #cbd5e1; padding: 6px; border-radius: 8px; font-size: 0.78rem; font-weight: 700; cursor: pointer;">10 Shares</button>
                    <button type="button" onclick="setPresetShares(50)" style="flex:1; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); color: #cbd5e1; padding: 6px; border-radius: 8px; font-size: 0.78rem; font-weight: 700; cursor: pointer;">50 Shares</button>
                    <button type="button" onclick="setPresetShares(100)" style="flex:1; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); color: #cbd5e1; padding: 6px; border-radius: 8px; font-size: 0.78rem; font-weight: 700; cursor: pointer;">100 Shares</button>
                </div>
            </div>

            <!-- Total Cost Summary Box -->
            <div style="background: rgba(204,255,0,0.04); border: 1px solid rgba(204,255,0,0.2); border-radius: 18px; padding: 18px; margin-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 0.85rem; color: #94a3b8;">
                    <span>Execution Fee</span>
                    <span style="color: var(--primary-accent); font-weight: 700;">$0.00 (Zero Commission)</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: baseline;">
                    <span style="font-size: 0.95rem; font-weight: 700; color: #fff;">Total Investment Required:</span>
                    <span style="font-size: 1.6rem; font-weight: 900; color: var(--primary-accent);" id="modalTotalDisplay">$1,243.00</span>
                </div>
            </div>

            <!-- Submit Button: Takes User Directly to Deposit Gateway -->
            <button type="submit" name="place_shares_order" style="width: 100%; background: var(--primary-accent); color: #000; font-weight: 800; font-size: 1.02rem; border: none; padding: 16px; border-radius: 16px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; box-shadow: 0 10px 28px var(--primary-accent-glow); transition: all 0.25s ease;">
                <span>Proceed to Deposit Gateway</span>
                <i class="fa-solid fa-arrow-right"></i>
            </button>
            <p style="text-align: center; font-size: 0.78rem; color: var(--text-muted); margin-top: 10px; margin-bottom: 0;">
                <i class="fa-solid fa-lock"></i> Secured with Admin Multi-Sig Verification
            </p>
        </form>
    </div>
</div>

<script>
// Modal Controls
function openBuyModal(symbol, name, price) {
    const modal = document.getElementById('buySharesModal');
    const select = document.getElementById('modalStockSelect');
    if (symbol && select) {
        select.value = symbol;
    }
    onStockSelectChange();
    modal.style.display = 'flex';
}

function closeBuyModal() {
    document.getElementById('buySharesModal').style.display = 'none';
}

function onStockSelectChange() {
    const select = document.getElementById('modalStockSelect');
    const opt = select.options[select.selectedIndex];
    const price = parseFloat(opt.getAttribute('data-price') || 248.60);
    
    document.getElementById('modalPriceDisplay').innerText = '$' + price.toFixed(2);
    document.getElementById('modalPriceInput').value = price.toFixed(2);
    calculateTotalCost();
}

function setPresetShares(qty) {
    document.getElementById('modalSharesQty').value = qty;
    calculateTotalCost();
}

function calculateTotalCost() {
    const price = parseFloat(document.getElementById('modalPriceInput').value || 248.60);
    const qty = parseFloat(document.getElementById('modalSharesQty').value || 1);
    const total = (price * (qty > 0 ? qty : 1)).toFixed(2);
    document.getElementById('modalTotalDisplay').innerText = '$' + parseFloat(total).toLocaleString(undefined, {minimumFractionDigits: 2});
}

// Live Price Tickers for Stocks
(function initStockDeskLiveTicks() {
    const stockDeskData = {
        'TSLA': { price: 248.60, elPrice: 'deskPriceTSLA', elChg: 'deskChangeTSLA', change: 4.35 },
        'AAPL': { price: 228.40, elPrice: 'deskPriceAAPL', elChg: 'deskChangeAAPL', change: 1.82 },
        'SPACEX': { price: 210.00, elPrice: 'deskPriceSPACEX', elChg: 'deskChangeSPACEX', change: 15.80 }
    };

    setInterval(() => {
        ['TSLA', 'AAPL', 'SPACEX'].forEach(sym => {
            const item = stockDeskData[sym];
            const jitter = (Math.random() - 0.49) * (item.price * 0.0018);
            item.price = parseFloat((item.price + jitter).toFixed(2));
            
            const elP = document.getElementById(item.elPrice);
            if (elP) {
                elP.innerText = '$' + item.price.toLocaleString(undefined, {minimumFractionDigits: 2});
                // Flashing tick effect
                elP.style.color = jitter >= 0 ? '#2ecc71' : '#ff5252';
                setTimeout(() => elP.style.color = '#ffffff', 400);
            }
        });
    }, 4000);

    // Auto-open modal if symbol requested via URL query (e.g. shares.php?symbol=TSLA)
    const urlParams = new URLSearchParams(window.location.search);
    const qSym = urlParams.get('symbol');
    if (qSym && ['TSLA', 'AAPL', 'SPACEX'].includes(qSym.toUpperCase())) {
        setTimeout(() => {
            openBuyModal(qSym.toUpperCase());
        }, 150);
    }
})();
</script>

<?php 
include_once __DIR__ . '/footer.php';
?>
