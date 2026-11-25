<?php 
include_once __DIR__ . '/header.php';

$msg = "";
$err = "";

// Check if directed from Shares Order
$is_shares_order  = (isset($_GET['type']) && $_GET['type'] === 'shares');
$order_code_param = mysqli_real_escape_string($link, trim($_GET['order_code'] ?? ''));
$symbol_param     = mysqli_real_escape_string($link, trim($_GET['symbol'] ?? 'TSLA'));
$shares_qty_param = floatval($_GET['shares'] ?? 1);
$prefill_amt      = floatval($_GET['amount'] ?? 0);

// Handle user submitting deposit confirmation
if (isset($_POST['submit_deposit'])) {
    $crypto   = mysqli_real_escape_string($link, trim($_POST['crypto'] ?? 'Bitcoin (BTC)'));
    $amt      = floatval(trim($_POST['amount'] ?? 0));
    $txn_hash = mysqli_real_escape_string($link, trim($_POST['txn_hash'] ?? ''));
    $attached_order_code = mysqli_real_escape_string($link, trim($_POST['attached_order_code'] ?? ''));
    $date     = date('Y-m-d H:i:s');

    // Handle payment proof image upload
    $proof_img_path = "";
    if (isset($_FILES['proof_image']) && $_FILES['proof_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['proof_image']['tmp_name'];
        $file_name = $_FILES['proof_image']['name'];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        
        if (in_array($ext, $allowed)) {
            $dest_dir = __DIR__ . '/../images/deposits/';
            if (!is_dir($dest_dir)) {
                @mkdir($dest_dir, 0777, true);
            }
            $target_name = "depo_" . intval($id) . "_" . time() . "_" . bin2hex(random_bytes(2)) . "." . $ext;
            $target_path = $dest_dir . $target_name;
            
            if (move_uploaded_file($file_tmp, $target_path)) {
                $proof_img_path = "images/deposits/" . $target_name;
            } else {
                $err = "Could not save uploaded payment proof image. Please try again.";
            }
        } else {
            $err = "Invalid payment proof file format. Please upload JPG, PNG, or WEBP.";
        }
    }

    if ($amt <= 0) {
        $err = "Please enter a valid deposit amount greater than $0.";
    } elseif (empty($txn_hash) && empty($proof_img_path)) {
        $err = "Please provide your blockchain transaction hash or upload a payment proof screenshot.";
    } elseif (empty($err)) {
        ensure_column_exists($link, 'deposits', 'crypto', "VARCHAR(50) DEFAULT 'BTC'");
        ensure_column_exists($link, 'deposits', 'txn_hash', "VARCHAR(255) DEFAULT ''");
        ensure_column_exists($link, 'deposits', 'shares_order_id', "INT DEFAULT 0");
        ensure_column_exists($link, 'deposits', 'proof_image', "VARCHAR(255) DEFAULT ''");

        $shares_order_db_id = 0;
        if (!empty($attached_order_code)) {
            $q_find_ord = mysqli_query($link, "SELECT id FROM shares_orders WHERE order_code = '$attached_order_code' LIMIT 1");
            if ($q_find_ord && $ord_data = mysqli_fetch_assoc($q_find_ord)) {
                $shares_order_db_id = intval($ord_data['id']);
            }
        }

        // Insert as PENDING with proof image for admin review and approval
        $ins = mysqli_query($link, "INSERT INTO deposits (email, amount, date, status, crypto, txn_hash, shares_order_id, proof_image) VALUES ('$uemail', '$amt', '$date', 'PENDING', '$crypto', '$txn_hash', '$shares_order_db_id', '$proof_img_path')");
        if ($ins) {
            $new_depo_id = mysqli_insert_id($link);
            if ($shares_order_db_id > 0) {
                mysqli_query($link, "UPDATE shares_orders SET deposit_id = '$new_depo_id', payment_status = 'Payment Submitted' WHERE id = '$shares_order_db_id'");
            }
            $msg = "Deposit request for $" . number_format($amt, 2) . " submitted successfully! Your payment proof and details have been sent to the admin for verification.";
        } else {
            $err = "Error submitting deposit: " . mysqli_error($link);
        }
    }
}
?>

<div style="max-width: 850px; margin: 0 auto;">

    <!-- Page Title Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h2 style="font-weight: 800; font-size: 1.8rem; color: #fff; margin-bottom: 4px;">Deposit Funds</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0;">Official admin-managed deposit gateway. Select asset and send payment to the verified address.</p>
        </div>
        <a href="customer.php" class="icon-btn-round" title="Back to Dashboard">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
    </div>

    <?php if ($msg != "") { ?>
    <div style="background: rgba(46, 204, 113, 0.15); border: 1px solid var(--color-green); color: var(--color-green); padding: 14px 18px; border-radius: 16px; margin-bottom: 20px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-circle-check font-size-18"></i>
        <span><?php echo $msg; ?></span>
    </div>
    <?php } ?>

    <?php if ($err != "") { ?>
    <div style="background: rgba(255, 82, 82, 0.15); border: 1px solid var(--color-red); color: var(--color-red); padding: 14px 18px; border-radius: 16px; margin-bottom: 20px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-circle-exclamation font-size-18"></i>
        <span><?php echo $err; ?></span>
    </div>
    <?php } ?>

    <?php if ($is_shares_order) { ?>
    <div style="background: rgba(204,255,0,0.08); border: 1px solid var(--primary-accent); border-radius: 20px; padding: 20px 22px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; box-shadow: 0 8px 25px rgba(204,255,0,0.12);">
        <div style="display:flex; align-items:center; gap:16px;">
            <div style="width:50px; height:50px; border-radius:14px; background:var(--primary-accent); color:#000; display:flex; align-items:center; justify-content:center; font-size:1.5rem; font-weight:800; flex-shrink:0;">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <div>
                <h4 style="font-weight: 800; color: #fff; margin: 0; font-size: 1.15rem;">Funding Shares Order: <span style="font-family:monospace; color:var(--primary-accent);"><?php echo htmlspecialchars($order_code_param); ?></span></h4>
                <p style="font-size: 0.88rem; color: #cbd5e1; margin: 4px 0 0;">Purchasing <strong><?php echo number_format($shares_qty_param, 2); ?> Shares</strong> of <strong><?php echo htmlspecialchars($symbol_param); ?></strong>. Send exactly <strong>$<?php echo number_format($prefill_amt, 2); ?> USD</strong> via your chosen network below.</p>
            </div>
        </div>
        <span style="background:var(--primary-accent); color:#000; font-weight:800; font-size:0.82rem; padding:6px 14px; border-radius:12px;">ORDER ACTIVE</span>
    </div>
    <?php } ?>

    <?php 
    $btc_addr = !empty($wallet) ? $wallet : 'bc1qzxhs0c73r4tmphcrmwjtwjvdxcc9eapcuqn63e';
    $eth_addr = !empty($walt_eth) ? $walt_eth : '0x71C83d3129486c9A74cfdD522aAf5E0C2B9f5fE0';
    $usdt_addr = !empty($walt_usdt) ? $walt_usdt : 'TXh8Z3z79Vp7f8ZpL2hY4eR3nK9wM1qXyT';

    $btc_qr_img = (!empty($qr_btc) && file_exists(__DIR__ . '/../' . explode('?', $qr_btc)[0])) ? '../' . $qr_btc : '';
    $eth_qr_img = (!empty($qr_eth) && file_exists(__DIR__ . '/../' . explode('?', $qr_eth)[0])) ? '../' . $qr_eth : '';
    $usdt_qr_img = (!empty($qr_usdt) && file_exists(__DIR__ . '/../' . explode('?', $qr_usdt)[0])) ? '../' . $qr_usdt : '';
    ?>

    <!-- Crypto Asset Selector Tabs -->
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 24px;">
        <div id="tab-btc" onclick="selectCryptoTab('btc', 'Bitcoin (BTC)', '<?php echo htmlspecialchars($btc_addr); ?>', 'Bitcoin Network', '<?php echo htmlspecialchars($btc_qr_img); ?>')" style="background: #151c18; border: 2px solid var(--primary-accent); border-radius: 20px; padding: 16px; cursor: pointer; text-align: center; transition: all 0.2s ease;">
            <div class="coin-icon-badge coin-icon-btc" style="margin: 0 auto 8px; width: 36px; height: 36px; font-size: 1.1rem;">
                <i class="fa-brands fa-bitcoin"></i>
            </div>
            <div style="font-weight: 800; font-size: 1rem; color: #fff;">Bitcoin</div>
            <div style="font-size: 0.75rem; color: var(--primary-accent);">BTC Network</div>
        </div>

        <div id="tab-eth" onclick="selectCryptoTab('eth', 'Ethereum (ETH)', '<?php echo htmlspecialchars($eth_addr); ?>', 'Ethereum ERC-20', '<?php echo htmlspecialchars($eth_qr_img); ?>')" style="background: #151c18; border: 1px solid var(--card-border); border-radius: 20px; padding: 16px; cursor: pointer; text-align: center; transition: all 0.2s ease;">
            <div class="coin-icon-badge coin-icon-eth" style="margin: 0 auto 8px; width: 36px; height: 36px; font-size: 1.1rem;">
                <i class="fa-brands fa-ethereum"></i>
            </div>
            <div style="font-weight: 800; font-size: 1rem; color: #fff;">Ethereum</div>
            <div style="font-size: 0.75rem; color: var(--text-muted);">ERC-20</div>
        </div>

        <div id="tab-usdt" onclick="selectCryptoTab('usdt', 'Tether (USDT)', '<?php echo htmlspecialchars($usdt_addr); ?>', 'USDT TRC-20', '<?php echo htmlspecialchars($usdt_qr_img); ?>')" style="background: #151c18; border: 1px solid var(--card-border); border-radius: 20px; padding: 16px; cursor: pointer; text-align: center; transition: all 0.2s ease;">
            <div class="coin-icon-badge" style="margin: 0 auto 8px; width: 36px; height: 36px; font-size: 1.1rem; background: #26a17b; color: #fff;">
                <i class="fa-solid fa-dollar-sign"></i>
            </div>
            <div style="font-weight: 800; font-size: 1rem; color: #fff;">Tether USDT</div>
            <div style="font-size: 0.75rem; color: var(--text-muted);">TRC-20 / ERC-20</div>
        </div>
    </div>

    <!-- Active Deposit Address Card -->
    <div style="background: #141c17; border: 1px solid var(--card-border); border-radius: 28px; padding: 28px; margin-bottom: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.4);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
            <span style="font-size: 0.85rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;" id="networkBadge">
                Network: Bitcoin Network
            </span>
            <span style="background: rgba(204, 255, 0, 0.15); color: var(--primary-accent); padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 800;">
                <i class="fa-solid fa-shield-halved me-1"></i> Admin Verified
            </span>
        </div>

        <!-- Address Box with 1-click copy -->
        <label style="font-size: 0.82rem; color: var(--text-muted); margin-bottom: 6px; display: block;">Deposit Wallet Address</label>
        <div style="display: flex; gap: 8px; margin-bottom: 20px; flex-wrap: wrap;">
            <input type="text" id="depositAddressInput" readonly value="<?php echo htmlspecialchars($btc_addr); ?>" style="flex: 1; min-width: 260px; background: #0c120f; border: 1px solid var(--card-border); color: #fff; padding: 14px 18px; border-radius: 16px; font-family: monospace; font-size: 0.95rem; font-weight: 600; outline: none;">
            <button type="button" onclick="copyDepositAddress()" style="background: var(--primary-accent); color: #000; border: none; font-weight: 800; padding: 14px 24px; border-radius: 16px; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s ease;">
                <i class="fa-regular fa-copy"></i> Copy
            </button>
        </div>

        <!-- 100% Admin Hosted QR Code (Zero 3rd party requests) -->
        <div style="background: #0c120f; border: 1px solid var(--card-border); border-radius: 20px; padding: 18px; text-align: center; max-width: 220px; margin: 0 auto 20px;">
            <div id="depositQrContainer" style="width: 160px; height: 160px; margin: 0 auto; display: flex; align-items: center; justify-content: center; background: #fff; border-radius: 12px; padding: 6px; box-shadow: 0 4px 15px rgba(0,0,0,0.4);"></div>
            <small style="color: var(--text-muted); font-size: 0.72rem; margin-top: 10px; display: block;">
                <i class="fa-solid fa-shield-check text-success me-1"></i> Official Admin Payment QR
            </small>
        </div>

        <div style="background: rgba(255, 255, 255, 0.03); border-radius: 14px; padding: 12px 16px; font-size: 0.8rem; color: var(--text-muted); text-align: center;">
            <i class="fa-solid fa-circle-info me-1" style="color: var(--primary-accent);"></i> Send only <strong id="selectedCryptoName" style="color: #fff;">Bitcoin (BTC)</strong> to this address. Sending any other currency will result in permanent loss.
        </div>
    </div>

    <!-- Submit Payment Confirmation Form -->
    <div style="background: #141c17; border: 1px solid var(--card-border); border-radius: 28px; padding: 28px; margin-bottom: 30px;">
        <h4 style="font-weight: 800; color: #fff; margin-bottom: 6px;">Submit Deposit Confirmation</h4>
        <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 20px;">After completing your transfer, submit your transaction details below for admin verification and instant balance crediting.</p>

        <form action="deposit.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="crypto" id="formCryptoInput" value="Bitcoin (BTC)">
            <input type="hidden" name="attached_order_code" value="<?php echo htmlspecialchars($order_code_param); ?>">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="font-size: 0.82rem; color: var(--text-muted); margin-bottom: 6px; display: block;">Amount Paid ($ USD)</label>
                    <input type="number" step="0.01" min="1" name="amount" value="<?php echo $prefill_amt > 0 ? htmlspecialchars($prefill_amt) : ''; ?>" placeholder="e.g. 500.00" required style="width: 100%; background: #0c120f; border: 1px solid var(--card-border); color: #fff; padding: 12px 16px; border-radius: 14px; outline: none; font-weight: 800;">
                </div>
                <div>
                    <label style="font-size: 0.82rem; color: var(--text-muted); margin-bottom: 6px; display: block;">Selected Currency</label>
                    <input type="text" id="formCryptoDisplay" readonly value="Bitcoin (BTC)" style="width: 100%; background: #0c120f; border: 1px solid var(--card-border); color: var(--primary-accent); padding: 12px 16px; border-radius: 14px; outline: none; font-weight: 700;">
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="font-size: 0.82rem; color: var(--text-muted); margin-bottom: 6px; display: block;">Transaction Hash / Payment Reference ID</label>
                <input type="text" name="txn_hash" placeholder="Paste your blockchain transaction hash or reference ID here..." required style="width: 100%; background: #0c120f; border: 1px solid var(--card-border); color: #fff; padding: 12px 16px; border-radius: 14px; outline: none; font-family: monospace;">
            </div>

            <!-- Payment Proof File Upload Section -->
            <div style="margin-bottom: 24px;">
                <label style="font-size: 0.82rem; color: #cbd5e1; font-weight: 700; margin-bottom: 6px; display: flex; align-items: center; justify-content: space-between;">
                    <span><i class="fa-solid fa-receipt me-1" style="color:var(--primary-accent);"></i> Upload Payment Proof / Transfer Receipt</span>
                    <span style="font-size: 0.7rem; background:rgba(204,255,0,0.12); color:var(--primary-accent); padding:2px 8px; border-radius:6px; font-weight:800;">SENT TO ADMIN</span>
                </label>
                <div style="position: relative; background: #0c120f; border: 2px dashed rgba(204,255,0,0.3); border-radius: 16px; padding: 18px; text-align: center; transition: all 0.2s ease;">
                    <input type="file" name="proof_image" id="proofImageInput" accept="image/*" required style="display: none;" onchange="previewProofImage(this)">
                    <label for="proofImageInput" style="cursor: pointer; display: block; margin: 0;">
                        <div id="proofPlaceholder">
                            <i class="fa-solid fa-cloud-arrow-up" style="font-size: 1.8rem; color: var(--primary-accent); margin-bottom: 6px;"></i>
                            <div style="color: #fff; font-weight: 700; font-size: 0.92rem;">Click to upload payment screenshot or transfer receipt</div>
                            <div style="color: #64748b; font-size: 0.75rem; margin-top: 4px;">Supports PNG, JPG, JPEG, WEBP (Admin inspects this to approve)</div>
                        </div>
                        <div id="proofPreviewBox" style="display: none; align-items: center; justify-content: center; gap: 14px;">
                            <img id="proofPreviewImg" src="" alt="Proof Preview" style="max-height: 90px; border-radius: 10px; border: 2px solid var(--primary-accent); object-fit: cover;">
                            <div style="text-align: left;">
                                <div id="proofFileName" style="color: #fff; font-weight: 700; font-size: 0.88rem;"></div>
                                <span style="color: var(--primary-accent); font-size: 0.76rem; font-weight: 800;"><i class="fa-solid fa-circle-check me-1"></i> Receipt Ready for Admin</span>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <button type="submit" name="submit_deposit" style="width: 100%; background: var(--primary-accent); color: #000; font-weight: 800; font-size: 1.05rem; border: none; padding: 14px; border-radius: 30px; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 6px 20px var(--primary-accent-glow);">
                <i class="fa-solid fa-paper-plane me-2"></i> Submit Deposit & Proof to Admin
            </button>
        </form>
    </div>

    <!-- User's Recent Deposits History Table -->
    <div style="background: #141c17; border: 1px solid var(--card-border); border-radius: 28px; padding: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h4 style="font-weight: 800; color: #fff; margin: 0;">My Deposit History</h4>
            <a href="deposit_history.php" style="color: var(--primary-accent); font-size: 0.85rem; font-weight: 700; text-decoration: none;">View All &rarr;</a>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.88rem;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--card-border); color: var(--text-muted);">
                        <th style="padding: 10px 12px;">Date</th>
                        <th style="padding: 10px 12px;">Amount</th>
                        <th style="padding: 10px 12px;">Asset</th>
                        <th style="padding: 10px 12px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $user_deposits = mysqli_query($link, "SELECT * FROM deposits WHERE email = '$uemail' ORDER BY id DESC LIMIT 5");
                    if ($user_deposits && mysqli_num_rows($user_deposits) > 0) {
                        while ($drow = mysqli_fetch_assoc($user_deposits)) {
                            $is_comp = ($drow['status'] == 'COMPLETED');
                    ?>
                    <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.04);">
                        <td style="padding: 12px; color: var(--text-muted);"><?php echo htmlspecialchars($drow['date']); ?></td>
                        <td style="padding: 12px; font-weight: 700; color: #fff;">$<?php echo number_format((float)$drow['amount'], 2); ?></td>
                        <td style="padding: 12px; color: #fff;"><?php echo htmlspecialchars(!empty($drow['crypto']) ? $drow['crypto'] : 'Bitcoin'); ?></td>
                        <td style="padding: 12px;">
                            <?php if ($is_comp) { ?>
                                <span style="background: rgba(46, 204, 113, 0.15); color: var(--color-green); padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 700;">COMPLETED</span>
                            <?php } else { ?>
                                <span style="background: rgba(241, 196, 15, 0.15); color: #f1c40f; padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 700;">PENDING</span>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                    ?>
                    <tr>
                        <td colspan="4" style="padding: 20px; text-align: center; color: var(--text-muted);">No deposits submitted yet.</td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Standalone 100% Offline QR Code Generator (Zero 3rd party calls) -->
<script src="style/qrcode.min.js"></script>

<script>
function selectCryptoTab(type, name, address, network, customQrImg) {
    // Reset borders
    document.getElementById('tab-btc').style.borderColor = 'var(--card-border)';
    document.getElementById('tab-eth').style.borderColor = 'var(--card-border)';
    document.getElementById('tab-usdt').style.borderColor = 'var(--card-border)';

    // Highlight selected
    document.getElementById('tab-' + type).style.borderColor = 'var(--primary-accent)';

    // Update details
    document.getElementById('depositAddressInput').value = address;
    document.getElementById('networkBadge').innerText = 'Network: ' + network;
    document.getElementById('selectedCryptoName').innerText = name;
    document.getElementById('formCryptoInput').value = name;
    document.getElementById('formCryptoDisplay').value = name;

    // Render QR Code 100% locally from Admin source (Zero third-party APIs)
    const qrContainer = document.getElementById('depositQrContainer');
    qrContainer.innerHTML = '';

    if (customQrImg && customQrImg.trim() !== '') {
        const img = document.createElement('img');
        img.src = customQrImg;
        img.alt = name + ' QR';
        img.style.maxWidth = '148px';
        img.style.maxHeight = '148px';
        img.style.objectFit = 'contain';
        img.style.display = 'block';
        img.style.margin = '0 auto';
        img.style.borderRadius = '6px';
        qrContainer.appendChild(img);
    } else {
        new QRCode(qrContainer, {
            text: address,
            width: 148,
            height: 148,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.M
        });
    }
}

// Initial Local Render on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    selectCryptoTab('btc', 'Bitcoin (BTC)', '<?php echo htmlspecialchars($btc_addr); ?>', 'Bitcoin Network', '<?php echo htmlspecialchars($btc_qr_img); ?>');
});

function copyDepositAddress() {
    const copyText = document.getElementById("depositAddressInput");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value).then(() => {
        swal({
            title: "Copied!",
            text: "Deposit address copied to clipboard: " + copyText.value,
            icon: "success",
            button: "Done",
            timer: 3000
        });
    }).catch(() => {
        document.execCommand("copy");
        swal("Copied!", copyText.value, "success");
    });
}

function previewProofImage(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        document.getElementById('proofFileName').innerText = file.name;
        
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('proofPreviewImg').src = e.target.result;
            document.getElementById('proofPlaceholder').style.display = 'none';
            document.getElementById('proofPreviewBox').style.display = 'flex';
        };
        reader.readAsDataURL(file);
    }
}
</script>

<?php 
include_once __DIR__ . '/footer.php';
?>
