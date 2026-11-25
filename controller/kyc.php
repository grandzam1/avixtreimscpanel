<?php 
$page = "KYC Verification Desk";
include_once __DIR__ . '/header.php';

$msg = "";
$err_msg = "";

// Ensure required KYC columns exist in users table
if (isset($link) && $link) {
    if (function_exists('ensure_column_exists')) {
        ensure_column_exists($link, 'users', 'verification', "INT DEFAULT 0");
        ensure_column_exists($link, 'users', 'kyc_status', "VARCHAR(30) DEFAULT 'unverified'");
    } else {
        $chk_v = mysqli_query($link, "SHOW COLUMNS FROM `users` LIKE 'verification'");
        if ($chk_v && mysqli_num_rows($chk_v) == 0) {
            @mysqli_query($link, "ALTER TABLE `users` ADD COLUMN `verification` INT DEFAULT 0");
        }
        $chk_k = mysqli_query($link, "SHOW COLUMNS FROM `users` LIKE 'kyc_status'");
        if ($chk_k && mysqli_num_rows($chk_k) == 0) {
            @mysqli_query($link, "ALTER TABLE `users` ADD COLUMN `kyc_status` VARCHAR(30) DEFAULT 'unverified'");
        }
    }
}

// Handle KYC Review Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Detect existing columns in users table
    $u_cols = [];
    $cq = @mysqli_query($link, "SHOW COLUMNS FROM `users`");
    if ($cq) {
        while ($cr = mysqli_fetch_assoc($cq)) {
            $u_cols[strtolower($cr['Field'])] = true;
        }
    }
    if (!isset($u_cols['verification'])) {
        @mysqli_query($link, "ALTER TABLE `users` ADD `verification` INT DEFAULT 0");
        $u_cols['verification'] = true;
    }
    if (!isset($u_cols['kyc_status'])) {
        @mysqli_query($link, "ALTER TABLE `users` ADD `kyc_status` VARCHAR(30) DEFAULT 'unverified'");
        $u_cols['kyc_status'] = true;
    }

    if (isset($_POST['approve_kyc'])) {
        $kyc_id = intval($_POST['kyc_id']);
        $email = mysqli_real_escape_string($link, trim($_POST['email']));
        
        $up_kyc = mysqli_query($link, "UPDATE kyc_applications SET status = 'APPROVED' WHERE id = '$kyc_id'");
        
        $set_parts = [];
        if (!empty($u_cols['verification'])) {
            $set_parts[] = "`verification` = '1'";
        }
        if (!empty($u_cols['kyc_status'])) {
            $set_parts[] = "`kyc_status` = 'verified'";
        }
        if (empty($set_parts)) {
            @mysqli_query($link, "ALTER TABLE `users` ADD `verification` INT DEFAULT 0");
            $set_parts[] = "`verification` = '1'";
        }

        $sql = "UPDATE users SET " . implode(', ', $set_parts) . " WHERE email = '$email'";
        $up_user = @mysqli_query($link, $sql);
        if (!$up_user) {
            @mysqli_query($link, "ALTER TABLE `users` ADD `verification` INT DEFAULT 0");
            @mysqli_query($link, "ALTER TABLE `users` ADD `kyc_status` VARCHAR(30) DEFAULT 'unverified'");
            $up_user = mysqli_query($link, "UPDATE users SET `verification` = '1' WHERE email = '$email'");
        }
        
        if ($up_kyc && $up_user) {
            $msg = "KYC Application #$kyc_id for $email has been APPROVED. User now has full Level 2 verification clearance!";
        } else {
            $err_msg = "Error approving KYC: " . mysqli_error($link);
        }
    }

    if (isset($_POST['reject_kyc'])) {
        $kyc_id = intval($_POST['kyc_id']);
        $email = mysqli_real_escape_string($link, trim($_POST['email']));
        $admin_note = mysqli_real_escape_string($link, trim($_POST['admin_note'] ?? 'Documents provided are invalid or illegible.'));
        
        $up_kyc = mysqli_query($link, "UPDATE kyc_applications SET status = 'REJECTED', admin_note = '$admin_note' WHERE id = '$kyc_id'");
        
        $set_parts = [];
        if (!empty($u_cols['verification'])) {
            $set_parts[] = "`verification` = '2'";
        }
        if (!empty($u_cols['kyc_status'])) {
            $set_parts[] = "`kyc_status` = 'rejected'";
        }
        if (empty($set_parts)) {
            @mysqli_query($link, "ALTER TABLE `users` ADD `verification` INT DEFAULT 0");
            $set_parts[] = "`verification` = '2'";
        }

        $sql = "UPDATE users SET " . implode(', ', $set_parts) . " WHERE email = '$email'";
        $up_user = @mysqli_query($link, $sql);
        if (!$up_user) {
            @mysqli_query($link, "ALTER TABLE `users` ADD `verification` INT DEFAULT 0");
            @mysqli_query($link, "ALTER TABLE `users` ADD `kyc_status` VARCHAR(30) DEFAULT 'unverified'");
            $up_user = mysqli_query($link, "UPDATE users SET `verification` = '2' WHERE email = '$email'");
        }
        
        if ($up_kyc) {
            $msg = "KYC Application #$kyc_id for $email has been REJECTED.";
        } else {
            $err_msg = "Error rejecting KYC: " . mysqli_error($link);
        }
    }

    if (isset($_POST['delete_kyc'])) {
        $kyc_id = intval($_POST['kyc_id']);
        $del = mysqli_query($link, "DELETE FROM kyc_applications WHERE id = '$kyc_id'");
        if ($del) {
            $msg = "KYC Application #$kyc_id removed from system.";
        } else {
            $err_msg = "Error deleting KYC: " . mysqli_error($link);
        }
    }
}

// Compute Metrics
$q_total = mysqli_query($link, "SELECT COUNT(*) as c FROM kyc_applications");
$total_kyc = ($q_total && $r = mysqli_fetch_assoc($q_total)) ? $r['c'] : 0;

$q_pend = mysqli_query($link, "SELECT COUNT(*) as c FROM kyc_applications WHERE status = 'PENDING'");
$pending_kyc = ($q_pend && $r = mysqli_fetch_assoc($q_pend)) ? $r['c'] : 0;

$q_appr = mysqli_query($link, "SELECT COUNT(*) as c FROM kyc_applications WHERE status = 'APPROVED'");
$approved_kyc = ($q_appr && $r = mysqli_fetch_assoc($q_appr)) ? $r['c'] : 0;

$q_rej = mysqli_query($link, "SELECT COUNT(*) as c FROM kyc_applications WHERE status = 'REJECTED'");
$rejected_kyc = ($q_rej && $r = mysqli_fetch_assoc($q_rej)) ? $r['c'] : 0;

// Search filter
$search = isset($_GET['search']) ? mysqli_real_escape_string($link, trim($_GET['search'])) : '';
$where_clause = "";
if (!empty($search)) {
    $where_clause = "WHERE email LIKE '%$search%' OR username LIKE '%$search%' OR document_number LIKE '%$search%'";
}
?>

<div class="row">
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

    <!-- Summary Metrics -->
    <div class="col-xl-3 col-md-6">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium mb-1">Total Submissions</p>
                        <h4 class="mb-0"><?php echo number_format($total_kyc); ?></h4>
                    </div>
                    <div class="mini-stat-icon avatar-sm rounded-circle bg-primary align-self-center">
                        <span class="avatar-title">
                            <i class="mdi mdi-file-document-multiple font-size-24"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium mb-1">Pending Review</p>
                        <h4 class="mb-0 text-warning"><?php echo number_format($pending_kyc); ?></h4>
                    </div>
                    <div class="mini-stat-icon avatar-sm rounded-circle bg-warning align-self-center">
                        <span class="avatar-title bg-warning">
                            <i class="mdi mdi-clock-alert-outline font-size-24 text-white"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium mb-1">Approved Clearance</p>
                        <h4 class="mb-0 text-success"><?php echo number_format($approved_kyc); ?></h4>
                    </div>
                    <div class="mini-stat-icon avatar-sm rounded-circle bg-success align-self-center">
                        <span class="avatar-title bg-success">
                            <i class="mdi mdi-shield-check font-size-24 text-white"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium mb-1">Rejected Submissions</p>
                        <h4 class="mb-0 text-danger"><?php echo number_format($rejected_kyc); ?></h4>
                    </div>
                    <div class="mini-stat-icon avatar-sm rounded-circle bg-danger align-self-center">
                        <span class="avatar-title bg-danger">
                            <i class="mdi mdi-close-octagon font-size-24 text-white"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KYC Table Card -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="card-title">KYC Identity Applications</h4>
                    <p class="card-title-desc mb-0">Inspect user-submitted ID documents (Passport, National ID, Driver's License, Selfie) and grant institutional clearance.</p>
                </div>
                <div class="d-flex gap-2">
                    <?php if (!empty($search)) { ?>
                        <a href="kyc.php" class="btn btn-outline-secondary btn-sm">Clear Filter</a>
                    <?php } ?>
                    <form action="kyc.php" method="GET" class="d-flex gap-1">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search email / user..." value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="mdi mdi-magnify"></i></button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatable" class="table table-bordered dt-responsive nowrap w-100 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Investor</th>
                                <th>Document Info</th>
                                <th>Country / DOB</th>
                                <th>Document Photos</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $select = mysqli_query($link, "SELECT * FROM kyc_applications $where_clause ORDER BY id DESC");
                            if ($select && mysqli_num_rows($select) > 0) {
                                while ($row = mysqli_fetch_assoc($select)) {
                                    $u_email = mysqli_real_escape_string($link, $row['email']);
                                    $q_u = mysqli_query($link, "SELECT fullname, verification FROM users WHERE email = '$u_email'");
                                    $u_data = ($q_u && mysqli_num_rows($q_u) > 0) ? mysqli_fetch_assoc($q_u) : null;
                                    $fullname = $u_data ? $u_data['fullname'] : $row['username'];

                                    // Resolve image web paths
                                    $fix_path = function($p) {
                                        if (empty($p)) return '';
                                        if (strpos($p, 'http') === 0) return $p;
                                        if (file_exists(__DIR__ . '/../' . $p)) return '../' . $p;
                                        if (file_exists(__DIR__ . '/' . $p)) return $p;
                                        return '../' . $p;
                                    };

                                    $front_web = $fix_path($row['front_image']);
                                    $back_web = $fix_path($row['back_image']);
                                    $selfie_web = $fix_path($row['selfie_image']);
                            ?>
                            <tr>
                                <td>#<?php echo $row['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($fullname); ?></strong>
                                    <div class="text-muted small"><?php echo htmlspecialchars($row['email']); ?></div>
                                    <div class="small text-info">@<?php echo htmlspecialchars($row['username']); ?></div>
                                </td>
                                <td>
                                    <span class="badge bg-soft-info text-info"><?php echo htmlspecialchars($row['document_type']); ?></span>
                                    <div class="font-monospace small fw-bold mt-1"><?php echo htmlspecialchars($row['document_number']); ?></div>
                                </td>
                                <td>
                                    <div><i class="mdi mdi-earth me-1"></i> <?php echo htmlspecialchars($row['country'] ?: 'N/A'); ?></div>
                                    <?php if (!empty($row['dob'])) { ?>
                                        <div class="small text-muted"><i class="mdi mdi-calendar me-1"></i> <?php echo htmlspecialchars($row['dob']); ?></div>
                                    <?php } ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <?php if (!empty($front_web)) { ?>
                                            <a href="javascript:void(0)" onclick="openKycModal('<?php echo $row['id']; ?>', '<?php echo htmlspecialchars(addslashes($fullname)); ?>', '<?php echo htmlspecialchars(addslashes($row['email'])); ?>', '<?php echo htmlspecialchars(addslashes($row['document_type'])); ?>', '<?php echo htmlspecialchars(addslashes($row['document_number'])); ?>', '<?php echo htmlspecialchars(addslashes($row['country'])); ?>', '<?php echo htmlspecialchars($front_web); ?>', '<?php echo htmlspecialchars($back_web); ?>', '<?php echo htmlspecialchars($selfie_web); ?>', '<?php echo $row['status']; ?>')" title="Inspect Front ID">
                                                <img src="<?php echo htmlspecialchars($front_web); ?>" alt="Front ID" style="width: 44px; height: 44px; object-fit: cover; border-radius: 8px; border: 2px solid #556ee6; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                                            </a>
                                        <?php } ?>

                                        <?php if (!empty($back_web)) { ?>
                                            <a href="javascript:void(0)" onclick="openKycModal('<?php echo $row['id']; ?>', '<?php echo htmlspecialchars(addslashes($fullname)); ?>', '<?php echo htmlspecialchars(addslashes($row['email'])); ?>', '<?php echo htmlspecialchars(addslashes($row['document_type'])); ?>', '<?php echo htmlspecialchars(addslashes($row['document_number'])); ?>', '<?php echo htmlspecialchars(addslashes($row['country'])); ?>', '<?php echo htmlspecialchars($front_web); ?>', '<?php echo htmlspecialchars($back_web); ?>', '<?php echo htmlspecialchars($selfie_web); ?>', '<?php echo $row['status']; ?>')" title="Inspect Back ID">
                                                <img src="<?php echo htmlspecialchars($back_web); ?>" alt="Back ID" style="width: 44px; height: 44px; object-fit: cover; border-radius: 8px; border: 2px solid #74788d; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                                            </a>
                                        <?php } ?>

                                        <?php if (!empty($selfie_web)) { ?>
                                            <a href="javascript:void(0)" onclick="openKycModal('<?php echo $row['id']; ?>', '<?php echo htmlspecialchars(addslashes($fullname)); ?>', '<?php echo htmlspecialchars(addslashes($row['email'])); ?>', '<?php echo htmlspecialchars(addslashes($row['document_type'])); ?>', '<?php echo htmlspecialchars(addslashes($row['document_number'])); ?>', '<?php echo htmlspecialchars(addslashes($row['country'])); ?>', '<?php echo htmlspecialchars($front_web); ?>', '<?php echo htmlspecialchars($back_web); ?>', '<?php echo htmlspecialchars($selfie_web); ?>', '<?php echo $row['status']; ?>')" title="Inspect Selfie">
                                                <img src="<?php echo htmlspecialchars($selfie_web); ?>" alt="Selfie" style="width: 44px; height: 44px; object-fit: cover; border-radius: 8px; border: 2px solid #34c38f; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                                            </a>
                                        <?php } ?>

                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="openKycModal('<?php echo $row['id']; ?>', '<?php echo htmlspecialchars(addslashes($fullname)); ?>', '<?php echo htmlspecialchars(addslashes($row['email'])); ?>', '<?php echo htmlspecialchars(addslashes($row['document_type'])); ?>', '<?php echo htmlspecialchars(addslashes($row['document_number'])); ?>', '<?php echo htmlspecialchars(addslashes($row['country'])); ?>', '<?php echo htmlspecialchars($front_web); ?>', '<?php echo htmlspecialchars($back_web); ?>', '<?php echo htmlspecialchars($selfie_web); ?>', '<?php echo $row['status']; ?>')">
                                            <i class="mdi mdi-eye"></i> Inspect
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($row['status'] == "PENDING") { ?>
                                        <span class="badge bg-warning font-size-12">PENDING REVIEW</span>
                                    <?php } elseif ($row['status'] == "REJECTED") { ?>
                                        <span class="badge bg-danger font-size-12" title="<?php echo htmlspecialchars($row['admin_note'] ?? ''); ?>">REJECTED</span>
                                    <?php } else { ?>
                                        <span class="badge bg-success font-size-12">APPROVED</span>
                                    <?php } ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        <?php if ($row['status'] == "PENDING") { ?>
                                            <form action="kyc.php" method="post" style="display:inline;">
                                                <input type="hidden" name="kyc_id" value="<?php echo $row['id']; ?>">
                                                <input type="hidden" name="email" value="<?php echo htmlspecialchars($row['email']); ?>">
                                                <button type="submit" name="approve_kyc" class="btn btn-sm btn-success" onclick="return confirm('Grant institutional KYC clearance to <?php echo htmlspecialchars(addslashes($row['email'])); ?>?')">
                                                    <i class="mdi mdi-check"></i> Approve
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-warning" onclick="promptRejectKyc('<?php echo $row['id']; ?>', '<?php echo htmlspecialchars(addslashes($row['email'])); ?>')">
                                                <i class="mdi mdi-close"></i> Reject
                                            </button>
                                        <?php } elseif ($row['status'] == "APPROVED") { ?>
                                            <button type="button" class="btn btn-sm btn-outline-success" disabled>
                                                <i class="mdi mdi-shield-check"></i> Cleared
                                            </button>
                                        <?php } else { ?>
                                            <button type="button" class="btn btn-sm btn-outline-danger" disabled>
                                                <i class="mdi mdi-close-circle"></i> Rejected
                                            </button>
                                        <?php } ?>

                                        <form action="kyc.php" method="post" style="display:inline;" onsubmit="return confirm('Delete this KYC record?');">
                                            <input type="hidden" name="kyc_id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" name="delete_kyc" class="btn btn-sm btn-danger">
                                                <i class="mdi mdi-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php 
                                }
                            } else {
                            ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">No KYC verification applications found.</td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Inspecting High-Resolution KYC Documents -->
<div class="modal fade" id="kycInspectModal" tabindex="-1" aria-labelledby="kycInspectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="kycInspectModalLabel"><i class="mdi mdi-shield-account me-2"></i> KYC Identity Document Inspection #<span id="modalKycId"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <!-- Left: Document Images Grid -->
                    <div class="col-lg-8">
                        <div class="row g-3">
                            <div class="col-md-6 text-center">
                                <div class="border rounded p-2 bg-light">
                                    <div class="fw-bold mb-2 small text-uppercase text-muted"><i class="mdi mdi-id-card me-1"></i> Front of Document</div>
                                    <img id="modalImgFront" src="" alt="Front ID" class="img-fluid rounded border" style="max-height: 240px; width: 100%; object-fit: contain; cursor: zoom-in; background: #000;" onclick="window.open(this.src, '_blank')">
                                    <div class="mt-1 small text-muted">Click to open full size</div>
                                </div>
                            </div>
                            <div class="col-md-6 text-center">
                                <div class="border rounded p-2 bg-light">
                                    <div class="fw-bold mb-2 small text-uppercase text-muted"><i class="mdi mdi-card-bulleted-outline me-1"></i> Back of Document</div>
                                    <img id="modalImgBack" src="" alt="Back ID" class="img-fluid rounded border" style="max-height: 240px; width: 100%; object-fit: contain; cursor: zoom-in; background: #000;" onclick="window.open(this.src, '_blank')">
                                    <div id="modalNoBackMsg" class="py-4 text-muted small" style="display:none;">No back image required/provided</div>
                                </div>
                            </div>
                            <div class="col-12 text-center">
                                <div class="border rounded p-2 bg-light">
                                    <div class="fw-bold mb-2 small text-uppercase text-muted"><i class="mdi mdi-camera-account me-1"></i> Live Selfie Holding Identification</div>
                                    <img id="modalImgSelfie" src="" alt="Selfie with ID" class="img-fluid rounded border" style="max-height: 280px; width: 100%; object-fit: contain; cursor: zoom-in; background: #000;" onclick="window.open(this.src, '_blank')">
                                    <div id="modalNoSelfieMsg" class="py-4 text-muted small" style="display:none;">No selfie photo uploaded</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Investor Information & Direct Action -->
                    <div class="col-lg-4">
                        <div class="card border h-100 mb-0 shadow-none">
                            <div class="card-body">
                                <h6 class="text-uppercase text-muted font-size-11 mb-3">Applicant Clearance Data</h6>
                                
                                <div class="mb-3">
                                    <label class="form-label text-muted mb-0 small">Applicant Name</label>
                                    <div class="fw-bold fs-5" id="modalKycName"></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted mb-0 small">Registered Email</label>
                                    <div class="fw-bold text-primary" id="modalKycEmail"></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted mb-0 small">Document Type</label>
                                    <div><span class="badge bg-info font-size-12" id="modalKycDocType"></span></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted mb-0 small">Document ID Number</label>
                                    <div class="font-monospace fw-bold p-2 bg-light rounded" id="modalKycDocNum"></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted mb-0 small">Issuing Country</label>
                                    <div class="fw-bold" id="modalKycCountry"></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted mb-0 small">Review Status</label>
                                    <div id="modalKycStatusBadge"></div>
                                </div>

                                <div id="modalKycActionGroup" class="mt-4 pt-3 border-top">
                                    <form action="kyc.php" method="post" class="d-flex flex-column gap-2">
                                        <input type="hidden" name="kyc_id" id="modalFormKycId" value="">
                                        <input type="hidden" name="email" id="modalFormKycEmail" value="">

                                        <button type="submit" name="approve_kyc" class="btn btn-success w-100" onclick="return confirm('Grant full KYC verification to this applicant?')">
                                            <i class="mdi mdi-shield-check me-1"></i> Approve &amp; Verify User
                                        </button>
                                        <button type="button" class="btn btn-warning w-100" onclick="triggerRejectFromModal()">
                                            <i class="mdi mdi-close-circle me-1"></i> Reject Application
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Rejection Reason Prompt -->
<div class="modal fade" id="rejectReasonModal" tabindex="-1" aria-labelledby="rejectReasonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="kyc.php" method="POST">
                <input type="hidden" name="kyc_id" id="rejectKycId" value="">
                <input type="hidden" name="email" id="rejectKycEmail" value="">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="rejectReasonModalLabel"><i class="mdi mdi-alert-circle me-1"></i> Reject KYC Application</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted">Please specify the reason for rejecting this applicant's identification documents. This explanation will be displayed to the user so they can correct and resubmit.</p>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Rejection Note / Instructions</label>
                        <textarea name="admin_note" rows="3" class="form-control" placeholder="e.g. ID card photo is blurry / expired. Please re-upload a clear uncropped photo with all corners visible." required>The uploaded identification documents were not clear enough or unreadable. Please upload high-resolution, uncropped photos.</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="reject_kyc" class="btn btn-danger">Confirm Rejection</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentInspectKycId = '';
let currentInspectKycEmail = '';

function openKycModal(id, name, email, docType, docNum, country, frontImg, backImg, selfieImg, status) {
    currentInspectKycId = id;
    currentInspectKycEmail = email;

    document.getElementById('modalKycId').textContent = id;
    document.getElementById('modalKycName').textContent = name;
    document.getElementById('modalKycEmail').textContent = email;
    document.getElementById('modalKycDocType').textContent = docType;
    document.getElementById('modalKycDocNum').textContent = docNum;
    document.getElementById('modalKycCountry').textContent = country || 'N/A';

    document.getElementById('modalFormKycId').value = id;
    document.getElementById('modalFormKycEmail').value = email;

    // Set Front Image
    const imgFront = document.getElementById('modalImgFront');
    imgFront.src = frontImg;

    // Set Back Image
    const imgBack = document.getElementById('modalImgBack');
    const noBack = document.getElementById('modalNoBackMsg');
    if (backImg) {
        imgBack.src = backImg;
        imgBack.style.display = 'block';
        noBack.style.display = 'none';
    } else {
        imgBack.style.display = 'none';
        noBack.style.display = 'block';
    }

    // Set Selfie Image
    const imgSelfie = document.getElementById('modalImgSelfie');
    const noSelfie = document.getElementById('modalNoSelfieMsg');
    if (selfieImg) {
        imgSelfie.src = selfieImg;
        imgSelfie.style.display = 'block';
        noSelfie.style.display = 'none';
    } else {
        imgSelfie.style.display = 'none';
        noSelfie.style.display = 'block';
    }

    // Status & actions
    const statusBadge = document.getElementById('modalKycStatusBadge');
    const actionGroup = document.getElementById('modalKycActionGroup');
    if (status === 'PENDING') {
        statusBadge.innerHTML = '<span class="badge bg-warning font-size-12">PENDING REVIEW</span>';
        actionGroup.style.display = 'block';
    } else if (status === 'REJECTED') {
        statusBadge.innerHTML = '<span class="badge bg-danger font-size-12">REJECTED</span>';
        actionGroup.style.display = 'none';
    } else {
        statusBadge.innerHTML = '<span class="badge bg-success font-size-12">APPROVED / CLEARED</span>';
        actionGroup.style.display = 'none';
    }

    const myModal = new bootstrap.Modal(document.getElementById('kycInspectModal'));
    myModal.show();
}

function promptRejectKyc(id, email) {
    document.getElementById('rejectKycId').value = id;
    document.getElementById('rejectKycEmail').value = email;
    const rejModal = new bootstrap.Modal(document.getElementById('rejectReasonModal'));
    rejModal.show();
}

function triggerRejectFromModal() {
    // Hide inspect modal
    const inspectModalEl = document.getElementById('kycInspectModal');
    const inspectModal = bootstrap.Modal.getInstance(inspectModalEl);
    if (inspectModal) inspectModal.hide();

    promptRejectKyc(currentInspectKycId, currentInspectKycEmail);
}
</script>

<?php 
include_once __DIR__ . '/footer.php';
?>
