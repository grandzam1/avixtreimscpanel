<?php 
include_once __DIR__ . '/header.php';
?>

<div style="max-width: 1100px; margin: 0 auto;">

    <!-- Page Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 14px;">
        <div>
            <h2 style="font-weight: 800; font-size: 1.8rem; color: #fff; margin-bottom: 4px;">Dashboard Navigation & Service Hub</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0;">Quick access to all core features, original investment plans, banking tools, and market interfaces.</p>
        </div>
        <a href="customer.php" class="icon-btn-round" title="Back to Dashboard">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
    </div>

    <!-- Navigation Hub Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; margin-bottom: 36px;">

        <!-- 1. Portfolio & Overview -->
        <a href="customer.php" style="background: #141c17; border: 1px solid var(--card-border); border-radius: 24px; padding: 24px; text-decoration: none; color: inherit; display: flex; gap: 18px; align-items: flex-start; transition: all 0.25s ease;" onmouseover="this.style.borderColor='var(--primary-accent)'" onmouseout="this.style.borderColor='var(--card-border)'">
            <div style="width: 48px; height: 48px; border-radius: 16px; background: rgba(204,255,0,0.12); color: var(--primary-accent); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                <i class="fa-solid fa-chart-pie"></i>
            </div>
            <div>
                <h4 style="font-weight: 800; color: #fff; margin-bottom: 6px;">Account Dashboard</h4>
                <p style="font-size: 0.84rem; color: var(--text-muted); margin: 0; line-height: 1.4;">Real-time portfolio balance ($<?php echo number_format($total_balance, 2); ?>), 24h market movers, and live interactive chart.</p>
            </div>
        </a>

        <!-- 2. Investment Plans -->
        <a href="plans.php" style="background: #141c17; border: 1px solid var(--card-border); border-radius: 24px; padding: 24px; text-decoration: none; color: inherit; display: flex; gap: 18px; align-items: flex-start; transition: all 0.25s ease;" onmouseover="this.style.borderColor='var(--primary-accent)'" onmouseout="this.style.borderColor='var(--card-border)'">
            <div style="width: 48px; height: 48px; border-radius: 16px; background: rgba(241,196,15,0.15); color: #f1c40f; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <div>
                <h4 style="font-weight: 800; color: #fff; margin-bottom: 6px;">Investment Plans & Packages</h4>
                <p style="font-size: 0.84rem; color: var(--text-muted); margin: 0; line-height: 1.4;">Starter (10%), Standard (15%), Premium (25%), and Deluxe (30%) yield packages.</p>
            </div>
        </a>

        <!-- 2b. Stock Shares Trading Desk (Technology, Silicon, Aerospace Indices) -->
        <a href="shares.php" style="background: #141c17; border: 1px solid rgba(204,255,0,0.3); border-radius: 24px; padding: 24px; text-decoration: none; color: inherit; display: flex; gap: 18px; align-items: flex-start; transition: all 0.25s ease;" onmouseover="this.style.borderColor='var(--primary-accent)'" onmouseout="this.style.borderColor='rgba(204,255,0,0.3)'">
            <div style="width: 48px; height: 48px; border-radius: 16px; background: rgba(204,255,0,0.15); color: var(--primary-accent); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                <i class="fa-solid fa-arrow-trend-up"></i>
            </div>
            <div>
                <h4 style="font-weight: 800; color: #fff; margin-bottom: 6px;">Stock Shares Desk <span style="font-size:0.68rem; background:rgba(204,255,0,0.15); color:var(--primary-accent); padding:2px 8px; border-radius:8px; font-weight:800; margin-left:6px;">NEW</span></h4>
                <p style="font-size: 0.84rem; color: var(--text-muted); margin: 0; line-height: 1.4;">Buy institutional equity shares in Technology (TSLA), Silicon (AAPL), and Aerospace (SPACEX) indices with live pricing.</p>
            </div>
        </a>

        <!-- 2c. Account Profile & Settings -->
        <a href="settings.php" style="background: #141c17; border: 1px solid var(--card-border); border-radius: 24px; padding: 24px; text-decoration: none; color: inherit; display: flex; gap: 18px; align-items: flex-start; transition: all 0.25s ease;" onmouseover="this.style.borderColor='var(--primary-accent)'" onmouseout="this.style.borderColor='var(--card-border)'">
            <div style="width: 48px; height: 48px; border-radius: 16px; background: rgba(0,229,255,0.12); color: #00e5ff; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                <i class="fa-solid fa-user-gear"></i>
            </div>
            <div>
                <h4 style="font-weight: 800; color: #fff; margin-bottom: 6px;">Profile & Display Picture</h4>
                <p style="font-size: 0.84rem; color: var(--text-muted); margin: 0; line-height: 1.4;">Upload your personal display photo, update contact credentials, and view account security status.</p>
            </div>
        </a>

        <!-- 2d. KYC Identity Verification -->
        <a href="kyc.php" style="background: #141c17; border: 1px solid <?php echo ($verification == '1') ? 'rgba(46,204,113,0.3)' : 'rgba(241,196,15,0.4)'; ?>; border-radius: 24px; padding: 24px; text-decoration: none; color: inherit; display: flex; gap: 18px; align-items: flex-start; transition: all 0.25s ease;">
            <div style="width: 48px; height: 48px; border-radius: 16px; background: <?php echo ($verification == '1') ? 'rgba(46,204,113,0.15)' : 'rgba(241,196,15,0.15)'; ?>; color: <?php echo ($verification == '1') ? '#2ecc71' : '#f1c40f'; ?>; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                <i class="fa-solid fa-id-card"></i>
            </div>
            <div>
                <h4 style="font-weight: 800; color: #fff; margin-bottom: 6px;">KYC Identity Verification 
                    <?php if ($verification == '1') { ?>
                        <span style="font-size:0.68rem; background:rgba(46,204,113,0.15); color:#2ecc71; padding:2px 8px; border-radius:8px; font-weight:800; margin-left:6px;">VERIFIED</span>
                    <?php } else { ?>
                        <span style="font-size:0.68rem; background:rgba(241,196,15,0.15); color:#f1c40f; padding:2px 8px; border-radius:8px; font-weight:800; margin-left:6px;">ACTION REQUIRED</span>
                    <?php } ?>
                </h4>
                <p style="font-size: 0.84rem; color: var(--text-muted); margin: 0; line-height: 1.4;">Submit your government-issued ID (Passport, National ID, Driver's License) for institutional withdrawal clearance.</p>
            </div>
        </a>

        <!-- 3. Deposit Gateway -->
        <a href="deposit.php" style="background: #141c17; border: 1px solid var(--card-border); border-radius: 24px; padding: 24px; text-decoration: none; color: inherit; display: flex; gap: 18px; align-items: flex-start; transition: all 0.25s ease;" onmouseover="this.style.borderColor='var(--primary-accent)'" onmouseout="this.style.borderColor='var(--card-border)'">
            <div style="width: 48px; height: 48px; border-radius: 16px; background: rgba(46,204,113,0.15); color: #2ecc71; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                <i class="fa-solid fa-arrow-down-long"></i>
            </div>
            <div>
                <h4 style="font-weight: 800; color: #fff; margin-bottom: 6px;">Make a Deposit</h4>
                <p style="font-size: 0.84rem; color: var(--text-muted); margin: 0; line-height: 1.4;">Official admin Bitcoin, Ethereum, and USDT deposit addresses with live QR codes.</p>
            </div>
        </a>

        <!-- 4. Deposit History -->
        <a href="deposit_history.php" style="background: #141c17; border: 1px solid var(--card-border); border-radius: 24px; padding: 24px; text-decoration: none; color: inherit; display: flex; gap: 18px; align-items: flex-start; transition: all 0.25s ease;" onmouseover="this.style.borderColor='var(--primary-accent)'" onmouseout="this.style.borderColor='var(--card-border)'">
            <div style="width: 48px; height: 48px; border-radius: 16px; background: rgba(52,152,219,0.15); color: #3498db; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div>
                <h4 style="font-weight: 800; color: #fff; margin-bottom: 6px;">Deposit History</h4>
                <p style="font-size: 0.84rem; color: var(--text-muted); margin: 0; line-height: 1.4;">Track all your pending approvals, approved deposits, and total transaction amounts.</p>
            </div>
        </a>

        <!-- 5. Withdraw Funds -->
        <a href="withdraw.php" style="background: #141c17; border: 1px solid var(--card-border); border-radius: 24px; padding: 24px; text-decoration: none; color: inherit; display: flex; gap: 18px; align-items: flex-start; transition: all 0.25s ease;" onmouseover="this.style.borderColor='var(--primary-accent)'" onmouseout="this.style.borderColor='var(--card-border)'">
            <div style="width: 48px; height: 48px; border-radius: 16px; background: rgba(231,76,60,0.15); color: #e74c3c; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <div>
                <h4 style="font-weight: 800; color: #fff; margin-bottom: 6px;">Withdraw Funds</h4>
                <p style="font-size: 0.84rem; color: var(--text-muted); margin: 0; line-height: 1.4;">Request settlement to your external crypto wallet or bank wire with PIN security.</p>
            </div>
        </a>

        <!-- 6. Affiliate & Referrals -->
        <a href="referals.php" style="background: #141c17; border: 1px solid var(--card-border); border-radius: 24px; padding: 24px; text-decoration: none; color: inherit; display: flex; gap: 18px; align-items: flex-start; transition: all 0.25s ease;" onmouseover="this.style.borderColor='var(--primary-accent)'" onmouseout="this.style.borderColor='var(--card-border)'">
            <div style="width: 48px; height: 48px; border-radius: 16px; background: rgba(155,89,182,0.15); color: #9b59b6; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <h4 style="font-weight: 800; color: #fff; margin-bottom: 6px;">Affiliate & Referrals</h4>
                <p style="font-size: 0.84rem; color: var(--text-muted); margin: 0; line-height: 1.4;">Your personal affiliate link, total referred users (<?php echo $refals; ?>), and commission payouts.</p>
            </div>
        </a>

        <!-- 6b. Notification Center -->
        <a href="notifications.php" style="background: #141c17; border: 1px solid var(--card-border); border-radius: 24px; padding: 24px; text-decoration: none; color: inherit; display: flex; gap: 18px; align-items: flex-start; transition: all 0.25s ease;" onmouseover="this.style.borderColor='var(--primary-accent)'" onmouseout="this.style.borderColor='var(--card-border)'">
            <div style="width: 48px; height: 48px; border-radius: 16px; background: rgba(204,255,0,0.12); color: var(--primary-accent); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                <i class="fa-regular fa-bell"></i>
            </div>
            <div>
                <h4 style="font-weight: 800; color: #fff; margin-bottom: 6px;">Notification Center</h4>
                <p style="font-size: 0.84rem; color: var(--text-muted); margin: 0; line-height: 1.4;">Access system bulletins, market notices, and direct communications from administration.</p>
            </div>
        </a>

        <!-- 7. Public Landing Page -->
        <a href="../index.php" style="background: #141c17; border: 1px solid var(--card-border); border-radius: 24px; padding: 24px; text-decoration: none; color: inherit; display: flex; gap: 18px; align-items: flex-start; transition: all 0.25s ease;" onmouseover="this.style.borderColor='var(--primary-accent)'" onmouseout="this.style.borderColor='var(--card-border)'">
            <div style="width: 48px; height: 48px; border-radius: 16px; background: rgba(255,255,255,0.08); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                <i class="fa-solid fa-globe"></i>
            </div>
            <div>
                <h4 style="font-weight: 800; color: #fff; margin-bottom: 6px;">Main Website</h4>
                <p style="font-size: 0.84rem; color: var(--text-muted); margin: 0; line-height: 1.4;">Return to the public homepage with 3D graphics, corporate roadmap, and services.</p>
            </div>
        </a>

        <!-- 8. About Company -->
        <a href="../about.php" style="background: #141c17; border: 1px solid var(--card-border); border-radius: 24px; padding: 24px; text-decoration: none; color: inherit; display: flex; gap: 18px; align-items: flex-start; transition: all 0.25s ease;" onmouseover="this.style.borderColor='var(--primary-accent)'" onmouseout="this.style.borderColor='var(--card-border)'">
            <div style="width: 48px; height: 48px; border-radius: 16px; background: rgba(255,255,255,0.08); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                <i class="fa-solid fa-building"></i>
            </div>
            <div>
                <h4 style="font-weight: 800; color: #fff; margin-bottom: 6px;">About Us & Legal</h4>
                <p style="font-size: 0.84rem; color: var(--text-muted); margin: 0; line-height: 1.4;">Company licensing, team background, security policies, and institutional audit.</p>
            </div>
        </a>

        <!-- 9. Logout -->
        <a href="logout.php" style="background: #141c17; border: 1px solid var(--card-border); border-radius: 24px; padding: 24px; text-decoration: none; color: inherit; display: flex; gap: 18px; align-items: flex-start; transition: all 0.25s ease;" onmouseover="this.style.borderColor='#ff5252'" onmouseout="this.style.borderColor='var(--card-border)'">
            <div style="width: 48px; height: 48px; border-radius: 16px; background: rgba(255,82,82,0.15); color: #ff5252; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                <i class="fa-solid fa-power-off"></i>
            </div>
            <div>
                <h4 style="font-weight: 800; color: #fff; margin-bottom: 6px;">Sign Out</h4>
                <p style="font-size: 0.84rem; color: var(--text-muted); margin: 0; line-height: 1.4;">Safely terminate your current authenticated session.</p>
            </div>
        </a>

    </div>

</div>

<?php 
include_once __DIR__ . '/footer.php';
?>
