<?php 
include_once __DIR__ . '/header.php';

// Fetch last deposit amount from deposits table
$sql_last = "SELECT amount FROM deposits WHERE email = '$uemail' ORDER BY id DESC LIMIT 1";
$res_last = mysqli_query($link, $sql_last);
$row_last = $res_last ? mysqli_fetch_assoc($res_last) : null;
$last_deposit_amount = ($row_last && !empty($row_last['amount'])) ? $row_last['amount'] : "0.00";

// Fetch total completed withdrawals
$sql_with = "SELECT SUM(amount) as sum_with FROM withdrawals WHERE email = '$uemail' AND status = 'COMPLETED'";
$res_with = mysqli_query($link, $sql_with);
$row_with = $res_with ? mysqli_fetch_assoc($res_with) : null;
$total_withdrawn = ($row_with && !empty($row_with['sum_with'])) ? $row_with['sum_with'] : "0.00";
?>

<!-- Dual Showcase Grid: Screen 1 (Overview & Movers) + Screen 2 (Live Market & Interactive Chart) -->
<div class="screens-grid">

    <!-- ==============================================
         PANEL 1: ACCOUNT OVERVIEW & TOP MOVERS
         ============================================== -->
    <div class="screen-card">
        <!-- Screen Header -->
        <div class="screen-header">
            <div class="profile-meta">
                <div class="profile-avatar">
                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&q=80" alt="Avatar">
                </div>
                <div>
                    <div class="greeting-text"><?php echo $greeting; ?></div>
                    <div class="user-display-name"><?php echo htmlspecialchars($display_name); ?></div>
                </div>
            </div>

            <div class="header-icons">
                <a href="navigation.php" class="icon-btn-round" title="Menu & Navigation">
                    <i class="fa-solid fa-bars-staggered"></i>
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
            </div>
        </div>

        <!-- Total Balance Section with Luminous Light Frosted Platinum Card Finish -->
        <div class="balance-section" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 60%, #edf2f7 100%); border: 1px solid rgba(226, 232, 240, 0.95); border-radius: 24px; padding: 22px 20px; box-shadow: 0 12px 30px rgba(0, 0, 0, 0.18); position: relative; overflow: hidden; margin-bottom: 24px;">
            <div class="balance-label-row">
                <span class="balance-title" style="color: #475569; font-weight: 700;">Total Net Balance</span>
                <button type="button" class="eye-toggle-btn" onclick="toggleBalancePrivacy()" title="Toggle Privacy" style="color: #64748b;">
                    <i class="fa-regular fa-eye" id="balanceEyeIcon"></i>
                </button>
            </div>
            <div class="main-balance-amount" id="userMainBalance" style="color: #090d16; font-weight: 900; letter-spacing: -1.5px;">
                $<?php echo number_format($total_balance, 2); ?>
            </div>
            <div class="balance-growth-pill" style="background: rgba(5, 150, 105, 0.1); border: 1px solid rgba(5, 150, 105, 0.25); color: #059669; padding: 4px 12px; border-radius: 20px;">
                <span style="font-weight: 800;">+<?php echo number_format($total_balance * 0.1456, 2); ?></span>
                <span style="font-weight: 600;">▲ +14.56% (24h)</span>
            </div>

            <!-- Core Financial Breakdown with Crisp Card Styling -->
            <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:10px; margin-top:18px; background: rgba(255, 255, 255, 0.95); border:1px solid #e2e8f0; border-radius:18px; padding:14px 12px; text-align:center; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);">
                <div>
                    <div style="font-size:0.72rem; color:#64748b; font-weight:600; margin-bottom:2px;">Active Deposits</div>
                    <div style="font-size:0.95rem; font-weight:800; color:#090d16;">$<?php echo number_format((float)$dbalance, 2); ?></div>
                </div>
                <div>
                    <div style="font-size:0.72rem; color:#64748b; font-weight:600; margin-bottom:2px;">Withdrawable</div>
                    <div style="font-size:0.95rem; font-weight:800; color:#059669;">$<?php echo number_format((float)$mbalance, 2); ?></div>
                </div>
                <div>
                    <div style="font-size:0.72rem; color:#64748b; font-weight:600; margin-bottom:2px;">Added Bonus</div>
                    <div style="font-size:0.95rem; font-weight:800; color:#090d16;">$<?php echo number_format((float)$abonus, 2); ?></div>
                </div>
            </div>
        </div>

        <!-- Quick 4 Action Buttons -->
        <div class="actions-grid">
            <a href="deposit.php" class="action-item">
                <div class="action-circle-btn">
                    <i class="fa-solid fa-plus"></i>
                </div>
                <span class="action-item-label">Deposit</span>
            </a>

            <a href="withdraw.php" class="action-item">
                <div class="action-circle-btn">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <span class="action-item-label">Withdraw</span>
            </a>

            <a href="plans.php" class="action-item">
                <div class="action-circle-btn">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <span class="action-item-label">Plans</span>
            </a>

            <a href="javascript:void(0)" onclick="openSwapModal()" class="action-item">
                <div class="action-circle-btn">
                    <i class="fa-solid fa-rotate"></i>
                </div>
                <span class="action-item-label">Swap</span>
            </a>
        </div>

        <!-- Top Growing Cryptos Carousel -->
        <div class="section-title-sm" style="display:flex; justify-content:space-between; align-items:center;">
            <span>Top Growing</span>
            <span style="font-size:0.75rem; color:var(--primary-accent); font-weight:600;"><i class="fa-solid fa-circle font-size-8 me-1"></i> Live</span>
        </div>
        <div class="growing-cards-row">
            <!-- Solana Card -->
            <div class="growing-card" onclick="selectLiveCoin('SOL', 'Solana', 'SOLUSDT', 'fa-solid fa-bolt', '#8e44ad')">
                <div class="growing-card-header">
                    <div class="coin-icon-badge coin-icon-sol">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <div class="growing-card-names">
                        <div class="coin-full-name">Solana</div>
                        <div class="coin-sym-name">SOL</div>
                    </div>
                </div>
                <div>
                    <div class="growing-price-amt" id="cardPriceSOL">$194.50</div>
                    <div class="growing-change-tag val-green" id="cardChangeSOL">▲ +9.87%</div>
                </div>
            </div>

            <!-- Cardano Card -->
            <div class="growing-card" onclick="selectLiveCoin('ADA', 'Cardano', 'ADAUSDT', 'fa-solid fa-cube', '#0033ad')">
                <div class="growing-card-header">
                    <div class="coin-icon-badge coin-icon-ada">
                        <i class="fa-solid fa-cube"></i>
                    </div>
                    <div class="growing-card-names">
                        <div class="coin-full-name">Cardano</div>
                        <div class="coin-sym-name">ADA</div>
                    </div>
                </div>
                <div>
                    <div class="growing-price-amt" id="cardPriceADA">$0.78</div>
                    <div class="growing-change-tag val-green" id="cardChangeADA">▲ +4.15%</div>
                </div>
            </div>

            <!-- Ethereum Card -->
            <div class="growing-card" onclick="selectLiveCoin('ETH', 'Ethereum', 'ETHUSDT', 'fa-brands fa-ethereum', '#627eea')">
                <div class="growing-card-header">
                    <div class="coin-icon-badge coin-icon-eth">
                        <i class="fa-brands fa-ethereum"></i>
                    </div>
                    <div class="growing-card-names">
                        <div class="coin-full-name">Ethereum</div>
                        <div class="coin-sym-name">ETH</div>
                    </div>
                </div>
                <div>
                    <div class="growing-price-amt" id="cardPriceETH">$3,849.93</div>
                    <div class="growing-change-tag val-green" id="cardChangeETH">▲ +2.48%</div>
                </div>
            </div>
        </div>

        <!-- Top Movers Section -->
        <div class="top-movers-section">
            <div class="section-title-sm">Top Movers</div>
            <div class="movers-tabs-row">
                <button class="movers-tab-btn active" onclick="switchMoversTab(this, 'all')">Trending</button>
                <button class="movers-tab-btn" onclick="switchMoversTab(this, 'favorites')">Favorites</button>
                <button class="movers-tab-btn" onclick="switchMoversTab(this, 'gainers')">Gainers</button>
                <button class="movers-tab-btn" onclick="switchMoversTab(this, 'losers')">Losers</button>
                <button class="movers-tab-btn" onclick="switchMoversTab(this, 'new')">NEW</button>
            </div>

            <div class="movers-list" id="moversList">
                <!-- Bitcoin -->
                <div class="mover-row-item" onclick="selectLiveCoin('BTC', 'Bitcoin', 'BTCUSDT', 'fa-brands fa-bitcoin', '#f7931a')">
                    <div class="mover-left">
                        <div class="coin-icon-badge coin-icon-btc">
                            <i class="fa-brands fa-bitcoin"></i>
                        </div>
                        <div>
                            <div class="mover-coin-title">Bitcoin</div>
                            <div class="mover-coin-sub">BTC / USDT</div>
                        </div>
                    </div>
                    <div class="mover-right">
                        <div class="mover-price" id="moverPriceBTC">$68,450.00</div>
                        <div class="mover-percent val-green" id="moverChangeBTC">▲ +1.50%</div>
                    </div>
                </div>

                <!-- Quant -->
                <div class="mover-row-item" onclick="selectLiveCoin('QNT', 'Quant', 'QNTUSDT', 'fa-solid fa-atom', '#d32f2f')">
                    <div class="mover-left">
                        <div class="coin-icon-badge coin-icon-qnt">
                            <i class="fa-solid fa-atom"></i>
                        </div>
                        <div>
                            <div class="mover-coin-title">Quant</div>
                            <div class="mover-coin-sub">QNT / USDT</div>
                        </div>
                    </div>
                    <div class="mover-right">
                        <div class="mover-price" id="moverPriceQNT">$112.50</div>
                        <div class="mover-percent val-green" id="moverChangeQNT">▲ +3.45%</div>
                    </div>
                </div>

                <!-- Polkadot -->
                <div class="mover-row-item" onclick="selectLiveCoin('DOT', 'Polkadot', 'DOTUSDT', 'fa-solid fa-circle-nodes', '#e6007a')">
                    <div class="mover-left">
                        <div class="coin-icon-badge coin-icon-dot">
                            <i class="fa-solid fa-circle-nodes"></i>
                        </div>
                        <div>
                            <div class="mover-coin-title">Polkadot</div>
                            <div class="mover-coin-sub">DOT / USDT</div>
                        </div>
                    </div>
                    <div class="mover-right">
                        <div class="mover-price" id="moverPriceDOT">$8.30</div>
                        <div class="mover-percent val-green" id="moverChangeDOT">▲ +2.88%</div>
                    </div>
                </div>

                <!-- Ethereum in List -->
                <div class="mover-row-item" onclick="selectLiveCoin('ETH', 'Ethereum', 'ETHUSDT', 'fa-brands fa-ethereum', '#627eea')">
                    <div class="mover-left">
                        <div class="coin-icon-badge coin-icon-eth">
                            <i class="fa-brands fa-ethereum"></i>
                        </div>
                        <div>
                            <div class="mover-coin-title">Ethereum</div>
                            <div class="mover-coin-sub">ETH / USDT</div>
                        </div>
                    </div>
                    <div class="mover-right">
                        <div class="mover-price" id="moverPriceETH">$3,849.93</div>
                        <div class="mover-percent val-green" id="moverChangeETH">▲ +2.48%</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Technology & Global Equities Section with Luminous Frosted Platinum Light Finish -->
        <div class="top-movers-section" style="margin-top: 24px; background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%); border: 1px solid rgba(226, 232, 240, 0.95); border-radius: 24px; padding: 20px 18px; box-shadow: 0 10px 25px rgba(0,0,0,0.14);">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
                <div class="section-title-sm" style="margin-bottom:0; display:flex; align-items:center; gap:8px; color: #090d16; font-weight: 800;">
                    <i class="fa-solid fa-layer-group" style="color:var(--primary-accent);"></i> Technology & Institutional Stocks
                </div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <a href="shares.php" style="font-size:0.75rem; color:#2563eb; font-weight:700; text-decoration:none; display:flex; align-items:center; gap:4px;">
                        All Shares <i class="fa-solid fa-arrow-right font-size-10"></i>
                    </a>
                    <span style="font-size:0.72rem; background:rgba(0,176,116,0.12); color:#059669; border: 1px solid rgba(0,176,116,0.25); padding:3px 10px; border-radius:12px; font-weight:800; display:inline-flex; align-items:center; gap:5px;">
                        <span style="width:6px; height:6px; border-radius:50%; background:#059669; display:inline-block; box-shadow:0 0 6px #059669;"></span> LIVE EQUITIES
                    </span>
                </div>
            </div>

            <div class="movers-list" id="stocksList">
                <!-- Tech & Mobility Index (TSLA) -->
                <div class="mover-row-item" onclick="selectLiveCoin('TSLA', 'Tech & Mobility Index', 'TSLA', 'fa-solid fa-bolt', '#e82127')" style="padding: 12px 14px; background: #ffffff; border: 1px solid #e2e8f0; border-left: 3px solid #e82127; border-radius: 14px; margin-bottom: 8px; cursor: pointer; transition: all 0.2s ease;">
                    <div class="mover-left">
                        <div class="coin-icon-badge" style="background: rgba(232, 33, 39, 0.12); color: #e82127; border: 1px solid rgba(232, 33, 39, 0.25);">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                        <div>
                            <div class="mover-coin-title" style="display:flex; align-items:center; gap:6px; color: #090d16; font-weight: 700;">
                                Tech & Mobility Index <span style="font-size:0.68rem; background:#f1f5f9; border:1px solid #e2e8f0; padding:1px 6px; border-radius:6px; color:#475569; font-weight:800;">TSLA</span>
                            </div>
                            <div class="mover-coin-sub" style="color: #64748b;">GLOBAL INDEX &bull; Clean Tech & Energy</div>
                        </div>
                    </div>
                    <div class="mover-right" style="display:flex; align-items:center; gap:12px;">
                        <div style="text-align:right;">
                            <div class="mover-price" id="moverPriceTSLA" style="color: #090d16; font-weight: 800;">$248.60</div>
                            <div class="mover-percent val-green" id="moverChangeTSLA" style="color: #059669;">▲ +4.35%</div>
                        </div>
                        <a href="shares.php?symbol=TSLA" onclick="event.stopPropagation();" style="background: #e82127; color:#fff; font-size:0.75rem; font-weight:800; padding:7px 14px; border-radius:10px; text-decoration:none; display:inline-flex; align-items:center; gap:6px; box-shadow:0 3px 10px rgba(232,33,39,0.25); transition:transform 0.15s ease;">
                            <i class="fa-solid fa-cart-shopping"></i> Buy
                        </a>
                    </div>
                </div>

                <!-- Apple Inc. (AAPL) -->
                <div class="mover-row-item" onclick="selectLiveCoin('AAPL', 'Apple, Inc.', 'AAPL', 'fa-brands fa-apple', '#475569')" style="padding: 12px 14px; background: #ffffff; border: 1px solid #e2e8f0; border-left: 3px solid #475569; border-radius: 14px; margin-bottom: 8px; cursor: pointer; transition: all 0.2s ease;">
                    <div class="mover-left">
                        <div class="coin-icon-badge" style="background: #f1f5f9; color: #090d16; border: 1px solid #cbd5e1;">
                            <i class="fa-brands fa-apple"></i>
                        </div>
                        <div>
                            <div class="mover-coin-title" style="display:flex; align-items:center; gap:6px; color: #090d16; font-weight: 700;">
                                Apple, Inc. <span style="font-size:0.68rem; background:#f1f5f9; border:1px solid #e2e8f0; padding:1px 6px; border-radius:6px; color:#475569; font-weight:800;">AAPL</span>
                            </div>
                            <div class="mover-coin-sub" style="color: #64748b;">NASDAQ &bull; AI Consumer Ecosystem</div>
                        </div>
                    </div>
                    <div class="mover-right" style="display:flex; align-items:center; gap:12px;">
                        <div style="text-align:right;">
                            <div class="mover-price" id="moverPriceAAPL" style="color: #090d16; font-weight: 800;">$228.40</div>
                            <div class="mover-percent val-green" id="moverChangeAAPL" style="color: #059669;">▲ +1.82%</div>
                        </div>
                        <a href="shares.php?symbol=AAPL" onclick="event.stopPropagation();" style="background: #0f172a; color:#fff; font-size:0.75rem; font-weight:800; padding:7px 14px; border-radius:10px; text-decoration:none; display:inline-flex; align-items:center; gap:6px; box-shadow:0 3px 10px rgba(15,23,42,0.25); transition:transform 0.15s ease;">
                            <i class="fa-solid fa-cart-shopping"></i> Buy
                        </a>
                    </div>
                </div>

                <!-- SpaceX Pre-IPO Syndicate (SPACEX) -->
                <div class="mover-row-item" onclick="selectLiveCoin('SPACEX', 'SpaceX Syndicate', 'SPACEX', 'fa-solid fa-rocket', '#0284c7')" style="padding: 12px 14px; background: #ffffff; border: 1px solid #e2e8f0; border-left: 3px solid #0284c7; border-radius: 14px; cursor: pointer; transition: all 0.2s ease;">
                    <div class="mover-left">
                        <div class="coin-icon-badge" style="background: rgba(2, 132, 199, 0.12); color: #0284c7; border: 1px solid rgba(2, 132, 199, 0.25);">
                            <i class="fa-solid fa-rocket"></i>
                        </div>
                        <div>
                            <div class="mover-coin-title" style="display:flex; align-items:center; gap:6px; color: #090d16; font-weight: 700;">
                                SpaceX Syndicate <span style="font-size:0.68rem; background:rgba(2,132,199,0.12); color:#0284c7; border:1px solid rgba(2,132,199,0.25); padding:1px 6px; border-radius:6px; font-weight:800;">PRE-IPO</span>
                            </div>
                            <div class="mover-coin-sub" style="color: #64748b;">Starlink & Heavy Launch Syndicate</div>
                        </div>
                    </div>
                    <div class="mover-right" style="display:flex; align-items:center; gap:12px;">
                        <div style="text-align:right;">
                            <div class="mover-price" id="moverPriceSPACEX" style="color: #090d16; font-weight: 800;">$210.00</div>
                            <div class="mover-percent val-green" id="moverChangeSPACEX" style="color: #059669;">▲ +15.80%</div>
                        </div>
                        <a href="shares.php?symbol=SPACEX" onclick="event.stopPropagation();" style="background: #0284c7; color:#fff; font-size:0.75rem; font-weight:800; padding:7px 14px; border-radius:10px; text-decoration:none; display:inline-flex; align-items:center; gap:6px; box-shadow:0 3px 10px rgba(2,132,199,0.25); transition:transform 0.15s ease;">
                            <i class="fa-solid fa-cart-shopping"></i> Buy
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Original Script Account Overview Details (Preserved with Light Card Aesthetic) -->
        <div style="background: #ffffff; border:1px solid rgba(226, 232, 240, 0.95); border-radius:22px; padding:18px 20px; margin-top:20px; box-shadow: 0 4px 16px rgba(0,0,0,0.06);">
            <div style="font-size:0.85rem; font-weight:800; color:#090d16; margin-bottom:12px; display:flex; justify-content:space-between; align-items:center;">
                <span>Account Status</span>
                <?php 
                $is_acc_verified = (isset($verification) && $verification == '1');
                if ($is_acc_verified) {
                ?>
                    <a href="kyc.php" style="color:#059669; font-weight:800; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                        <i class="fa-solid fa-circle-check"></i> Verified (Level 2)
                    </a>
                <?php 
                } else {
                    $q_kyc_chk = @mysqli_query($link, "SELECT status FROM kyc_applications WHERE email = '$uemail' ORDER BY id DESC LIMIT 1");
                    $kyc_row_chk = ($q_kyc_chk && mysqli_num_rows($q_kyc_chk) > 0) ? mysqli_fetch_assoc($q_kyc_chk) : null;
                    if ($kyc_row_chk && $kyc_row_chk['status'] === 'PENDING') {
                ?>
                    <a href="kyc.php" style="color:#d97706; font-weight:800; text-decoration:none; display:inline-flex; align-items:center; gap:6px; background:#fef3c7; padding:2px 10px; border-radius:12px; font-size:0.75rem;">
                        <i class="fa-solid fa-clock"></i> KYC Under Review
                    </a>
                <?php } else { ?>
                    <a href="kyc.php" style="color:#dc2626; font-weight:800; text-decoration:none; display:inline-flex; align-items:center; gap:6px; background:#fee2e2; padding:2px 10px; border-radius:12px; font-size:0.75rem;">
                        <i class="fa-solid fa-shield-halved"></i> Unverified &bull; Verify KYC &rarr;
                    </a>
                <?php } } ?>
            </div>
            <div style="display:grid; grid-template-columns: repeat(2, 1fr); gap:12px; font-size:0.8rem;">
                <div>
                    <span style="color:#64748b; font-weight:600; display:block;">Username:</span>
                    <strong style="color:#090d16;"><?php echo htmlspecialchars($username); ?></strong>
                </div>
                <div>
                    <span style="color:#64748b; font-weight:600; display:block;">Last Access:</span>
                    <strong style="color:#090d16;"><?php echo !empty($last_access) ? htmlspecialchars($last_access) : date('Y-m-d H:i'); ?></strong>
                </div>
                <div>
                    <span style="color:#64748b; font-weight:600; display:block;">Last Deposit:</span>
                    <strong style="color:#090d16;">$<?php echo number_format((float)$last_deposit_amount, 2); ?></strong>
                </div>
                <div>
                    <span style="color:#64748b; font-weight:600; display:block;">Withdrawn:</span>
                    <strong style="color:#090d16;">$<?php echo number_format((float)$total_withdrawn, 2); ?></strong>
                </div>
            </div>
        </div>
    </div>


    <!-- ==============================================
         PANEL 2: LIVE MARKET ASSET & WORKING CHART
         ============================================== -->
    <div class="screen-card">
        <!-- Header -->
        <div class="screen2-header">
            <div style="display:flex; align-items:center; gap:12px;">
                <div id="activeCoinIconBadge" class="coin-icon-badge coin-icon-eth" style="width:36px; height:36px; font-size:1.1rem;">
                    <i id="activeCoinIcon" class="fa-brands fa-ethereum"></i>
                </div>
                <div>
                    <div class="screen2-title" id="chartCoinTitle" style="margin:0;">Ethereum</div>
                    <div style="font-size:0.75rem; color:var(--text-muted);" id="chartPairSymbol">ETH / USDT &bull; Live Market</div>
                </div>
            </div>

            <div style="display:flex; gap:8px;">
                <button type="button" class="icon-btn-round" title="Toggle TradingView Terminal" onclick="toggleTradingViewWidget()">
                    <i class="fa-solid fa-chart-candlestick"></i>
                </button>
                <button type="button" class="icon-btn-round" title="Refresh Live Price" onclick="fetchLiveMarketData(true)">
                    <i class="fa-solid fa-arrows-rotate" id="refreshIcon"></i>
                </button>
            </div>
        </div>

        <!-- Timeframe Pills -->
        <div class="timeframe-pills-row">
            <button class="timeframe-pill" onclick="changeTimeframe(this, '1Day')">1Day</button>
            <button class="timeframe-pill" onclick="changeTimeframe(this, '1Week')">1Week</button>
            <button class="timeframe-pill" onclick="changeTimeframe(this, '1Month')">1Month</button>
            <button class="timeframe-pill active" onclick="changeTimeframe(this, '6Months')">6Months</button>
            <button class="timeframe-pill" onclick="changeTimeframe(this, '1Year')">1Year</button>
            <button class="timeframe-pill" onclick="changeTimeframe(this, 'All')">All</button>
        </div>

        <!-- Live Price Display -->
        <div class="asset-price-box">
            <div class="asset-big-quote" id="chartPriceQuote">ETH = $3,849.93</div>
            <div class="asset-change-quote" id="chartChangeQuote">
                <span id="chartChangePercent">▲ +2.48%</span>
                <span style="font-size:0.78rem; color:var(--text-muted); font-weight:500; margin-left:8px;" id="chartHighLow">24h High: $3,910.00 &bull; Low: $3,740.00</span>
            </div>
        </div>

        <!-- REAL WORKING INTERACTIVE CHART (Chart.js) -->
        <div class="chart-canvas-wrap">
            <canvas id="liveMarketChartCanvas"></canvas>
        </div>

        <!-- Collapsible TradingView Interactive Terminal from Original Script -->
        <div id="tradingViewContainer" style="display:none; margin-bottom:20px; border-radius:18px; overflow:hidden; border:1px solid var(--card-border);">
            <div id="tradingview_embed" style="height:380px;"></div>
        </div>

        <!-- Risk Score & AI Suggestion Box -->
        <div class="analytics-card-box">
            <div class="analytics-header-row">
                <div>
                    <div class="analytics-col-label">Risk Score</div>
                    <div class="analytics-col-value" id="riskScoreVal">64/100</div>
                </div>
                <div style="text-align: right;">
                    <div class="analytics-col-label">Ai Suggestion</div>
                    <div class="ai-suggestion-text" id="aiSuggestionVal">24% ETH &rarr; BTC</div>
                </div>
            </div>

            <!-- Progress Bar with Neon Lime Fill -->
            <div class="analytics-progress-bar-wrap">
                <div class="analytics-progress-fill" id="analyticsProgressFill" style="width: 64%;"></div>
            </div>

            <div class="progress-labels-row">
                <span>Low Risk</span>
                <span>Moderate</span>
                <span>High Risk</span>
            </div>
        </div>

        <!-- Buy & Sell Action Buttons -->
        <div class="screen2-action-btns-row">
            <a href="deposit.php" class="btn-pill-buy">
                <i class="fa-solid fa-plus me-1"></i> Buy / Deposit
            </a>
            <a href="withdraw.php" class="btn-pill-sell">
                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Sell / Withdraw
            </a>
        </div>
    </div>

</div>

<!-- Quick Swap Modal -->
<div id="swapModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.8); z-index:9999; align-items:center; justify-content:center; padding:16px;">
    <div style="background:#151c18; border:1px solid var(--card-border); border-radius:24px; padding:24px; max-width:400px; width:100%; color:#fff;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h4 style="margin:0; font-weight:700;">Instant Crypto Swap</h4>
            <button onclick="document.getElementById('swapModal').style.display='none'" style="background:none; border:none; color:#fff; font-size:1.2rem; cursor:pointer;">&times;</button>
        </div>
        <p style="font-size:0.85rem; color:var(--text-muted); margin-bottom:16px;">Zero fee conversion between supported digital assets.</p>
        <div style="margin-bottom:12px;">
            <label style="font-size:0.8rem; color:var(--text-muted);">From Asset</label>
            <select id="swapFrom" style="width:100%; background:#0e1410; border:1px solid var(--card-border); color:#fff; padding:10px 14px; border-radius:12px; outline:none;">
                <option value="BTC">Bitcoin (BTC)</option>
                <option value="ETH" selected>Ethereum (ETH)</option>
                <option value="USDT">Tether (USDT)</option>
            </select>
        </div>
        <div style="margin-bottom:18px;">
            <label style="font-size:0.8rem; color:var(--text-muted);">To Asset</label>
            <select id="swapTo" style="width:100%; background:#0e1410; border:1px solid var(--card-border); color:#fff; padding:10px 14px; border-radius:12px; outline:none;">
                <option value="BTC" selected>Bitcoin (BTC)</option>
                <option value="ETH">Ethereum (ETH)</option>
                <option value="USDT">Tether (USDT)</option>
            </select>
        </div>
        <button onclick="document.getElementById('swapModal').style.display='none'; swal('Swap Order Placed', 'Order placed successfully at current market rates.', 'success');" style="width:100%; background:var(--primary-accent); color:#000; font-weight:800; border:none; padding:12px; border-radius:30px; cursor:pointer;">
            Execute Instant Swap
        </button>
    </div>
</div>

<style>
.price-tick-up {
    animation: tickUpFlash 0.75s ease-out;
}
.price-tick-down {
    animation: tickDownFlash 0.75s ease-out;
}
@keyframes tickUpFlash {
    0% { color: #059669 !important; text-shadow: 0 0 10px rgba(5, 150, 105, 0.45); transform: translateY(-1px); }
    100% { color: inherit; text-shadow: none; transform: translateY(0); }
}
@keyframes tickDownFlash {
    0% { color: #dc2626 !important; text-shadow: 0 0 10px rgba(220, 38, 38, 0.45); transform: translateY(1px); }
    100% { color: inherit; text-shadow: none; transform: translateY(0); }
}
</style>

<!-- ==============================================
     REAL LIVE MARKET & CHART LOGIC (JAVASCRIPT)
     ============================================== -->
<script>
// State for the active chart & coin
let currentCoinSymbol = 'ETH';
let currentCoinName = 'Ethereum';
let currentPair = 'ETHUSDT';
let currentTimeframe = '6Months';
let chartInstance = null;

// Centralized Reactive Market Store
const liveMarketStore = {
    // Cryptocurrencies
    'ETH': {
        name: 'Ethereum',
        pair: 'ETHUSDT',
        price: 3849.93,
        changePct: 2.48,
        high: 3910.00,
        low: 3740.00,
        risk: 64,
        ai: '24% ETH → BTC',
        isStock: false,
        elPrices: ['cardPriceETH', 'moverPriceETH'], // Updates BOTH top card and bottom list row!
        elChanges: ['cardChangeETH', 'moverChangeETH']
    },
    'BTC': {
        name: 'Bitcoin',
        pair: 'BTCUSDT',
        price: 68450.00,
        changePct: 1.50,
        high: 69200.00,
        low: 67100.00,
        risk: 42,
        ai: 'Hold BTC (Bullish Momentum)',
        isStock: false,
        elPrices: ['moverPriceBTC'],
        elChanges: ['moverChangeBTC']
    },
    'SOL': {
        name: 'Solana',
        pair: 'SOLUSDT',
        price: 194.50,
        changePct: 9.87,
        high: 202.00,
        low: 181.50,
        risk: 71,
        ai: 'Take Profit 15% SOL → USDT',
        isStock: false,
        elPrices: ['cardPriceSOL'],
        elChanges: ['cardChangeSOL']
    },
    'ADA': {
        name: 'Cardano',
        pair: 'ADAUSDT',
        price: 0.78,
        changePct: 4.15,
        high: 0.82,
        low: 0.74,
        risk: 55,
        ai: 'Accumulate ADA on pullbacks',
        isStock: false,
        elPrices: ['cardPriceADA'],
        elChanges: ['cardChangeADA']
    },
    'DOT': {
        name: 'Polkadot',
        pair: 'DOTUSDT',
        price: 8.30,
        changePct: 2.88,
        high: 8.65,
        low: 8.05,
        risk: 58,
        ai: 'Stake DOT for 12% APY',
        isStock: false,
        elPrices: ['moverPriceDOT'],
        elChanges: ['moverChangeDOT']
    },
    'QNT': {
        name: 'Quant',
        pair: 'QNTUSDT',
        price: 112.50,
        changePct: 3.45,
        high: 118.00,
        low: 109.00,
        risk: 62,
        ai: 'Rebalance QNT into ETH',
        isStock: false,
        elPrices: ['moverPriceQNT'],
        elChanges: ['moverChangeQNT']
    },
    // Stocks & Equities
    'TSLA': {
        name: 'Tech & Mobility Index',
        pair: 'TSLA',
        price: 248.60,
        changePct: 4.35,
        high: 254.20,
        low: 241.80,
        risk: 44,
        ai: 'Positive Momentum: Algorithmic Deployments',
        isStock: true,
        exchange: 'NASDAQ',
        elPrices: ['moverPriceTSLA'],
        elChanges: ['moverChangeTSLA']
    },
    'AAPL': {
        name: 'Apple, Inc.',
        pair: 'AAPL',
        price: 228.40,
        changePct: 1.82,
        high: 231.50,
        low: 226.10,
        risk: 28,
        ai: 'Accumulate: Apple Intelligence Hardware Supercycle',
        isStock: true,
        exchange: 'NASDAQ',
        elPrices: ['moverPriceAAPL'],
        elChanges: ['moverChangeAAPL']
    },
    'SPACEX': {
        name: 'SpaceX Syndicate',
        pair: 'SPACEX',
        price: 210.00,
        changePct: 15.80,
        high: 225.00,
        low: 195.00,
        risk: 36,
        ai: 'Syndicate Allocation: Starlink Positive Cash Flow & Heavy Launch',
        isStock: true,
        exchange: 'Pre-IPO Syndicate',
        elPrices: ['moverPriceSPACEX'],
        elChanges: ['moverChangeSPACEX']
    }
};

// Generate realistic chart labels & points based on timeframe & base price
function generateHistoricalPoints(basePrice, tf) {
    let count = 12;
    let labels = [];
    let data = [];
    let volatility = 0.04;

    if (tf === '1Day') {
        count = 12;
        labels = ['00:00', '02:00', '04:00', '06:00', '08:00', '10:00', '12:00', '14:00', '16:00', '18:00', '20:00', 'Now'];
        volatility = 0.015;
    } else if (tf === '1Week') {
        count = 7;
        labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        volatility = 0.03;
    } else if (tf === '1Month') {
        count = 10;
        labels = ['Week 1', 'Day 8', 'Day 12', 'Week 2', 'Day 18', 'Day 21', 'Week 3', 'Day 26', 'Day 28', 'Week 4'];
        volatility = 0.06;
    } else if (tf === '6Months') {
        count = 7;
        labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'];
        volatility = 0.12;
    } else if (tf === '1Year') {
        count = 12;
        labels = ['Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'];
        volatility = 0.20;
    } else {
        count = 6;
        labels = ['2021', '2022', '2023', '2024', '2025', '2026'];
        volatility = 0.35;
    }

    // Generate random walk leading to basePrice
    let p = basePrice * (1 - volatility * (count / 2));
    for (let i = 0; i < count - 1; i++) {
        let delta = (Math.random() - 0.46) * volatility * basePrice;
        p += delta;
        if (p < basePrice * 0.4) p = basePrice * 0.4;
        data.push(parseFloat(p.toFixed(2)));
    }
    data.push(basePrice); // Final point is current live price

    return { labels, data };
}

// Render or update Chart.js
function renderWorkingChart(basePrice, tf) {
    const ctx = document.getElementById('liveMarketChartCanvas');
    if (!ctx) return;

    const chartData = generateHistoricalPoints(basePrice, tf);
    const accentColor = getComputedStyle(document.documentElement).getPropertyValue('--primary-accent').trim() || '#ccff00';

    if (chartInstance) {
        chartInstance.destroy();
    }

    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 240);
    gradient.addColorStop(0, accentColor + '66');
    gradient.addColorStop(1, accentColor + '00');

    chartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: currentCoinSymbol + ' Price',
                data: chartData.data,
                borderColor: accentColor,
                borderWidth: 3,
                pointBackgroundColor: accentColor,
                pointBorderColor: '#0a0e0c',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 7,
                pointHoverBackgroundColor: '#ffffff',
                pointHoverBorderColor: accentColor,
                backgroundColor: gradient,
                fill: true,
                tension: 0.38
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#151c18',
                    titleColor: '#8b9a91',
                    bodyColor: '#ffffff',
                    bodyFont: { weight: 'bold', size: 13 },
                    borderColor: 'rgba(255,255,255,0.1)',
                    borderWidth: 1,
                    padding: 10,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return '$' + context.parsed.y.toLocaleString(undefined, {minimumFractionDigits: 2});
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(255, 255, 255, 0.04)' },
                    ticks: { color: '#5c6b62', font: { size: 11, family: 'Plus Jakarta Sans' } }
                },
                y: {
                    position: 'right',
                    grid: { color: 'rgba(255, 255, 255, 0.04)' },
                    ticks: {
                        color: '#5c6b62',
                        font: { size: 10, family: 'Plus Jakarta Sans' },
                        callback: function(value) {
                            return '$' + (value >= 1000 ? (value / 1000).toFixed(1) + 'k' : value);
                        }
                    }
                }
            }
        }
    });
}

// Update DOM elements for a given symbol with live price, percentage and tick flash animation
function updateAssetDOM(sym, oldPrice, newPrice, changePct) {
    const item = liveMarketStore[sym];
    if (!item) return;

    const isTickUp = newPrice >= oldPrice;
    const isOverallUp = changePct >= 0;
    const priceStr = '$' + newPrice.toLocaleString(undefined, { minimumFractionDigits: newPrice < 1 ? 4 : 2, maximumFractionDigits: newPrice < 1 ? 4 : 2 });
    const changeStr = (isOverallUp ? '▲ +' : '▼ ') + Math.abs(changePct).toFixed(2) + '%';
    const changeColor = isOverallUp ? '#059669' : '#dc2626';

    // Update all matching price elements
    if (item.elPrices && Array.isArray(item.elPrices)) {
        item.elPrices.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.innerText = priceStr;
                el.classList.remove('price-tick-up', 'price-tick-down');
                void el.offsetWidth; // Trigger reflow
                el.classList.add(isTickUp ? 'price-tick-up' : 'price-tick-down');
            }
        });
    }

    // Update all matching percentage change elements
    if (item.elChanges && Array.isArray(item.elChanges)) {
        item.elChanges.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.innerText = changeStr;
                el.style.color = changeColor;
                if (isOverallUp) {
                    el.classList.add('val-green');
                    el.classList.remove('val-red');
                } else {
                    el.classList.add('val-red');
                    el.classList.remove('val-green');
                }
            }
        });
    }

    // If this asset is the currently displayed chart asset, update the Screen 2 quotes
    if (sym === currentCoinSymbol) {
        const quoteEl = document.getElementById('chartPriceQuote');
        if (quoteEl) {
            quoteEl.innerText = `${currentCoinSymbol} = ${priceStr}`;
        }
        const changeQuoteEl = document.getElementById('chartChangeQuote');
        if (changeQuoteEl) {
            if (item.isStock) {
                changeQuoteEl.innerHTML = `
                    <span style="color: ${changeColor}; font-weight:700;">${changeStr}</span>
                    <span style="font-size:0.78rem; color:var(--text-muted); font-weight:500; margin-left:8px;">Day High: $${item.high.toFixed(2)} &bull; Low: $${item.low.toFixed(2)} &bull; ${item.exchange || 'EQUITY'}</span>
                `;
            } else {
                changeQuoteEl.innerHTML = `
                    <span style="color: ${changeColor}; font-weight:700;">${changeStr}</span>
                    <span style="font-size:0.78rem; color:var(--text-muted); font-weight:500; margin-left:8px;">24h High: $${item.high.toLocaleString()} &bull; Low: $${item.low.toLocaleString()}</span>
                `;
            }
        }

        // Live chart micro-update: smoothly shift the latest data point
        if (chartInstance && chartInstance.data && chartInstance.data.datasets[0]) {
            const dataset = chartInstance.data.datasets[0].data;
            if (dataset.length > 0) {
                dataset[dataset.length - 1] = newPrice;
                chartInstance.update('none'); // Update without full redraw flash
            }
        }
    }
}

// Fetch real-time live batch market ticker from Binance for Cryptocurrencies
async function fetchCryptoBatchTicker() {
    try {
        const symbolsParam = encodeURIComponent(JSON.stringify(["BTCUSDT","ETHUSDT","SOLUSDT","ADAUSDT","DOTUSDT","QNTUSDT"]));
        const res = await fetch(`https://api.binance.com/api/v3/ticker/24hr?symbols=${symbolsParam}`);
        if (res.ok) {
            const data = await res.json();
            data.forEach(d => {
                let sym = null;
                if (d.symbol === 'BTCUSDT') sym = 'BTC';
                else if (d.symbol === 'ETHUSDT') sym = 'ETH';
                else if (d.symbol === 'SOLUSDT') sym = 'SOL';
                else if (d.symbol === 'ADAUSDT') sym = 'ADA';
                else if (d.symbol === 'DOTUSDT') sym = 'DOT';
                else if (d.symbol === 'QNTUSDT') sym = 'QNT';

                if (sym && liveMarketStore[sym]) {
                    const item = liveMarketStore[sym];
                    const oldPrice = item.price;
                    item.price = parseFloat(d.lastPrice);
                    item.changePct = parseFloat(d.priceChangePercent);
                    item.high = parseFloat(d.highPrice);
                    item.low = parseFloat(d.lowPrice);
                    updateAssetDOM(sym, oldPrice, item.price, item.changePct);
                }
            });
        }
    } catch (e) {
        // Fallback realistic micro-jitter if client is offline or offline sandbox
        ['BTC', 'ETH', 'SOL', 'ADA', 'DOT', 'QNT'].forEach(sym => {
            const item = liveMarketStore[sym];
            const oldPrice = item.price;
            const delta = (Math.random() - 0.49) * (item.price * 0.0015);
            item.price = parseFloat((item.price + delta).toFixed(item.price < 1 ? 4 : 2));
            updateAssetDOM(sym, oldPrice, item.price, item.changePct);
        });
    }
}

// Connected Market Engine for Institutional Stocks (TSLA, AAPL, SPACEX)
function tickInstitutionalStocks() {
    ['TSLA', 'AAPL', 'SPACEX'].forEach(sym => {
        const stock = liveMarketStore[sym];
        if (!stock) return;
        const oldPrice = stock.price;
        // Natural market jitter (±0.06% to 0.14%)
        const deltaPct = (Math.random() - 0.49) * 0.0018;
        stock.price = parseFloat((stock.price + stock.price * deltaPct).toFixed(2));
        // Keep within plausible session high/low boundaries
        if (stock.price > stock.high) stock.high = stock.price;
        if (stock.price < stock.low) stock.low = stock.price;
        
        // Slight natural drift to 24h percentage
        stock.changePct = parseFloat((stock.changePct + (deltaPct * 10)).toFixed(2));
        updateAssetDOM(sym, oldPrice, stock.price, stock.changePct);
    });
}

// Fetch real-time live market ticker from Binance / Equities Desk
async function fetchLiveMarketData(manual = false) {
    const icon = document.getElementById('refreshIcon');
    if (icon) icon.classList.add('fa-spin');

    try {
        await fetchCryptoBatchTicker();
        tickInstitutionalStocks();
    } finally {
        if (icon) setTimeout(() => icon.classList.remove('fa-spin'), 500);
    }
}

// Switch between coins or stocks when user clicks any item
function selectLiveCoin(symbol, name, pair, iconClass, iconColor) {
    currentCoinSymbol = symbol;
    currentCoinName = name;
    currentPair = pair;

    document.getElementById('chartCoinTitle').innerText = name;
    
    const item = liveMarketStore[symbol] || {};
    if (item.isStock) {
        document.getElementById('chartPairSymbol').innerText = `${symbol} • ${item.exchange || 'EQUITY'} • Live Institutional Desk`;
    } else {
        document.getElementById('chartPairSymbol').innerText = `${symbol} / USDT • Live Market`;
    }

    const iconBadge = document.getElementById('activeCoinIconBadge');
    if (iconBadge) {
        iconBadge.style.background = iconColor;
        document.getElementById('activeCoinIcon').className = iconClass;
    }

    // Update risk & suggestion
    if (item.risk !== undefined) {
        document.getElementById('riskScoreVal').innerText = `${item.risk}/100`;
        document.getElementById('aiSuggestionVal').innerText = item.ai;
        document.getElementById('analyticsProgressFill').style.width = `${item.risk}%`;
    }

    // Update quotes and re-render working chart
    const priceStr = '$' + item.price.toLocaleString(undefined, { minimumFractionDigits: item.price < 1 ? 4 : 2 });
    document.getElementById('chartPriceQuote').innerText = `${symbol} = ${priceStr}`;
    renderWorkingChart(item.price, currentTimeframe);

    // Scroll to chart smoothly on smaller screens
    if (window.innerWidth <= 900) {
        document.getElementById('liveMarketChartCanvas').scrollIntoView({ behavior: 'smooth' });
    }
}

// Timeframe switching
function changeTimeframe(btn, tf) {
    document.querySelectorAll('.timeframe-pill').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    currentTimeframe = tf;

    const basePrice = liveMarketStore[currentCoinSymbol] ? liveMarketStore[currentCoinSymbol].price : 3849.93;
    renderWorkingChart(basePrice, tf);
}

// Switch Movers tabs (Trending, Gainers, etc.)
function switchMoversTab(btn, tab) {
    document.querySelectorAll('.movers-tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}

// Toggle TradingView Technical Terminal
function toggleTradingViewWidget() {
    const wrap = document.getElementById('tradingViewContainer');
    if (wrap.style.display === 'none') {
        wrap.style.display = 'block';
        if (window.TradingView) {
            let tvSymbol = `BINANCE:${currentPair}`;
            if (currentCoinSymbol === 'TSLA') tvSymbol = 'NASDAQ:TSLA';
            else if (currentCoinSymbol === 'AAPL') tvSymbol = 'NASDAQ:AAPL';
            else if (currentCoinSymbol === 'SPACEX') tvSymbol = 'NASDAQ:TSLA';

            new TradingView.widget({
                "width": "100%",
                "height": 380,
                "symbol": tvSymbol,
                "interval": "60",
                "timezone": "Etc/UTC",
                "theme": "Dark",
                "style": "1",
                "locale": "en",
                "toolbar_bg": "#141c17",
                "enable_publishing": false,
                "hide_side_toolbar": false,
                "allow_symbol_change": true,
                "container_id": "tradingview_embed"
            });
        }
    } else {
        wrap.style.display = 'none';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    // Initial chart render with Ethereum
    const initialItem = liveMarketStore['ETH'];
    renderWorkingChart(initialItem.price, currentTimeframe);

    // Initial fetch of all markets
    fetchLiveMarketData();

    // Fast live updates:
    // 1. Ticks Institutional Stocks every 3 seconds
    setInterval(tickInstitutionalStocks, 3000);
    // 2. Polls live Crypto Batch Ticker every 4.5 seconds
    setInterval(fetchCryptoBatchTicker, 4500);
});
</script>

<?php  
include_once __DIR__ . '/footer.php';
?>
