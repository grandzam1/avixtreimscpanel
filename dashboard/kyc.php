<?php 
include_once __DIR__ . '/header.php';

$success_msg = "";
$error_msg = "";

// Ensure kyc uploads directory exists
$kyc_upload_dir = __DIR__ . '/../images/kyc/';
if (!is_dir($kyc_upload_dir)) {
    @mkdir($kyc_upload_dir, 0777, true);
}

// Fetch latest KYC submission for this user
$q_latest_kyc = mysqli_query($link, "SELECT * FROM kyc_applications WHERE email = '$uemail' ORDER BY id DESC LIMIT 1");
$latest_kyc = ($q_latest_kyc && mysqli_num_rows($q_latest_kyc) > 0) ? mysqli_fetch_assoc($q_latest_kyc) : null;

// Handle KYC form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_kyc'])) {
    $doc_type = mysqli_real_escape_string($link, trim($_POST['document_type'] ?? ''));
    $doc_number = mysqli_real_escape_string($link, trim($_POST['document_number'] ?? ''));
    $country = mysqli_real_escape_string($link, trim($_POST['country'] ?? ''));
    $dob = mysqli_real_escape_string($link, trim($_POST['dob'] ?? ''));

    if (empty($doc_type) || empty($doc_number) || empty($country)) {
        $error_msg = "Please fill in all required fields (Document Type, ID Number, Country).";
    } elseif (!isset($_FILES['front_image']) || $_FILES['front_image']['error'] !== UPLOAD_ERR_OK) {
        $error_msg = "Please upload a clear front photo of your government-issued ID.";
    } else {
        $allowed_exts = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];
        $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];

        // Helper upload function
        $upload_file = function($field, $prefix) use ($kyc_upload_dir, $id, $allowed_exts, $allowed_mimes) {
            if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
                return '';
            }
            $file_info = $_FILES[$field];
            $ext = strtolower(pathinfo($file_info['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed_exts)) {
                return false;
            }
            if ($file_info['size'] > 10 * 1024 * 1024) { // 10MB limit
                return false;
            }
            $new_name = $prefix . '_' . intval($id) . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $dest = $kyc_upload_dir . $new_name;
            if (move_uploaded_file($file_info['tmp_name'], $dest)) {
                return 'images/kyc/' . $new_name;
            }
            return false;
        };

        $front_path = $upload_file('front_image', 'front');
        if ($front_path === false) {
            $error_msg = "Invalid front image file. Only JPG, PNG, WEBP, or PDF files under 10MB are allowed.";
        } else {
            $back_path = $upload_file('back_image', 'back');
            if ($back_path === false) $back_path = '';

            $selfie_path = $upload_file('selfie_image', 'selfie');
            if ($selfie_path === false) $selfie_path = '';

            $insert = mysqli_query($link, "INSERT INTO kyc_applications 
                (user_id, username, email, document_type, document_number, front_image, back_image, selfie_image, country, dob, status) 
                VALUES ('$id', '$username', '$uemail', '$doc_type', '$doc_number', '$front_path', '$back_path', '$selfie_path', '$country', '$dob', 'PENDING')");

            if ($insert) {
                mysqli_query($link, "UPDATE users SET kyc_status = 'pending' WHERE email = '$uemail'");
                $success_msg = "Your KYC identity verification application has been submitted successfully! Compliance will review your documents shortly.";
                // Refresh latest kyc
                $q_latest_kyc = mysqli_query($link, "SELECT * FROM kyc_applications WHERE email = '$uemail' ORDER BY id DESC LIMIT 1");
                $latest_kyc = ($q_latest_kyc && mysqli_num_rows($q_latest_kyc) > 0) ? mysqli_fetch_assoc($q_latest_kyc) : null;
            } else {
                $error_msg = "Error submitting application: " . mysqli_error($link);
            }
        }
    }
}

$is_user_verified = ($verification == '1');
$is_pending = (!$is_user_verified && $latest_kyc && $latest_kyc['status'] === 'PENDING');
$is_rejected = (!$is_user_verified && $latest_kyc && $latest_kyc['status'] === 'REJECTED');
?>

<div style="max-width: 860px; margin: 0 auto;">

    <!-- Breadcrumb & Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
        <div>
            <h2 style="font-weight: 800; font-size: 1.8rem; color: #fff; margin-bottom: 4px;">KYC Identity Verification</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0;">Institutional customer verification and regulatory compliance desk.</p>
        </div>
        <a href="customer.php" class="icon-btn-round" title="Back to Dashboard">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
    </div>

    <?php if (!empty($success_msg)) { ?>
        <div style="background: rgba(46, 204, 113, 0.15); border: 1px solid rgba(46, 204, 113, 0.4); border-radius: 16px; padding: 16px 20px; color: #2ecc71; margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
            <i class="fa-solid fa-circle-check" style="font-size: 1.4rem;"></i>
            <div style="font-size: 0.92rem; font-weight: 600;"><?php echo $success_msg; ?></div>
        </div>
    <?php } ?>

    <?php if (!empty($error_msg)) { ?>
        <div style="background: rgba(231, 76, 60, 0.15); border: 1px solid rgba(231, 76, 60, 0.4); border-radius: 16px; padding: 16px 20px; color: #e74c3c; margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
            <i class="fa-solid fa-circle-exclamation" style="font-size: 1.4rem;"></i>
            <div style="font-size: 0.92rem; font-weight: 600;"><?php echo $error_msg; ?></div>
        </div>
    <?php } ?>

    <?php if ($is_user_verified) { ?>
        <!-- STATE 1: FULLY VERIFIED ACCOUNT -->
        <div style="background: linear-gradient(135deg, rgba(20, 28, 23, 0.98), rgba(12, 18, 14, 0.98)); border: 1px solid rgba(46, 204, 113, 0.4); border-radius: 28px; padding: 36px 30px; text-align: center; box-shadow: 0 10px 40px rgba(0,0,0,0.5); margin-bottom: 30px;">
            <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(46, 204, 113, 0.15); border: 2px solid #2ecc71; color: #2ecc71; display: inline-flex; align-items: center; justify-content: center; font-size: 2.4rem; margin-bottom: 20px; box-shadow: 0 0 25px rgba(46, 204, 113, 0.3);">
                <i class="fa-solid fa-shield-check"></i>
            </div>
            <h3 style="font-weight: 800; font-size: 1.6rem; color: #fff; margin-bottom: 8px;">Account Fully Verified</h3>
            <p style="color: var(--text-muted); font-size: 0.95rem; max-width: 580px; margin: 0 auto 24px; line-height: 1.6;">
                Your account holds Level 2 institutional KYC clearance. Unlimited fiat withdrawals, high-frequency stock trading, and priority blockchain settlements are fully enabled.
            </p>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; max-width: 680px; margin: 0 auto 28px; text-align: left;">
                <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--card-border); border-radius: 16px; padding: 14px 18px;">
                    <div style="font-size: 0.75rem; color: var(--text-muted);">Legal Full Name</div>
                    <div style="font-weight: 700; color: #fff; font-size: 0.92rem;"><?php echo htmlspecialchars($fullname ?: $username); ?></div>
                </div>
                <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--card-border); border-radius: 16px; padding: 14px 18px;">
                    <div style="font-size: 0.75rem; color: var(--text-muted);">Account Email</div>
                    <div style="font-weight: 700; color: #fff; font-size: 0.92rem;"><?php echo htmlspecialchars($uemail); ?></div>
                </div>
                <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--card-border); border-radius: 16px; padding: 14px 18px;">
                    <div style="font-size: 0.75rem; color: var(--text-muted);">Clearance Status</div>
                    <div style="font-weight: 800; color: #2ecc71; font-size: 0.92rem;"><i class="fa-solid fa-circle-check me-1"></i> VERIFIED</div>
                </div>
                <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--card-border); border-radius: 16px; padding: 14px 18px;">
                    <div style="font-size: 0.75rem; color: var(--text-muted);">Daily Withdrawal Limit</div>
                    <div style="font-weight: 800; color: var(--primary-accent); font-size: 0.92rem;">Unlimited Institutional</div>
                </div>
            </div>

            <div style="display: flex; gap: 14px; justify-content: center; flex-wrap: wrap;">
                <a href="withdraw.php" class="btn-pill-buy" style="text-decoration: none; padding: 12px 28px; font-size: 0.92rem;">
                    <i class="fa-solid fa-wallet me-2"></i> Proceed to Withdraw
                </a>
                <a href="customer.php" class="btn-pill-ghost" style="text-decoration: none; padding: 12px 24px; font-size: 0.92rem;">
                    <i class="fa-solid fa-chart-pie me-2"></i> Dashboard Overview
                </a>
            </div>
        </div>

    <?php } elseif ($is_pending) { ?>
        <!-- STATE 2: APPLICATION UNDER REVIEW -->
        <div style="background: #141c17; border: 1px solid rgba(241, 196, 15, 0.4); border-radius: 28px; padding: 36px 30px; text-align: center; box-shadow: 0 10px 40px rgba(0,0,0,0.5); margin-bottom: 30px;">
            <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(241, 196, 15, 0.15); border: 2px solid #f1c40f; color: #f1c40f; display: inline-flex; align-items: center; justify-content: center; font-size: 2.2rem; margin-bottom: 20px; box-shadow: 0 0 25px rgba(241, 196, 15, 0.25);">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <h3 style="font-weight: 800; font-size: 1.6rem; color: #fff; margin-bottom: 8px;">KYC Application Under Review</h3>
            <p style="color: var(--text-muted); font-size: 0.95rem; max-width: 580px; margin: 0 auto 24px; line-height: 1.6;">
                Your identity verification files have been transmitted to the compliance desk. Our compliance team verifies documents within 15 minutes to 2 business hours.
            </p>

            <!-- Submitted Data Card -->
            <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--card-border); border-radius: 20px; padding: 22px; max-width: 680px; margin: 0 auto 28px; text-align: left;">
                <div style="font-size: 0.82rem; font-weight: 800; color: var(--primary-accent); margin-bottom: 14px; text-transform: uppercase; letter-spacing: 1px;">
                    <i class="fa-solid fa-file-shield me-2"></i> Submitted Application Summary
                </div>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; font-size: 0.88rem;">
                    <div>
                        <span style="color: var(--text-muted); display: block; font-size: 0.78rem;">Document Type:</span>
                        <strong style="color: #fff;"><?php echo htmlspecialchars($latest_kyc['document_type']); ?></strong>
                    </div>
                    <div>
                        <span style="color: var(--text-muted); display: block; font-size: 0.78rem;">ID / Document Number:</span>
                        <strong style="color: #fff;"><?php echo htmlspecialchars($latest_kyc['document_number']); ?></strong>
                    </div>
                    <div>
                        <span style="color: var(--text-muted); display: block; font-size: 0.78rem;">Issuing Country:</span>
                        <strong style="color: #fff;"><?php echo htmlspecialchars($latest_kyc['country']); ?></strong>
                    </div>
                    <div>
                        <span style="color: var(--text-muted); display: block; font-size: 0.78rem;">Submission Date:</span>
                        <strong style="color: #fff;"><?php echo htmlspecialchars($latest_kyc['created_at']); ?></strong>
                    </div>
                </div>

                <!-- Document previews -->
                <div style="margin-top: 18px; padding-top: 14px; border-top: 1px solid rgba(255,255,255,0.06);">
                    <span style="color: var(--text-muted); font-size: 0.78rem; display: block; margin-bottom: 8px;">Uploaded Documents:</span>
                    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                        <?php if (!empty($latest_kyc['front_image'])) { ?>
                            <a href="../<?php echo htmlspecialchars($latest_kyc['front_image']); ?>" target="_blank" style="display: flex; align-items: center; gap: 6px; background: rgba(255,255,255,0.06); padding: 6px 12px; border-radius: 10px; color: #fff; font-size: 0.78rem; text-decoration: none;">
                                <i class="fa-solid fa-id-card"></i> Front ID <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 0.65rem; color: var(--text-muted);"></i>
                            </a>
                        <?php } ?>
                        <?php if (!empty($latest_kyc['back_image'])) { ?>
                            <a href="../<?php echo htmlspecialchars($latest_kyc['back_image']); ?>" target="_blank" style="display: flex; align-items: center; gap: 6px; background: rgba(255,255,255,0.06); padding: 6px 12px; border-radius: 10px; color: #fff; font-size: 0.78rem; text-decoration: none;">
                                <i class="fa-solid fa-id-card-clip"></i> Back ID <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 0.65rem; color: var(--text-muted);"></i>
                            </a>
                        <?php } ?>
                        <?php if (!empty($latest_kyc['selfie_image'])) { ?>
                            <a href="../<?php echo htmlspecialchars($latest_kyc['selfie_image']); ?>" target="_blank" style="display: flex; align-items: center; gap: 6px; background: rgba(255,255,255,0.06); padding: 6px 12px; border-radius: 10px; color: #fff; font-size: 0.78rem; text-decoration: none;">
                                <i class="fa-solid fa-camera"></i> Selfie with ID <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 0.65rem; color: var(--text-muted);"></i>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 14px; justify-content: center;">
                <a href="customer.php" class="btn-pill-ghost" style="text-decoration: none; padding: 12px 28px; font-size: 0.92rem;">
                    <i class="fa-solid fa-chart-pie me-2"></i> Return to Dashboard
                </a>
            </div>
        </div>

    <?php } else { ?>
        <!-- STATE 3: SUBMISSION FORM (Unverified or Rejected) -->
        <?php if ($is_rejected) { ?>
            <div style="background: rgba(231, 76, 60, 0.12); border: 1px solid rgba(231, 76, 60, 0.4); border-radius: 20px; padding: 20px 24px; color: #ff6b6b; margin-bottom: 24px;">
                <div style="font-weight: 800; font-size: 1.05rem; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-triangle-exclamation"></i> Previous Verification Request Requires Re-submission
                </div>
                <p style="font-size: 0.88rem; margin: 0; line-height: 1.5; color: #ffc9c9;">
                    <?php if (!empty($latest_kyc['admin_note'])) { ?>
                        <strong>Compliance Note:</strong> <?php echo htmlspecialchars($latest_kyc['admin_note']); ?>
                    <?php } else { ?>
                        Your prior identification photos were not clear enough or have expired. Please submit high-resolution, uncropped photos below.
                    <?php } ?>
                </p>
            </div>
        <?php } ?>

        <!-- Information Notice -->
        <div style="background: #141c17; border: 1px solid var(--card-border); border-radius: 24px; padding: 24px; margin-bottom: 28px;">
            <div style="display: flex; gap: 16px; align-items: flex-start;">
                <div style="width: 44px; height: 44px; border-radius: 14px; background: rgba(204,255,0,0.15); color: var(--primary-accent); display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;">
                    <i class="fa-solid fa-id-card"></i>
                </div>
                <div>
                    <h4 style="font-weight: 800; color: #fff; margin-bottom: 6px; font-size: 1.1rem;">Verification Guidelines</h4>
                    <p style="color: var(--text-muted); font-size: 0.86rem; margin: 0; line-height: 1.5;">
                        To comply with international financial AML/KYC regulations, please provide a valid government-issued photo ID. Ensure all four corners are visible, text is crisp and legible, and there is no glare.
                    </p>
                </div>
            </div>
        </div>

        <!-- KYC Upload Form -->
        <div style="background: #141c17; border: 1px solid var(--card-border); border-radius: 28px; padding: 32px 28px; box-shadow: 0 10px 40px rgba(0,0,0,0.5);">
            <form action="kyc.php" method="POST" enctype="multipart/form-data" id="kycSubmitForm">

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 24px;">
                    <div>
                        <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #fff; margin-bottom: 8px;">
                            Document Type <span style="color: #ff5252;">*</span>
                        </label>
                        <select name="document_type" required style="width: 100%; background: #0c120f; border: 1px solid var(--card-border); border-radius: 14px; padding: 14px 16px; color: #fff; font-size: 0.9rem; font-family: inherit; outline: none;">
                            <option value="National Identity Card">National Identity Card (NID / CNIC)</option>
                            <option value="International Passport">International Passport</option>
                            <option value="Driver License">Driver's License</option>
                            <option value="Residence Permit">Residence Permit / Alien Card</option>
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #fff; margin-bottom: 8px;">
                            Document / ID Number <span style="color: #ff5252;">*</span>
                        </label>
                        <input type="text" name="document_number" required placeholder="e.g. A29485719" style="width: 100%; background: #0c120f; border: 1px solid var(--card-border); border-radius: 14px; padding: 14px 16px; color: #fff; font-size: 0.9rem; font-family: inherit; outline: none;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #fff; margin-bottom: 8px;">
                            Issuing Country <span style="color: #ff5252;">*</span>
                        </label>
                        <input type="text" name="country" required placeholder="e.g. United States, Germany, United Kingdom" style="width: 100%; background: #0c120f; border: 1px solid var(--card-border); border-radius: 14px; padding: 14px 16px; color: #fff; font-size: 0.9rem; font-family: inherit; outline: none;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #fff; margin-bottom: 8px;">
                            Date of Birth
                        </label>
                        <input type="date" name="dob" style="width: 100%; background: #0c120f; border: 1px solid var(--card-border); border-radius: 14px; padding: 14px 16px; color: #fff; font-size: 0.9rem; font-family: inherit; outline: none; color-scheme: dark;">
                    </div>
                </div>

                <!-- Document Photo Uploads Grid -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 28px;">

                    <!-- 1. Front of Document -->
                    <div>
                        <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #fff; margin-bottom: 8px;">
                            Front of Document <span style="color: #ff5252;">*</span>
                        </label>
                        <div style="background: #0c120f; border: 2px dashed rgba(204,255,0,0.35); border-radius: 18px; padding: 20px; text-align: center; position: relative; cursor: pointer; transition: all 0.2s;" onclick="document.getElementById('kycFrontInput').click()">
                            <input type="file" name="front_image" id="kycFrontInput" accept="image/*,.pdf" required style="display: none;" onchange="previewKycDoc(this, 'previewFront', 'iconFront')">
                            <div id="iconFront">
                                <i class="fa-solid fa-address-card" style="font-size: 2rem; color: var(--primary-accent); margin-bottom: 8px; display: block;"></i>
                                <div style="font-size: 0.85rem; font-weight: 700; color: #fff;">Upload Front Photo</div>
                                <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 4px;">JPG, PNG, WEBP (Max 10MB)</div>
                            </div>
                            <img id="previewFront" src="" alt="Preview" style="display: none; width: 100%; max-height: 140px; object-fit: contain; border-radius: 10px;">
                        </div>
                    </div>

                    <!-- 2. Back of Document (Optional for passport) -->
                    <div>
                        <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #fff; margin-bottom: 8px;">
                            Back of Document <span style="font-size: 0.72rem; color: var(--text-muted); font-weight: normal;">(ID / License)</span>
                        </label>
                        <div style="background: #0c120f; border: 2px dashed var(--card-border); border-radius: 18px; padding: 20px; text-align: center; position: relative; cursor: pointer; transition: all 0.2s;" onclick="document.getElementById('kycBackInput').click()">
                            <input type="file" name="back_image" id="kycBackInput" accept="image/*,.pdf" style="display: none;" onchange="previewKycDoc(this, 'previewBack', 'iconBack')">
                            <div id="iconBack">
                                <i class="fa-solid fa-id-card-clip" style="font-size: 2rem; color: var(--text-muted); margin-bottom: 8px; display: block;"></i>
                                <div style="font-size: 0.85rem; font-weight: 700; color: #fff;">Upload Back Photo</div>
                                <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 4px;">Optional for Passport</div>
                            </div>
                            <img id="previewBack" src="" alt="Preview" style="display: none; width: 100%; max-height: 140px; object-fit: contain; border-radius: 10px;">
                        </div>
                    </div>

                    <!-- 3. Selfie with ID -->
                    <div>
                        <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #fff; margin-bottom: 8px;">
                            Selfie Holding ID <span style="color: #ff5252;">*</span>
                        </label>
                        <div style="background: #0c120f; border: 2px dashed rgba(204,255,0,0.35); border-radius: 18px; padding: 20px; text-align: center; position: relative; cursor: pointer; transition: all 0.2s;" onclick="document.getElementById('kycSelfieInput').click()">
                            <input type="file" name="selfie_image" id="kycSelfieInput" accept="image/*,.pdf" required style="display: none;" onchange="previewKycDoc(this, 'previewSelfie', 'iconSelfie')">
                            <div id="iconSelfie">
                                <i class="fa-solid fa-camera" style="font-size: 2rem; color: var(--primary-accent); margin-bottom: 8px; display: block;"></i>
                                <div style="font-size: 0.85rem; font-weight: 700; color: #fff;">Upload Selfie with ID</div>
                                <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 4px;">Your face & ID clearly visible</div>
                            </div>
                            <img id="previewSelfie" src="" alt="Preview" style="display: none; width: 100%; max-height: 140px; object-fit: contain; border-radius: 10px;">
                        </div>
                    </div>

                </div>

                <!-- Legal Declaration -->
                <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--card-border); border-radius: 16px; padding: 16px 20px; margin-bottom: 26px; display: flex; gap: 12px; align-items: flex-start;">
                    <input type="checkbox" id="kycConsent" required style="margin-top: 4px; accent-color: var(--primary-accent); width: 18px; height: 18px; cursor: pointer;">
                    <label for="kycConsent" style="font-size: 0.84rem; color: var(--text-muted); margin: 0; line-height: 1.5; cursor: pointer;">
                        I solemnly declare that all personal identification details and document scans provided belong to me and are authentic, legally valid, and unmanipulated.
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" name="submit_kyc" class="btn-pill-buy" style="width: 100%; padding: 16px; font-size: 1rem; font-weight: 800; display: flex; align-items: center; justify-content: center; gap: 10px; cursor: pointer; border: none;">
                    <i class="fa-solid fa-shield-check"></i> Submit KYC Verification Application
                </button>

            </form>
        </div>
    <?php } ?>

</div>

<script>
function previewKycDoc(input, previewId, iconId) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById(previewId);
                const icon = document.getElementById(iconId);
                if (img && icon) {
                    img.src = e.target.result;
                    img.style.display = 'block';
                    icon.style.display = 'none';
                }
            };
            reader.readAsDataURL(file);
        } else {
            const icon = document.getElementById(iconId);
            if (icon) {
                icon.innerHTML = '<i class="fa-solid fa-file-pdf" style="font-size: 2rem; color: #ff5252; margin-bottom: 8px; display: block;"></i>' +
                                 '<div style="font-size: 0.85rem; font-weight: 700; color: #fff;">' + file.name + '</div>' +
                                 '<div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 4px;">File attached</div>';
            }
        }
    }
}
</script>

<?php 
include_once __DIR__ . '/footer.php';
?>
