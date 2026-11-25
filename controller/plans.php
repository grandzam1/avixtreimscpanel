<?php 
$page = "Investment Plans";
include_once __DIR__ . '/header.php';

$msg = "";
$err_msg = "";

// Handle Delete Plan
if (isset($_POST['delete_plan'])) {
    $plan_id = intval($_POST['plan_id']);
    $del = mysqli_query($link, "DELETE FROM plans WHERE id = '$plan_id'");
    if ($del) {
        $msg = "Plan #$plan_id deleted successfully.";
    } else {
        $err_msg = "Error deleting plan: " . mysqli_error($link);
    }
}

// Handle Add New Plan
if (isset($_POST['add_plan'])) {
    $name        = mysqli_real_escape_string($link, trim($_POST['name']));
    $badge       = mysqli_real_escape_string($link, trim($_POST['badge']));
    $category    = mysqli_real_escape_string($link, trim($_POST['category']));
    $min_deposit = floatval($_POST['min_deposit']);
    $max_deposit = floatval($_POST['max_deposit']);
    $roi         = floatval($_POST['roi']);
    $duration    = mysqli_real_escape_string($link, trim($_POST['duration']));
    $icon        = mysqli_real_escape_string($link, trim($_POST['icon']));
    $description = mysqli_real_escape_string($link, trim($_POST['description']));
    $features    = mysqli_real_escape_string($link, trim($_POST['features']));
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    if (empty($name)) {
        $err_msg = "Plan name is required.";
    } else {
        $sql = "INSERT INTO plans (name, badge, category, min_deposit, max_deposit, roi, duration, icon, description, features, is_featured) 
                VALUES ('$name', '$badge', '$category', '$min_deposit', '$max_deposit', '$roi', '$duration', '$icon', '$description', '$features', '$is_featured')";
        if (mysqli_query($link, $sql)) {
            $msg = "New investment package '$name' created successfully!";
        } else {
            $err_msg = "Error adding plan: " . mysqli_error($link);
        }
    }
}

// Handle Update Plan
if (isset($_POST['update_plan'])) {
    $plan_id     = intval($_POST['plan_id']);
    $name        = mysqli_real_escape_string($link, trim($_POST['name']));
    $badge       = mysqli_real_escape_string($link, trim($_POST['badge']));
    $category    = mysqli_real_escape_string($link, trim($_POST['category']));
    $min_deposit = floatval($_POST['min_deposit']);
    $max_deposit = floatval($_POST['max_deposit']);
    $roi         = floatval($_POST['roi']);
    $duration    = mysqli_real_escape_string($link, trim($_POST['duration']));
    $icon        = mysqli_real_escape_string($link, trim($_POST['icon']));
    $description = mysqli_real_escape_string($link, trim($_POST['description']));
    $features    = mysqli_real_escape_string($link, trim($_POST['features']));
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    if (empty($name)) {
        $err_msg = "Plan name is required.";
    } else {
        $sql = "UPDATE plans SET 
                name = '$name',
                badge = '$badge',
                category = '$category',
                min_deposit = '$min_deposit',
                max_deposit = '$max_deposit',
                roi = '$roi',
                duration = '$duration',
                icon = '$icon',
                description = '$description',
                features = '$features',
                is_featured = '$is_featured'
                WHERE id = '$plan_id'";
        if (mysqli_query($link, $sql)) {
            $msg = "Investment package '$name' updated successfully!";
        } else {
            $err_msg = "Error updating plan: " . mysqli_error($link);
        }
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
                    <h4 class="card-title">Manage Investment Plans & Packages</h4>
                    <p class="card-title-desc mb-0">Customize existing crypto arbitrage, clean compute, and quantitative syndicate plans, or create new bespoke packages for your clients.</p>
                </div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPlanModal">
                    <i class="mdi mdi-plus me-1"></i> Create New Plan
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered dt-responsive nowrap w-100 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Plan Name</th>
                                <th>Category / Badge</th>
                                <th>Min Deposit</th>
                                <th>Max Deposit</th>
                                <th>ROI (%)</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th style="width: 130px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = mysqli_query($link, "SELECT * FROM plans ORDER BY min_deposit ASC");
                            if ($query && mysqli_num_rows($query) > 0) {
                                while ($row = mysqli_fetch_assoc($query)) {
                            ?>
                                <tr>
                                    <td><strong>#<?php echo $row['id']; ?></strong></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs me-2">
                                                <span class="avatar-title rounded-circle bg-primary-subtle text-primary font-size-16">
                                                    <i class="<?php echo !empty($row['icon']) ? htmlspecialchars($row['icon']) : 'fa-solid fa-chart-line'; ?>"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <h5 class="font-size-14 mb-0"><?php echo htmlspecialchars($row['name']); ?></h5>
                                                <small class="text-muted"><?php echo htmlspecialchars(substr($row['description'] ?? '', 0, 45)) . '...'; ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary font-size-12"><?php echo htmlspecialchars($row['badge']); ?></span>
                                        <span class="d-block text-muted font-size-11 mt-1"><?php echo htmlspecialchars($row['category']); ?></span>
                                    </td>
                                    <td><strong class="text-success">$<?php echo number_format($row['min_deposit'], 2); ?></strong></td>
                                    <td><strong>$<?php echo number_format($row['max_deposit'], 2); ?></strong></td>
                                    <td>
                                        <span class="badge bg-success-subtle text-success font-size-13 font-weight-bold">
                                            <?php echo htmlspecialchars($row['roi']); ?>%
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['duration']); ?></td>
                                    <td>
                                        <?php if ($row['is_featured'] == 1) { ?>
                                            <span class="badge bg-warning font-size-12"><i class="mdi mdi-star"></i> Featured</span>
                                        <?php } else { ?>
                                            <span class="badge bg-light text-dark font-size-12">Standard</span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editModal<?php echo $row['id']; ?>" title="Edit Plan">
                                                <i class="mdi mdi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteModal<?php echo $row['id']; ?>" title="Delete Plan">
                                                <i class="mdi mdi-trash-can"></i>
                                            </button>
                                        </div>

                                        <!-- Edit Modal -->
                                        <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="plan_id" value="<?php echo $row['id']; ?>">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Investment Plan: <?php echo htmlspecialchars($row['name']); ?></h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row g-3">
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Plan Name</label>
                                                                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($row['name']); ?>" required>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Badge Label</label>
                                                                    <input type="text" name="badge" class="form-control" value="<?php echo htmlspecialchars($row['badge']); ?>" placeholder="e.g. MOST POPULAR, INSTITUTIONAL">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Category</label>
                                                                    <input type="text" name="category" class="form-control" value="<?php echo htmlspecialchars($row['category']); ?>" placeholder="e.g. Crypto Arbitrage, Clean Energy">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Icon Class (FontAwesome)</label>
                                                                    <input type="text" name="icon" class="form-control" value="<?php echo htmlspecialchars($row['icon'] ?? 'fa-solid fa-chart-line'); ?>">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label">Min Deposit ($ USD)</label>
                                                                    <input type="number" step="0.01" name="min_deposit" class="form-control" value="<?php echo $row['min_deposit']; ?>" required>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label">Max Deposit ($ USD)</label>
                                                                    <input type="number" step="0.01" name="max_deposit" class="form-control" value="<?php echo $row['max_deposit']; ?>" required>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label">ROI (%)</label>
                                                                    <input type="number" step="0.01" name="roi" class="form-control" value="<?php echo $row['roi']; ?>" required>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Duration</label>
                                                                    <input type="text" name="duration" class="form-control" value="<?php echo htmlspecialchars($row['duration']); ?>" placeholder="e.g. 7 Working Days, 14 Working Days" required>
                                                                </div>
                                                                <div class="col-md-6 d-flex align-items-center pt-4">
                                                                    <div class="form-check form-switch font-size-15">
                                                                        <input class="form-check-input" type="checkbox" name="is_featured" id="featured_<?php echo $row['id']; ?>" value="1" <?php echo ($row['is_featured'] == 1) ? 'checked' : ''; ?>>
                                                                        <label class="form-check-label" for="featured_<?php echo $row['id']; ?>">
                                                                            Feature this plan (Highlighted on Front-End)
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <label class="form-label">Description</label>
                                                                    <textarea name="description" class="form-control" rows="2"><?php echo htmlspecialchars($row['description']); ?></textarea>
                                                                </div>
                                                                <div class="col-12">
                                                                    <label class="form-label">Features List (One feature per line)</label>
                                                                    <textarea name="features" class="form-control" rows="4"><?php echo htmlspecialchars($row['features']); ?></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" name="update_plan" class="btn btn-primary">Save Changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="deleteModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="plan_id" value="<?php echo $row['id']; ?>">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Confirm Plan Deletion</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Are you sure you want to permanently delete <strong><?php echo htmlspecialchars($row['name']); ?></strong>? This action cannot be undone.
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" name="delete_plan" class="btn btn-danger">Yes, Delete Plan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php 
                                }
                            } else {
                                echo '<tr><td colspan="9" class="text-center py-4 text-muted">No investment plans found in database. Click "Create New Plan" to add one.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add New Plan Modal -->
<div class="modal fade" id="addPlanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="mdi mdi-plus-circle me-1 text-primary"></i> Create New Investment Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Plan Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Quantitative Market Syndicate" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Badge Label</label>
                            <input type="text" name="badge" class="form-control" placeholder="e.g. INSTITUTIONAL, POPULAR">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <input type="text" name="category" class="form-control" placeholder="e.g. Quantitative Arbitrage, Clean Compute">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Icon Class (FontAwesome)</label>
                            <input type="text" name="icon" class="form-control" value="fa-solid fa-chart-line" placeholder="fa-solid fa-car, fa-solid fa-bolt">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Min Deposit ($ USD)</label>
                            <input type="number" step="0.01" name="min_deposit" class="form-control" placeholder="1000.00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Max Deposit ($ USD)</label>
                            <input type="number" step="0.01" name="max_deposit" class="form-control" placeholder="10000.00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ROI (%)</label>
                            <input type="number" step="0.01" name="roi" class="form-control" placeholder="15.00" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Duration</label>
                            <input type="text" name="duration" class="form-control" value="7 Working Days" placeholder="e.g. 7 Working Days" required>
                        </div>
                        <div class="col-md-6 d-flex align-items-center pt-4">
                            <div class="form-check form-switch font-size-15">
                                <input class="form-check-input" type="checkbox" name="is_featured" id="add_is_featured" value="1">
                                <label class="form-check-label" for="add_is_featured">
                                    Feature this plan on the homepage
                                </label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Brief summary of the plan strategy and allocation..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Features List (One feature per line)</label>
                            <textarea name="features" class="form-control" rows="4" placeholder="7 Working Days Duration&#10;Minimum Deposit: $1,000&#10;Instant Profit Withdrawal&#10;Capital Protected"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_plan" class="btn btn-primary">Create Investment Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/footer.php'; ?>
