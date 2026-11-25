<?php 
$page = "Deposits";
include_once __DIR__ . '/header.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
$msg = "";
$err_msg = "";
	
if (isset($_POST['approve'])) {
    $depost_id = intval($_POST['deposit_id']);
    $email     = mysqli_real_escape_string($link, trim($_POST['email']));
    $amount    = floatval($_POST['amount']);
    $method    = mysqli_real_escape_string($link, trim($_POST['method'] ?? 'Bitcoin'));
    $status    = trim($_POST['status']);

    if ($status === "COMPLETED") {
        $msg = "This deposit has already been approved.";
    } else {
        $query = mysqli_query($link, "UPDATE deposits SET status = 'COMPLETED' WHERE id = '$depost_id'");

        if ($query) {
            // Correctly credit the user's active deposit balance (dbalance)
            mysqli_query($link, "UPDATE users SET dbalance = dbalance + $amount WHERE email = '$email'");

            // If deposit was created for a stock shares order, also approve the shares order
            $q_chk_shares = mysqli_query($link, "SELECT shares_order_id FROM deposits WHERE id = '$depost_id' LIMIT 1");
            if ($q_chk_shares && $sh_row = mysqli_fetch_assoc($q_chk_shares)) {
                $sh_order_id = intval($sh_row['shares_order_id'] ?? 0);
                if ($sh_order_id > 0) {
                    mysqli_query($link, "UPDATE shares_orders SET status = 'Approved', payment_status = 'Verified & Active' WHERE id = '$sh_order_id'");
                }
            }

            // Fetch user's full name
            $u_query = mysqli_query($link, "SELECT fullname FROM users WHERE email = '$email'");
            $u_data = mysqli_fetch_assoc($u_query);
            $name = $u_data ? $u_data['fullname'] : 'Investor';

            // Send notification email to the actual user
            if (file_exists("../PHPMailer/PHPMailer.php")) {
                require_once "../PHPMailer/PHPMailer.php";
                require_once '../PHPMailer/Exception.php';

                try {
                    $mail = new PHPMailer(true);
                    $mail->setFrom($sitemail, $sitename);
                    $mail->addAddress($email, $name);
                    $mail->Subject = "Deposit Approved - " . $sitename;
                    $mail->isHTML(true);

                    $mail->Body = '
                    <div style="background: #0b0f0d; width: 100%; padding: 40px 15px; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif; color: #fff;">
                        <div style="background: #151c18; max-width: 600px; margin: 0 auto; border-radius: 16px; border: 1px solid rgba(255,255,255,0.08); overflow: hidden;">
                            <div style="background: #1d2822; padding: 25px 30px; border-bottom: 2px solid ' . ($theme_color ?? '#ccff00') . ';">
                                <h2 style="margin: 0; color: #fff; font-size: 22px;">' . htmlspecialchars($sitename) . '</h2>
                            </div>
                            <div style="padding: 30px; font-size: 15px; line-height: 1.6; color: #d0d7d3;">
                                <p>Dear <strong>' . htmlspecialchars(ucwords($name)) . '</strong>,</p>
                                <p>Congratulations! Your deposit of <strong style="color: ' . ($theme_color ?? '#ccff00') . ';">$' . number_format($amount, 2) . ' USD</strong> (' . htmlspecialchars($method) . ') has been verified and approved.</p>
                                <p>Your account deposit balance has been credited successfully. You can now track your active portfolio in your dashboard.</p>
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
                    // Suppress mail sending error if mail server is offline in local dev
                }
            }

            $msg = "Deposit #$depost_id approved successfully! $" . number_format($amount, 2) . " credited to $email.";
        } else {
            $err_msg = "Error updating deposit: " . mysqli_error($link);
        }
    }
}

if (isset($_POST['reject'])) {
    $depost_id = intval($_POST['deposit_id']);
    $email     = mysqli_real_escape_string($link, trim($_POST['email']));
    $query = mysqli_query($link, "UPDATE deposits SET status = 'REJECTED' WHERE id = '$depost_id'");
    if ($query) {
        // Also update linked shares order if any
        $q_chk_shares = mysqli_query($link, "SELECT shares_order_id FROM deposits WHERE id = '$depost_id' LIMIT 1");
        if ($q_chk_shares && $sh_row = mysqli_fetch_assoc($q_chk_shares)) {
            $sh_order_id = intval($sh_row['shares_order_id'] ?? 0);
            if ($sh_order_id > 0) {
                mysqli_query($link, "UPDATE shares_orders SET status = 'Rejected', payment_status = 'Payment Rejected' WHERE id = '$sh_order_id'");
            }
        }
        $msg = "Deposit request #$depost_id for $email has been REJECTED.";
    } else {
        $err_msg = "Error rejecting deposit: " . mysqli_error($link);
    }
}

if (isset($_POST['delete'])) {
    $depost_id = intval($_POST['deposit_id']);
    $del = mysqli_query($link, "DELETE FROM deposits WHERE id = '$depost_id'");
    if ($del) {
        $msg = "Deposit transaction #$depost_id deleted successfully.";
    } else {
        $err_msg = "Error deleting deposit: " . mysqli_error($link);
    }
}
?>

<div class="row">
    <?php if ($msg != "") { ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-all me-2"></i> <?php echo $msg; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php } ?>
    <?php if ($err_msg != "") { ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle me-2"></i> <?php echo $err_msg; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php } ?>

    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title">Manage Deposit Requests</h4>
                    <p class="card-title-desc mb-0">All incoming user deposits must be verified and manually approved here by the admin. No external auto-execution occurs.</p>
                </div>
                <a href="new-deposit.php" class="btn btn-primary">
                    <i class="mdi mdi-plus me-1"></i> Add Manual Deposit
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatable" class="table table-bordered dt-responsive nowrap w-100 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>User Email</th>
                                <th>Name</th>
                                <th>Amount</th>
                                <th>Asset / Method</th>
                                <th>Payment Proof</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $select = mysqli_query($link, "SELECT * FROM deposits ORDER BY id DESC");
                            if ($select && mysqli_num_rows($select) > 0) {
                                while ($row = mysqli_fetch_assoc($select)) {
                                    $usr = mysqli_real_escape_string($link, $row['email']);
                                    $user = mysqli_query($link, "SELECT fullname FROM users WHERE email = '$usr'");
                                    $user_r = $user ? mysqli_fetch_assoc($user) : null;
                                    $fullname = $user_r ? $user_r['fullname'] : 'Unknown User';
                                    $method = !empty($row['crypto']) ? $row['crypto'] : 'Bitcoin (BTC)';
                                    
                                    // Check payment proof image
                                    $proof_img = !empty($row['proof_image']) ? $row['proof_image'] : '';
                                    $has_proof = false;
                                    $proof_web_path = '';
                                    if (!empty($proof_img)) {
                                        if (strpos($proof_img, 'http') === 0) {
                                            $proof_web_path = $proof_img;
                                            $has_proof = true;
                                        } elseif (file_exists(__DIR__ . '/../' . $proof_img)) {
                                            $proof_web_path = '../' . $proof_img;
                                            $has_proof = true;
                                        } elseif (file_exists(__DIR__ . '/' . $proof_img)) {
                                            $proof_web_path = $proof_img;
                                            $has_proof = true;
                                        }
                                    }
                            ?>
                            <tr>
                                <td>#<?php echo $row['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($row['email']); ?></strong></td>
                                <td><?php echo htmlspecialchars(ucfirst($fullname)); ?></td>
                                <td><span class="badge bg-success font-size-13">$<?php echo number_format((float)$row['amount'], 2); ?></span></td>
                                <td>
                                    <span class="badge bg-soft-info text-info"><?php echo htmlspecialchars($method); ?></span>
                                    <?php if (!empty($row['txn_hash'])) { ?>
                                        <br><small class="text-muted" title="<?php echo htmlspecialchars($row['txn_hash']); ?>">
                                            TX: <?php echo htmlspecialchars(substr($row['txn_hash'], 0, 16)) . '...'; ?>
                                        </small>
                                    <?php } ?>
                                </td>
                                <td>
                                    <?php if ($has_proof) { ?>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="javascript:void(0)" onclick="viewDepositProof('<?php echo htmlspecialchars($proof_web_path); ?>', '<?php echo $row['id']; ?>', '<?php echo htmlspecialchars(addslashes($row['email'])); ?>', '<?php echo number_format((float)$row['amount'], 2); ?>', '<?php echo htmlspecialchars(addslashes($method)); ?>', '<?php echo htmlspecialchars(addslashes($row['txn_hash'] ?? '')); ?>', '<?php echo $row['status']; ?>')" title="Click to view payment proof">
                                                <img src="<?php echo htmlspecialchars($proof_web_path); ?>" alt="Proof" style="width: 44px; height: 44px; object-fit: cover; border-radius: 8px; border: 2px solid #556ee6; box-shadow: 0 2px 6px rgba(0,0,0,0.15); transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewDepositProof('<?php echo htmlspecialchars($proof_web_path); ?>', '<?php echo $row['id']; ?>', '<?php echo htmlspecialchars(addslashes($row['email'])); ?>', '<?php echo number_format((float)$row['amount'], 2); ?>', '<?php echo htmlspecialchars(addslashes($method)); ?>', '<?php echo htmlspecialchars(addslashes($row['txn_hash'] ?? '')); ?>', '<?php echo $row['status']; ?>')">
                                                <i class="mdi mdi-eye"></i> View
                                            </button>
                                        </div>
                                    <?php } else { ?>
                                        <span class="badge bg-light text-muted font-size-12"><i class="mdi mdi-image-off me-1"></i> No receipt</span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <?php if ($row['status'] == "PENDING") { ?>
                                        <span class="badge bg-warning font-size-12">PENDING</span>
                                    <?php } elseif ($row['status'] == "REJECTED") { ?>
                                        <span class="badge bg-danger font-size-12">REJECTED</span>
                                    <?php } else { ?>
                                        <span class="badge bg-success font-size-12">COMPLETED</span>
                                    <?php } ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['date']); ?></td>
                                <td>
                                    <form action="deposits.php" method="post" style="display:inline-flex; gap: 6px; align-items:center;">
                                        <input type="hidden" name="deposit_id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($row['email']); ?>">
                                        <input type="hidden" name="amount" value="<?php echo htmlspecialchars($row['amount']); ?>">
                                        <input type="hidden" name="method" value="<?php echo htmlspecialchars($method); ?>">
                                        <input type="hidden" name="status" value="<?php echo htmlspecialchars($row['status']); ?>">

                                        <?php if ($row['status'] == "PENDING") { ?>
                                            <button type="submit" name="approve" class="btn btn-sm btn-success" onclick="return confirm('Approve deposit of $<?php echo $row['amount']; ?> for <?php echo htmlspecialchars($row['email']); ?>?')">
                                                <i class="mdi mdi-check"></i> Approve
                                            </button>
                                            <button type="submit" name="reject" class="btn btn-sm btn-warning" onclick="return confirm('Reject deposit #<?php echo $row['id']; ?> for <?php echo htmlspecialchars($row['email']); ?>?')">
                                                <i class="mdi mdi-close"></i> Reject
                                            </button>
                                        <?php } elseif ($row['status'] == "REJECTED") { ?>
                                            <button type="button" class="btn btn-sm btn-outline-danger" disabled>
                                                <i class="mdi mdi-close-circle"></i> Rejected
                                            </button>
                                        <?php } else { ?>
                                            <button type="button" class="btn btn-sm btn-outline-success" disabled>
                                                <i class="mdi mdi-check-all"></i> Approved
                                            </button>
                                        <?php } ?>

                                        <button type="submit" name="delete" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this deposit record?')">
                                            <i class="mdi mdi-trash-can"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php 
                                }
                            } else {
                            ?>
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">No deposit records found.</td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Viewing High-Resolution Deposit Proof -->
<div class="modal fade" id="depositProofModal" tabindex="-1" aria-labelledby="depositProofModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="depositProofModalLabel"><i class="mdi mdi-file-document-outline me-2"></i> Deposit Payment Proof #<span id="modalDepId"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-md-7 text-center">
                        <div style="background: #0f172a; padding: 12px; border-radius: 12px; border: 1px solid #334155; min-height: 280px; display: flex; align-items: center; justify-content: center;">
                            <img id="modalProofImg" src="" alt="Payment Receipt" class="img-fluid rounded" style="max-height: 440px; width: auto; object-fit: contain; cursor: zoom-in;" onclick="window.open(this.src, '_blank')">
                        </div>
                        <div class="mt-2 text-muted small">
                            <i class="mdi mdi-magnify-plus-outline"></i> Click image to open full resolution in new tab
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="card border h-100 mb-0">
                            <div class="card-body">
                                <h6 class="text-uppercase text-muted font-size-11 mb-3">Deposit Transaction Info</h6>
                                
                                <div class="mb-3">
                                    <label class="form-label text-muted mb-0 small">Investor Email</label>
                                    <div class="fw-bold" id="modalDepEmail"></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted mb-0 small">Deposit Amount</label>
                                    <div class="fs-4 fw-bold text-success" id="modalDepAmount"></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted mb-0 small">Asset / Payment Method</label>
                                    <div><span class="badge bg-primary" id="modalDepMethod"></span></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted mb-0 small">Blockchain TX Hash</label>
                                    <div class="font-monospace small text-break p-2 bg-light rounded" id="modalDepTxn">None provided</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted mb-0 small">Current Status</label>
                                    <div id="modalDepStatusBadge"></div>
                                </div>

                                <div id="modalActionButtons" class="mt-4 pt-3 border-top">
                                    <form action="deposits.php" method="post" class="d-flex flex-column gap-2">
                                        <input type="hidden" name="deposit_id" id="modalFormDepId" value="">
                                        <input type="hidden" name="email" id="modalFormEmail" value="">
                                        <input type="hidden" name="amount" id="modalFormAmount" value="">
                                        <input type="hidden" name="method" id="modalFormMethod" value="">
                                        <input type="hidden" name="status" value="PENDING">

                                        <button type="submit" name="approve" class="btn btn-success w-100" onclick="return confirm('Approve this deposit request now?')">
                                            <i class="mdi mdi-check-circle me-1"></i> Approve Deposit
                                        </button>
                                        <button type="submit" name="reject" class="btn btn-warning w-100" onclick="return confirm('Reject this deposit request?')">
                                            <i class="mdi mdi-close-circle me-1"></i> Reject Deposit
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a id="modalNewTabLink" href="#" target="_blank" class="btn btn-outline-secondary">
                    <i class="mdi mdi-open-in-new me-1"></i> Open Original Image
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function viewDepositProof(imgUrl, id, email, amount, method, txn, status) {
    document.getElementById('modalDepId').textContent = id;
    document.getElementById('modalDepEmail').textContent = email;
    document.getElementById('modalDepAmount').textContent = amount;
    document.getElementById('modalDepMethod').textContent = method;
    document.getElementById('modalDepTxn').textContent = txn ? txn : 'No blockchain hash entered';
    document.getElementById('modalProofImg').src = imgUrl;
    document.getElementById('modalNewTabLink').href = imgUrl;

    document.getElementById('modalFormDepId').value = id;
    document.getElementById('modalFormEmail').value = email;
    document.getElementById('modalFormAmount').value = amount.replace('$', '').replace(/,/g, '');
    document.getElementById('modalFormMethod').value = method;

    const statusBadge = document.getElementById('modalDepStatusBadge');
    const actionBtns = document.getElementById('modalActionButtons');
    
    if (status === 'PENDING') {
        statusBadge.innerHTML = '<span class="badge bg-warning font-size-12">PENDING VERIFICATION</span>';
        actionBtns.style.display = 'block';
    } else if (status === 'REJECTED') {
        statusBadge.innerHTML = '<span class="badge bg-danger font-size-12">REJECTED</span>';
        actionBtns.style.display = 'none';
    } else {
        statusBadge.innerHTML = '<span class="badge bg-success font-size-12">COMPLETED / APPROVED</span>';
        actionBtns.style.display = 'none';
    }

    const myModal = new bootstrap.Modal(document.getElementById('depositProofModal'));
    myModal.show();
}
</script>

<?php 
include_once __DIR__ . '/footer.php';
?>
