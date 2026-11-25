<?php
include 'session.php';

$wrn = "";
$msg = "";
$mods = "";

// Handle withdrawal request before PIN step
if (isset($_POST['submit'])) {
    $method = @trim($_POST['des']);
    $amt = @trim($_POST['amt']);
    $wallett = @trim($_POST['wallet'] ?? '');
    $bname = @trim($_POST['bnk'] ?? '');
    $anum = @trim($_POST['num'] ?? '');
    $aname = @trim($_POST['name'] ?? '');

    if (!empty($method) && !empty($amt)) {
        if ($amt > $mbalance) {
            $wrn = "Please your withdrawable balance ($" . number_format($mbalance, 2) . ") is too low for this request.";
        } else {
            $_SESSION['method'] = $method;
            $_SESSION['amt'] = $amt;
            $_SESSION['wallett'] = $wallett;
            $_SESSION['bname'] = $bname;
            $_SESSION['anum'] = $anum;
            $_SESSION['aname'] = $aname;
            $mods = "show";
        }
    }
}

// Handle PIN and final check
if (isset($_POST['pin'])) {
    $pin = trim($_POST['wpin']);

    if (!empty($pin)) {
        if ($wpin != $pin) {
            $wrn = 'Please enter the correct withdrawal authorization PIN to continue with this request.';
        } else {
            // PIN is correct
            $msg = "PIN verified successfully. Proceeding...";
            echo "<meta http-equiv='refresh' content='1; url=?step=upgrade' />";
        }
    } else {
        $wrn = "Please enter your withdrawal authorization PIN.";
    }
}

// If coming from PIN success, check upgrade
if (isset($_GET['step']) && $_GET['step'] === 'upgrade') {
    if ((int)$upgrade === 2) {
        $wrn = "Upgrade your account to proceed with withdrawals.";
    } elseif ((int)$upgrade === 1) {
        $msg = "Account upgrade verified successfully. Proceeding...";
        echo "<meta http-equiv='refresh' content='1; url=?step=verification' />";
    } else {
        $wrn = "Invalid upgrade status.";
    }
}

// If coming from upgrade success, check verification
if (isset($_GET['step']) && $_GET['step'] === 'verification') {
    if (trim((string)$verification) !== "1") {
        $wrn = "Account KYC verification is required to proceed with withdrawals. <a href='kyc.php' style='color:var(--primary-accent); font-weight:700; text-decoration:underline;'>Complete KYC Verification here &rarr;</a>";
    } elseif (trim((string)$verification) === "1") {
        $method  = $_SESSION['method'];
        $amt     = $_SESSION['amt'];
        $wallett = $_SESSION['wallett'];
        $bname   = $_SESSION['bname'];
        $anum    = $_SESSION['anum'];
        $aname   = $_SESSION['aname'];

        // Insert withdrawal
        mysqli_query($link, "INSERT INTO withdrawals (email, amount, wallet, method, bank_name, acct_number, acct_name) VALUES ('$uemail', '$amt', '$wallett', '$method', '$bname', '$anum', '$aname')");

        // Deduct from user's balance
        mysqli_query($link, "UPDATE users SET mbalance = mbalance - $amt WHERE email = '$uemail'");

        // Send Email notification
        $subject = "Withdrawal Alert!";
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8\r\n";
        $headers .= "From: $sitename <$sitemail>\r\n";

        $body = '
        <div style="background: #f5f7f8; font-family: sans-serif;">
            <div style="background:#fff; max-width: 600px; margin: auto; padding: 30px;">
                <div style="float: left;"><img src="'.$siteurl.'/logo.png"></div>
                <div style="float: right"><p>Dear: '.$username.'</p></div>
                <div style="clear: both;"></div>
                <div style="background: #1976d2; padding: 20px; color: #fff;">
                    <h1>Thank you for choosing '.$sitename.'</h1>
                </div>
                <div style="padding: 20px;">
                    <p>Your withdrawal request of '.$amt.' USD via '.$method.' is under review.</p>
                </div>
                <div style="background: #1976d2; padding: 20px; color: #fff;">
                    <p>Please do not reply to this email. Emails sent to this address will not be answered.<br>Copyright '.date('Y').' '.$sitename.'.</p>
                </div>
            </div>
        </div>';

        @mail($uemail, $subject, $body, $headers);

        $msg = "Withdrawal request has been submitted.";
        echo "<meta http-equiv='refresh' content='2; url=withdraw.php' />";
    } else {
        $wrn = "Invalid verification status.";
    }
}

include_once __DIR__ . '/header.php';
?>

<div style="max-width: 850px; margin: 0 auto;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h2 style="font-weight: 800; font-size: 1.8rem; color: #fff; margin-bottom: 4px;">Withdraw Funds</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0;">Request instant settlement to your external cryptocurrency address or bank account.</p>
        </div>
        <a href="customer.php" class="icon-btn-round" title="Back to Dashboard">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
    </div>

    <!-- Balance Banner -->
    <div style="background: #141c17; border: 1px solid var(--card-border); border-radius: 24px; padding: 20px 24px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <div style="font-size: 0.8rem; color: var(--text-muted);">Available for Withdrawal</div>
            <div style="font-size: 1.8rem; font-weight: 800; color: var(--primary-accent);">$<?php echo number_format((float)$mbalance, 2); ?></div>
        </div>
        <a href="deposit.php" class="btn-pill-buy" style="padding: 10px 20px; text-decoration: none; font-size: 0.88rem;">Deposit More</a>
    </div>

    <?php if ($msg != "") { ?>
    <div style="background: rgba(46, 204, 113, 0.15); border: 1px solid var(--color-green); color: var(--color-green); padding: 14px 18px; border-radius: 16px; margin-bottom: 20px; font-weight: 600;">
        <i class="fa-solid fa-circle-check me-2"></i> <?php echo $msg; ?>
    </div>
    <?php } ?>

    <?php if ($wrn != "") { ?>
    <div style="background: rgba(255, 82, 82, 0.15); border: 1px solid var(--color-red); color: var(--color-red); padding: 14px 18px; border-radius: 16px; margin-bottom: 20px; font-weight: 600;">
        <i class="fa-solid fa-triangle-exclamation me-2"></i> <?php echo $wrn; ?>
    </div>
    <?php } ?>

    <!-- Withdrawal Method Accordion Cards -->
    <div style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 28px;">

        <!-- Option 1: Bitcoin -->
        <div style="background: #141c17; border: 1px solid var(--card-border); border-radius: 24px; padding: 22px;">
            <div style="display: flex; justify-content: space-between; align-items: center; cursor: pointer;" onclick="document.getElementById('formBtc').classList.toggle('d-none')">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div class="coin-icon-badge coin-icon-btc" style="width: 44px; height: 44px; font-size: 1.3rem;">
                        <i class="fa-brands fa-bitcoin"></i>
                    </div>
                    <div>
                        <div style="font-weight: 800; color: #fff; font-size: 1.05rem;">Bitcoin (BTC) Withdrawal</div>
                        <div style="font-size: 0.78rem; color: var(--primary-accent);">Recommended &bull; Fast Blockchain Settlement</div>
                    </div>
                </div>
                <button type="button" class="icon-btn-round"><i class="fa-solid fa-chevron-down"></i></button>
            </div>

            <div id="formBtc" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--card-border);">
                <form action="withdraw.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $id ?>">
                    <input type="hidden" name="des" value="Bitcoin Withdrawal">
                    <input type="hidden" name="email" value="<?php echo $uemail ?>">

                    <div style="margin-bottom: 14px;">
                        <label style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 6px; display: block;">Withdrawal Amount ($ USD)</label>
                        <input type="number" step="0.01" max="<?php echo $mbalance; ?>" name="amt" placeholder="e.g. 500" required style="width: 100%; background: #0c120f; border: 1px solid var(--card-border); color: #fff; padding: 12px 16px; border-radius: 14px; outline: none;">
                    </div>

                    <div style="margin-bottom: 18px;">
                        <label style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 6px; display: block;">Your Destination Bitcoin (BTC) Wallet Address</label>
                        <input type="text" name="wallet" placeholder="bc1q... or 1..." required style="width: 100%; background: #0c120f; border: 1px solid var(--card-border); color: #fff; padding: 12px 16px; border-radius: 14px; outline: none; font-family: monospace;">
                    </div>

                    <button type="submit" name="submit" style="background: var(--primary-accent); color: #000; font-weight: 800; border: none; padding: 12px 24px; border-radius: 30px; cursor: pointer;">
                        Proceed with Bitcoin Withdrawal
                    </button>
                </form>
            </div>
        </div>

        <!-- Option 2: Bank Transfer -->
        <div style="background: #141c17; border: 1px solid var(--card-border); border-radius: 24px; padding: 22px;">
            <div style="display: flex; justify-content: space-between; align-items: center; cursor: pointer;" onclick="document.getElementById('formBank').classList.toggle('d-none')">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div class="coin-icon-badge" style="width: 44px; height: 44px; font-size: 1.3rem; background: #3498db; color: #fff;">
                        <i class="fa-solid fa-building-columns"></i>
                    </div>
                    <div>
                        <div style="font-weight: 800; color: #fff; font-size: 1.05rem;">Direct Bank Wire Transfer</div>
                        <div style="font-size: 0.78rem; color: var(--text-muted);">SWIFT / SEPA / Local ACH</div>
                    </div>
                </div>
                <button type="button" class="icon-btn-round"><i class="fa-solid fa-chevron-down"></i></button>
            </div>

            <div id="formBank" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--card-border);">
                <form action="withdraw.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $id ?>">
                    <input type="hidden" name="des" value="Transfer Withdrawal">
                    <input type="hidden" name="email" value="<?php echo $uemail ?>">

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
                        <div>
                            <label style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 6px; display: block;">Account Holder Name</label>
                            <input type="text" name="name" placeholder="Full Legal Name" required style="width: 100%; background: #0c120f; border: 1px solid var(--card-border); color: #fff; padding: 12px 16px; border-radius: 14px; outline: none;">
                        </div>
                        <div>
                            <label style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 6px; display: block;">Bank Name</label>
                            <input type="text" name="bnk" placeholder="e.g. JPMorgan Chase" required style="width: 100%; background: #0c120f; border: 1px solid var(--card-border); color: #fff; padding: 12px 16px; border-radius: 14px; outline: none;">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 18px;">
                        <div>
                            <label style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 6px; display: block;">Account / IBAN Number</label>
                            <input type="text" name="num" placeholder="Account Number" required style="width: 100%; background: #0c120f; border: 1px solid var(--card-border); color: #fff; padding: 12px 16px; border-radius: 14px; outline: none;">
                        </div>
                        <div>
                            <label style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 6px; display: block;">Amount ($ USD)</label>
                            <input type="number" step="0.01" max="<?php echo $mbalance; ?>" name="amt" placeholder="e.g. 1000" required style="width: 100%; background: #0c120f; border: 1px solid var(--card-border); color: #fff; padding: 12px 16px; border-radius: 14px; outline: none;">
                        </div>
                    </div>

                    <button type="submit" name="submit" style="background: var(--primary-accent); color: #000; font-weight: 800; border: none; padding: 12px 24px; border-radius: 30px; cursor: pointer;">
                        Proceed with Bank Transfer
                    </button>
                </form>
            </div>
        </div>

    </div>

    <div style="background: #141c17; border: 1px solid var(--card-border); border-radius: 20px; padding: 16px 20px; text-align: center; font-size: 0.82rem; color: var(--text-muted);">
        Questions regarding settlement limits or authorization? Contact support: <strong style="color: #fff;"><?php echo htmlspecialchars($sitemail); ?></strong>
    </div>

</div>

<!-- PIN Authorization Modal Dialog -->
<div id="pinModal" style="display: <?php echo $mods == 'show' ? 'flex' : 'none'; ?>; position: fixed; inset: 0; background: rgba(0,0,0,0.85); z-index: 9999; align-items: center; justify-content: center; padding: 16px;">
    <div style="background: #151c18; border: 1px solid var(--card-border); border-radius: 28px; padding: 28px; max-width: 420px; width: 100%; color: #fff; box-shadow: 0 20px 50px rgba(0,0,0,0.8);">
        <h4 style="font-weight: 800; margin-bottom: 8px;">Withdrawal Security PIN</h4>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 20px;">Please enter your personal authorization security PIN to confirm this withdrawal request.</p>

        <form method="post" action="withdraw.php">
            <div style="margin-bottom: 20px;">
                <input type="password" name="wpin" placeholder="Enter security PIN" required autofocus style="width: 100%; background: #0c120f; border: 1px solid var(--card-border); color: #fff; padding: 14px 18px; border-radius: 16px; text-align: center; font-size: 1.4rem; letter-spacing: 4px; outline: none;">
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" name="pin" style="flex: 1; background: var(--primary-accent); color: #000; font-weight: 800; border: none; padding: 12px; border-radius: 30px; cursor: pointer;">
                    Authorize & Continue
                </button>
                <button type="button" onclick="document.getElementById('pinModal').style.display='none'" style="background: #223028; color: #fff; font-weight: 600; border: none; padding: 12px 20px; border-radius: 30px; cursor: pointer;">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<?php 
include_once __DIR__ . '/footer.php';
?>
