<?php
$page = "Add New Deposit";
include_once __DIR__ . '/header.php'; 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$msg = "";
$err_msg = "";

if (isset($_POST['add'])) {
    $usermail = mysqli_real_escape_string($link, trim($_POST['user']));
    $amt      = floatval(trim($_POST['amt']));
    $date     = mysqli_real_escape_string($link, trim($_POST['date']));
    $status   = mysqli_real_escape_string($link, trim($_POST['status']));
    $crypto   = mysqli_real_escape_string($link, trim($_POST['crypto'] ?? 'Bitcoin'));

    if (!empty($usermail) && $amt > 0 && !empty($date) && !empty($status)) {
        // Ensure columns exist
        ensure_column_exists($link, 'deposits', 'crypto', "VARCHAR(50) DEFAULT 'BTC'");
        ensure_column_exists($link, 'deposits', 'txn_hash', "VARCHAR(255) DEFAULT ''");

        $insert = mysqli_query($link, "INSERT INTO deposits (email, amount, date, status, crypto) VALUES ('$usermail', '$amt', '$date', '$status', '$crypto')");
        if ($insert) {
            if ($status === "COMPLETED") {
                mysqli_query($link, "UPDATE users SET dbalance = dbalance + $amt WHERE email = '$usermail'");
            }

            $f = mysqli_query($link, "SELECT fullname FROM users WHERE email = '$usermail'");
            $u = mysqli_fetch_assoc($f);
            $name = $u ? ucfirst($u['fullname']) : 'Investor';

            if (file_exists("../PHPMailer/PHPMailer.php")) {
                require_once "../PHPMailer/PHPMailer.php";
                require_once '../PHPMailer/Exception.php';

                try {
                    $mail = new PHPMailer(true);
                    $mail->setFrom($sitemail, $sitename);
                    // Send to the actual user being credited!
                    $mail->addAddress($usermail, $name);
                    $mail->Subject = "Deposit Credited - " . $sitename;
                    $mail->isHTML(true);

                    $mail->Body = '
                    <div style="background: #0b0f0d; width: 100%; padding: 40px 15px; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif; color: #fff;">
                        <div style="background: #151c18; max-width: 600px; margin: 0 auto; border-radius: 16px; border: 1px solid rgba(255,255,255,0.08); overflow: hidden;">
                            <div style="background: #1d2822; padding: 25px 30px; border-bottom: 2px solid ' . ($theme_color ?? '#ccff00') . ';">
                                <h2 style="margin: 0; color: #fff; font-size: 22px;">' . htmlspecialchars($sitename) . '</h2>
                            </div>
                            <div style="padding: 30px; font-size: 15px; line-height: 1.6; color: #d0d7d3;">
                                <p>Dear <strong>' . htmlspecialchars(ucwords($name)) . '</strong>,</p>
                                <p>Congratulations! A deposit of <strong style="color: ' . ($theme_color ?? '#ccff00') . ';">$' . number_format($amt, 2) . ' USD</strong> (' . htmlspecialchars($crypto) . ') has been credited to your account.</p>
                                <p>Your active deposit balance is now updated. You can access your full portfolio overview in your member dashboard.</p>
                                <div style="margin: 25px 0;">
                                    <a href="' . htmlspecialchars($siteurl) . '/dashboard/" style="background: ' . ($theme_color ?? '#ccff00') . '; color: #000; padding: 12px 25px; border-radius: 30px; text-decoration: none; font-weight: bold; display: inline-block;">View Dashboard</a>
                                </div>
                            </div>
                            <div style="background: #111713; padding: 20px 30px; font-size: 13px; color: #7a8a81; text-align: center;">
                                <p style="margin: 0;">&copy; ' . date('Y') . ' ' . htmlspecialchars($sitename) . '. All rights reserved.</p>
                            </div>
                        </div>
                    </div>';

                    @$mail->send();
                } catch (\Exception $e) {
                    // Mail server offline in local test
                }
            }

            echo "<script>alert('Deposit added successfully!'); window.location.href = 'deposits.php';</script>";
            exit();
        } else {
            $err_msg = "Error creating deposit: " . mysqli_error($link);
        }
    } else {
        $err_msg = "All fields are required and amount must be greater than zero.";
    }
}
?>

<div class="row">
    <div class="col-12">
        <?php if ($msg != "") { ?>
            <div class="alert alert-success"><?php echo $msg; ?></div>
        <?php } ?>
        <?php if ($err_msg != "") { ?>
            <div class="alert alert-danger"><?php echo $err_msg; ?></div>
        <?php } ?>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Add Manual Deposit</h4>
                <p class="card-title-desc">Manually credit a user with a verified deposit. Only authorized administrators can perform this action.</p>
            </div>
            <div class="card-body p-4">
                <form method="post" action="new-deposit.php">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="clientSelect" class="form-label">Select Client</label>
                                <select class="form-control" name="user" id="clientSelect" required>
                                    <option value="">- Select Client -</option>
                                    <?php 
                                    $user_q = mysqli_query($link, "SELECT email, fullname FROM users ORDER BY fullname ASC");
                                    if ($user_q && mysqli_num_rows($user_q) > 0) {
                                        while ($urow = mysqli_fetch_assoc($user_q)) {
                                    ?>
                                        <option value="<?php echo htmlspecialchars($urow['email']); ?>">
                                            <?php echo htmlspecialchars($urow['fullname']) . ' (' . htmlspecialchars($urow['email']) . ')'; ?>
                                        </option>
                                    <?php }} ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="depositAmount" class="form-label">Amount (USD)</label>
                                <input class="form-control" type="number" step="0.01" min="1" name="amt" placeholder="e.g. 500.00" required id="depositAmount">
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label for="cryptoAsset" class="form-label">Asset / Cryptocurrency</label>
                                <select class="form-control" name="crypto" id="cryptoAsset">
                                    <option value="Bitcoin (BTC)">Bitcoin (BTC)</option>
                                    <option value="Ethereum (ETH)">Ethereum (ETH)</option>
                                    <option value="Tether (USDT)">Tether (USDT - TRC20)</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label for="depositDate" class="form-label">Transaction Date</label>
                                <input class="form-control" type="date" name="date" value="<?php echo date('Y-m-d'); ?>" required id="depositDate">
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label for="depositStatus" class="form-label">Deposit Status</label>
                                <select class="form-control" name="status" id="depositStatus">
                                    <option value="COMPLETED">COMPLETED (Credited Instantly)</option>
                                    <option value="PENDING">PENDING (Manual Approval Later)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" name="add" class="btn btn-primary px-4 py-2">
                            <i class="mdi mdi-check-circle me-1"></i> Add and Credit Deposit
                        </button>
                        <a href="deposits.php" class="btn btn-secondary px-4 py-2 ms-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
include_once __DIR__ . '/footer.php';
?>
