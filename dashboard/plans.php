<?php 
include_once __DIR__ . '/header.php';

$msg = "";
$err = "";

// Handle user choosing / subscribing to a plan
if (isset($_POST['choose_plan'])) {
    $plan_name = mysqli_real_escape_string($link, trim($_POST['plan_name']));
    $plan_min  = floatval($_POST['plan_min']);
    $inv_amt   = floatval($_POST['invest_amount']);

    if ($inv_amt < $plan_min) {
        $err = "Minimum investment for $plan_name is $" . number_format($plan_min, 2);
    } elseif ($inv_amt > $mbalance) {
        $err = "Insufficient balance ($" . number_format($mbalance, 2) . "). Please make a deposit first to activate this plan.";
    } else {
        // Deduct from mbalance and add to active deposit balance (dbalance)
        mysqli_query($link, "UPDATE users SET mbalance = mbalance - $inv_amt, dbalance = dbalance + $inv_amt WHERE email = '$uemail'");
        
        // Log in deposits table
        $date = date('Y-m-d H:i:s');
        mysqli_query($link, "INSERT INTO deposits (email, amount, date, status, crypto) VALUES ('$uemail', '$inv_amt', '$date', 'COMPLETED', '$plan_name Plan')");

        // Refresh session balances
        $mbalance -= $inv_amt;
        $dbalance += $inv_amt;
        $total_balance = (float)$mbalance + (float)$dbalance;

        $msg = "Congratulations! You have successfully invested $" . number_format($inv_amt, 2) . " into the $plan_name Plan.";
    }
}
?>

<div style="max-width: 1200px; margin: 0 auto;">

    <!-- Page Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 14px;">
        <div>
            <h2 style="font-weight: 800; font-size: 1.8rem; color: #fff; margin-bottom: 4px;">Investment Plans & Packages</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0;">Choose from our verified institutional investment strategies designed for sustainable crypto yields.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="deposit.php" class="btn-pill-buy" style="padding: 10px 22px; font-size: 0.88rem; text-decoration: none;">
                <i class="fa-solid fa-plus me-1"></i> Deposit Funds
            </a>
            <a href="customer.php" class="icon-btn-round" title="Dashboard">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
        </div>
    </div>

    <?php if ($msg != "") { ?>
    <div style="background: rgba(46, 204, 113, 0.15); border: 1px solid var(--color-green); color: var(--color-green); padding: 14px 18px; border-radius: 16px; margin-bottom: 24px; font-weight: 600;">
        <i class="fa-solid fa-circle-check me-2"></i> <?php echo $msg; ?>
    </div>
    <?php } ?>

    <?php if ($err != "") { ?>
    <div style="background: rgba(255, 82, 82, 0.15); border: 1px solid var(--color-red); color: var(--color-red); padding: 14px 18px; border-radius: 16px; margin-bottom: 24px; font-weight: 600;">
        <i class="fa-solid fa-triangle-exclamation me-2"></i> <?php echo $err; ?>
        <?php if (strpos($err, "make a deposit") !== false) { ?>
            <a href="deposit.php" style="color: var(--primary-accent); margin-left: 10px; text-decoration: underline;">Make a Deposit Now</a>
        <?php } ?>
    </div>
    <?php } ?>

    <!-- User Balance Summary with Luminous Brushed Platinum Light Background -->
    <div style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border: 1px solid rgba(226, 232, 240, 0.95); border-radius: 20px; padding: 20px 24px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; box-shadow: 0 6px 20px rgba(0,0,0,0.08);">
        <div style="display: flex; gap: 28px;">
            <div>
                <span style="font-size: 0.8rem; color: #64748b; font-weight: 600; display: block; margin-bottom: 2px;">Withdrawable Balance</span>
                <span style="font-size: 1.45rem; font-weight: 800; color: #059669;">$<?php echo number_format((float)$mbalance, 2); ?></span>
            </div>
            <div>
                <span style="font-size: 0.8rem; color: #64748b; font-weight: 600; display: block; margin-bottom: 2px;">Active Deposits</span>
                <span style="font-size: 1.45rem; font-weight: 800; color: #090d16;">$<?php echo number_format((float)$dbalance, 2); ?></span>
            </div>
        </div>
        <div style="font-size: 0.85rem; color: #475569; font-weight: 600; background: #f1f5f9; padding: 8px 16px; border-radius: 20px; border: 1px solid #e2e8f0;">
            <i class="fa-solid fa-shield-halved me-1" style="color: var(--primary-accent);"></i> Capital Protected &bull; Compounded Daily
        </div>
    </div>

    <!-- Dynamic Investment Plans Grid from Database -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 36px;">
        <?php
        $p_query = mysqli_query($link, "SELECT * FROM plans ORDER BY min_deposit ASC");
        $calc_options = [];
        if ($p_query && mysqli_num_rows($p_query) > 0) {
            while ($p = mysqli_fetch_assoc($p_query)) {
                $p_id   = $p['id'];
                $p_name = htmlspecialchars($p['name']);
                $p_badge = htmlspecialchars($p['badge'] ?? 'STANDARD');
                $p_min  = (float)$p['min_deposit'];
                $p_max  = (float)$p['max_deposit'];
                $p_roi  = (float)$p['roi'];
                $p_dur  = htmlspecialchars($p['duration']);
                $p_desc = htmlspecialchars($p['description'] ?? '');
                $p_icon = !empty($p['icon']) ? htmlspecialchars($p['icon']) : 'fa-solid fa-chart-line';
                $p_feat = ($p['is_featured'] == 1);
                
                $calc_options[] = [
                    'val' => $p_roi . '|' . $p_min,
                    'label' => $p_name . ' (' . $p_roi . '% - Min $' . number_format($p_min) . ')'
                ];
                
                $border_style = $p_feat ? 'border: 2px solid var(--primary-accent); box-shadow: 0 10px 30px var(--primary-accent-glow); position: relative;' : 'border: 1px solid var(--card-border);';
                $btn_style = $p_feat ? 'background: var(--primary-accent); color: #000;' : 'background: #1f2c24; color: #fff; border: 1px solid var(--card-border);';
                
                $features_arr = array_filter(array_map('trim', explode("\n", $p['features'] ?? '')));
        ?>
        <div style="background: #141c17; <?php echo $border_style; ?> border-radius: 26px; padding: 26px 22px; display: flex; flex-direction: column; justify-content: space-between; transition: all 0.25s ease;">
            <?php if ($p_feat) { ?>
            <div style="position: absolute; top: -12px; right: 24px; background: var(--primary-accent); color: #000; font-weight: 800; font-size: 0.72rem; padding: 3px 12px; border-radius: 20px;">
                MOST POPULAR
            </div>
            <?php } ?>
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <span style="background: <?php echo $p_feat ? 'rgba(204,255,0,0.15)' : 'rgba(255,255,255,0.06)'; ?>; color: <?php echo $p_feat ? 'var(--primary-accent)' : '#fff'; ?>; font-size: 0.75rem; font-weight: 700; padding: 4px 10px; border-radius: 20px;">
                        <?php echo $p_badge; ?>
                    </span>
                    <i class="<?php echo $p_icon; ?>" style="color: var(--primary-accent); font-size: 1.2rem;"></i>
                </div>
                <h3 style="font-weight: 800; color: #fff; margin-bottom: 6px; font-size: 1.2rem;"><?php echo $p_name; ?></h3>
                <div style="font-size: 2.2rem; font-weight: 800; color: var(--primary-accent); margin-bottom: 4px;">
                    <?php echo $p_roi; ?>% <span style="font-size: 0.9rem; color: var(--text-muted); font-weight: 600;">/ Weekly</span>
                </div>
                <p style="font-size: 0.82rem; color: var(--text-muted); margin-bottom: 20px; min-height: 40px;"><?php echo $p_desc; ?></p>

                <ul style="list-style: none; padding: 0; margin-bottom: 24px; font-size: 0.85rem; color: #d2ded7; display: flex; flex-direction: column; gap: 10px;">
                    <li style="display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-check" style="color: var(--primary-accent);"></i> Min Deposit: <strong>$<?php echo number_format($p_min); ?></strong>
                    </li>
                    <li style="display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-check" style="color: var(--primary-accent);"></i> Max Deposit: <strong><?php echo ($p_max >= 1000000) ? 'Unlimited' : '$' . number_format($p_max); ?></strong>
                    </li>
                    <li style="display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-check" style="color: var(--primary-accent);"></i> Duration: <strong><?php echo $p_dur; ?></strong>
                    </li>
                    <?php 
                    $cf = 0;
                    foreach ($features_arr as $feat) {
                        if (stripos($feat, 'minimum deposit') !== false || stripos($feat, 'duration') !== false) continue;
                        if ($cf++ >= 2) break;
                    ?>
                    <li style="display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-check" style="color: var(--primary-accent);"></i> <?php echo htmlspecialchars($feat); ?>
                    </li>
                    <?php } ?>
                </ul>
            </div>

            <button type="button" onclick="openInvestModal('<?php echo addslashes($p_name); ?>', <?php echo $p_min; ?>, <?php echo $p_max; ?>, <?php echo $p_roi; ?>)" style="width: 100%; <?php echo $btn_style; ?> font-weight: 800; padding: 12px; border-radius: 30px; cursor: pointer; transition: all 0.2s ease;">
                Choose <?php echo $p_name; ?>
            </button>
        </div>
        <?php 
            }
        } 
        ?>
    </div>

    <!-- Interactive Investment Profit Calculator with Light Brushed Card Aesthetic -->
    <div style="background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%); border: 1px solid rgba(226, 232, 240, 0.95); border-radius: 28px; padding: 32px; margin-bottom: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.12);">
        <h4 style="font-weight: 800; color: #090d16; margin-bottom: 6px; font-size: 1.25rem;">
            <i class="fa-solid fa-calculator me-2" style="color: var(--primary-accent);"></i> Interactive Profit Calculator
        </h4>
        <p style="color: #64748b; font-size: 0.88rem; margin-bottom: 24px;">Estimate your guaranteed weekly return based on your planned capital allocation.</p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; align-items: center;">
            <div>
                <label style="font-size: 0.82rem; color: #334155; font-weight: 700; margin-bottom: 8px; display: block;">Select Investment Strategy</label>
                <select id="calcPlanSelect" onchange="calculateProfit()" style="width: 100%; background: #ffffff; border: 1px solid #cbd5e1; color: #090d16; padding: 12px 16px; border-radius: 14px; outline: none; margin-bottom: 16px; font-weight: 600;">
                    <?php 
                    if (!empty($calc_options)) {
                        foreach ($calc_options as $idx => $opt) { 
                    ?>
                        <option value="<?php echo htmlspecialchars($opt['val']); ?>" <?php echo ($idx === 1 || ($idx === 0 && count($calc_options) === 1)) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($opt['label']); ?>
                        </option>
                    <?php 
                        }
                    } else {
                        echo '<option value="15|5000">Standard Plan (15% Weekly - Min $5,000)</option>';
                    }
                    ?>
                </select>

                <label style="font-size: 0.82rem; color: #334155; font-weight: 700; margin-bottom: 8px; display: block;">Deposit Capital ($ USD)</label>
                <input type="number" id="calcAmountInput" value="5000" min="1000" step="500" oninput="calculateProfit()" style="width: 100%; background: #ffffff; border: 1px solid #cbd5e1; color: #090d16; padding: 12px 16px; border-radius: 14px; outline: none; font-size: 1.1rem; font-weight: 800;">
            </div>

            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 20px; padding: 24px; display: grid; grid-template-columns: 1fr 1fr; gap: 16px; text-align: center; box-shadow: 0 4px 14px rgba(0,0,0,0.05);">
                <div>
                    <div style="font-size: 0.78rem; color: #64748b; font-weight: 600; margin-bottom: 4px;">Weekly Profit</div>
                    <div style="font-size: 1.6rem; font-weight: 800; color: #059669;" id="calcWeeklyProfit">$750.00</div>
                </div>
                <div>
                    <div style="font-size: 0.78rem; color: #64748b; font-weight: 600; margin-bottom: 4px;">Monthly Return</div>
                    <div style="font-size: 1.6rem; font-weight: 800; color: #090d16;" id="calcMonthlyProfit">$3,000.00</div>
                </div>
                <div style="grid-column: span 2; border-top: 1px solid #e2e8f0; padding-top: 14px;">
                    <span style="font-size: 0.82rem; color: #64748b; font-weight: 600;">Total Expected Payoff: </span>
                    <strong style="color: #090d16; font-size: 1.15rem; font-weight: 800;" id="calcTotalReturn">$8,000.00</strong>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Modal Dialog to Invest -->
<div id="investModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.85); z-index: 9999; align-items: center; justify-content: center; padding: 16px;">
    <div style="background: #151c18; border: 1px solid var(--card-border); border-radius: 28px; padding: 28px; max-width: 440px; width: 100%; color: #fff;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h4 style="margin: 0; font-weight: 800;" id="modalPlanTitle">Activate Plan</h4>
            <button onclick="document.getElementById('investModal').style.display='none'" style="background: none; border: none; color: #fff; font-size: 1.3rem; cursor: pointer;">&times;</button>
        </div>

        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 18px;">Allocate funds from your withdrawable balance to activate this automated plan.</p>

        <form action="plans.php" method="POST">
            <input type="hidden" name="plan_name" id="modalPlanNameInput" value="">
            <input type="hidden" name="plan_min" id="modalPlanMinInput" value="">

            <div style="margin-bottom: 16px;">
                <label style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 6px; display: block;">Investment Amount ($ USD)</label>
                <input type="number" step="0.01" name="invest_amount" id="modalInvestAmount" required style="width: 100%; background: #0c120f; border: 1px solid var(--card-border); color: #fff; padding: 12px 16px; border-radius: 14px; outline: none; font-size: 1.1rem; font-weight: 700;">
                <small style="color: var(--text-muted); display: block; margin-top: 4px;" id="modalMinHint">Minimum: $1,000</small>
            </div>

            <div style="background: #0f1612; border-radius: 14px; padding: 12px 16px; margin-bottom: 20px; font-size: 0.82rem; display: flex; justify-content: space-between;">
                <span style="color: var(--text-muted);">Available Balance:</span>
                <strong style="color: var(--primary-accent);">$<?php echo number_format($mbalance, 2); ?></strong>
            </div>

            <button type="submit" name="choose_plan" style="width: 100%; background: var(--primary-accent); color: #000; font-weight: 800; border: none; padding: 14px; border-radius: 30px; cursor: pointer;">
                Confirm & Activate Plan
            </button>
        </form>
    </div>
</div>

<script>
function openInvestModal(name, min, max, pct) {
    document.getElementById('modalPlanTitle').innerText = 'Activate ' + name;
    document.getElementById('modalPlanNameInput').value = name;
    document.getElementById('modalPlanMinInput').value = min;
    document.getElementById('modalInvestAmount').value = min;
    document.getElementById('modalInvestAmount').min = min;
    document.getElementById('modalMinHint').innerText = 'Minimum required: $' + min.toLocaleString();
    document.getElementById('investModal').style.display = 'flex';
}

function calculateProfit() {
    const val = document.getElementById('calcPlanSelect').value.split('|');
    const rate = parseFloat(val[0]);
    const min = parseFloat(val[1]);
    let amt = parseFloat(document.getElementById('calcAmountInput').value) || 0;
    if (amt < min) amt = min;

    const weekly = (amt * rate) / 100;
    const monthly = weekly * 4;
    const total = amt + monthly;

    document.getElementById('calcWeeklyProfit').innerText = '$' + weekly.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('calcMonthlyProfit').innerText = '$' + monthly.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('calcTotalReturn').innerText = '$' + total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
}
calculateProfit();
</script>

<?php 
include_once __DIR__ . '/footer.php';
?>
