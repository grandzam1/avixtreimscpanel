<?php 
include_once __DIR__ . '/header.php';

$msg = "";
$err = "";

// Handle user profile and display picture updates
if (isset($_POST['update_profile'])) {
    $new_name  = mysqli_real_escape_string($link, trim($_POST['fullname'] ?? ''));
    $new_phone = mysqli_real_escape_string($link, trim($_POST['phone'] ?? ''));

    // Handle profile photo upload
    $img_sql = "";
    if (isset($_FILES['avatar_file']) && $_FILES['avatar_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['avatar_file']['tmp_name'];
        $file_name = $_FILES['avatar_file']['name'];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        
        if (in_array($ext, $allowed)) {
            $dest_dir = __DIR__ . '/../images/avatars/';
            if (!is_dir($dest_dir)) {
                @mkdir($dest_dir, 0777, true);
            }
            $target_name = "user_" . intval($id) . "_" . time() . "." . $ext;
            $target_path = $dest_dir . $target_name;
            
            if (move_uploaded_file($file_tmp, $target_path)) {
                $db_path = "images/avatars/" . $target_name;
                $img_sql = ", image = '$db_path' ";
                $user_image = $db_path;
            } else {
                $err = "Could not save uploaded display picture. Please try again.";
            }
        } else {
            $err = "Invalid image file format. Allowed types: JPG, PNG, WEBP, GIF.";
        }
    }

    if (empty($err)) {
        ensure_column_exists($link, 'users', 'image', "VARCHAR(255) DEFAULT ''");
        // Withdrawal PIN (wpin) is strictly issued and managed by administrator. It cannot be altered by users.
        $q_upd = mysqli_query($link, "UPDATE users SET fullname = '$new_name', phone = '$new_phone' $img_sql WHERE id = '$id'");
        if ($q_upd) {
            $msg = "Profile details and display picture updated successfully!";
            $fullname = $new_name;
            $phone = $new_phone;
            $display_name = !empty($fullname) ? $fullname : $username;
        } else {
            $err = "Error updating profile: " . mysqli_error($link);
        }
    }
}

// Handle password change
if (isset($_POST['change_password'])) {
    $cur_pass = trim($_POST['cur_password'] ?? '');
    $new_pass = trim($_POST['new_password'] ?? '');
    $cfm_pass = trim($_POST['cfm_password'] ?? '');

    if ($cur_pass !== $password) {
        $err = "Current password does not match our records.";
    } elseif (strlen($new_pass) < 6) {
        $err = "New password must be at least 6 characters long.";
    } elseif ($new_pass !== $cfm_pass) {
        $err = "New passwords do not match.";
    } else {
        $n_pass = mysqli_real_escape_string($link, $new_pass);
        $q_pass = mysqli_query($link, "UPDATE users SET password = '$n_pass' WHERE id = '$id'");
        if ($q_pass) {
            $msg = "Password has been successfully changed!";
            $password = $new_pass;
        } else {
            $err = "Database error updating password.";
        }
    }
}

// Determine avatar preview
$current_avatar = "";
if (!empty($user_image)) {
    if (strpos($user_image, 'http') === 0) {
        $current_avatar = $user_image;
    } elseif (file_exists(__DIR__ . '/../' . $user_image)) {
        $current_avatar = '../' . $user_image;
    } elseif (file_exists(__DIR__ . '/' . $user_image)) {
        $current_avatar = $user_image;
    }
}
?>

<div style="max-width: 900px; margin: 0 auto;">

    <!-- Page Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 14px;">
        <div>
            <h2 style="font-weight: 800; font-size: 1.8rem; color: #fff; margin-bottom: 4px;">Account Settings & Profile</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0;">Manage your personal display picture, institutional profile credentials, and security PIN.</p>
        </div>
        <a href="customer.php" class="icon-btn-round" title="Back to Dashboard">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
    </div>

    <!-- Feedback Alerts -->
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

    <!-- Profile & Display Picture Card -->
    <div style="background: #141c17; border: 1px solid var(--card-border); border-radius: 28px; padding: 32px 28px; margin-bottom: 28px; box-shadow: 0 10px 30px rgba(0,0,0,0.4);">
        <form method="post" action="settings.php" enctype="multipart/form-data">
            
            <!-- Avatar Display & Live Preview Section -->
            <div style="display: flex; align-items: center; gap: 24px; margin-bottom: 28px; flex-wrap: wrap;">
                <div style="position: relative;">
                    <?php if (!empty($current_avatar)) { ?>
                        <img id="avatarPreview" src="<?php echo htmlspecialchars($current_avatar); ?>" alt="Display Picture" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary-accent); box-shadow: 0 6px 20px var(--primary-accent-glow);">
                    <?php } else { ?>
                        <div id="avatarFallback" style="width: 100px; height: 100px; border-radius: 50%; background: #202a23; color: var(--primary-accent); font-size: 2.4rem; font-weight: 800; display: flex; align-items: center; justify-content: center; border: 3px solid var(--primary-accent); box-shadow: 0 6px 20px var(--primary-accent-glow);">
                            <?php echo strtoupper(substr($display_name, 0, 1)); ?>
                        </div>
                        <img id="avatarPreview" src="" alt="Display Picture" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary-accent); box-shadow: 0 6px 20px var(--primary-accent-glow); display: none;">
                    <?php } ?>
                </div>

                <div style="flex: 1; min-width: 250px;">
                    <h4 style="font-weight: 800; color: #fff; margin-bottom: 4px;"><?php echo htmlspecialchars($display_name); ?></h4>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 12px;">Allowed image formats: JPG, PNG, WEBP, GIF. Max file size: 5MB.</p>
                    
                    <label style="display: inline-flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.15); color: #fff; padding: 10px 18px; border-radius: 14px; font-size: 0.86rem; font-weight: 700; cursor: pointer; transition: all 0.2s ease;">
                        <i class="fa-solid fa-camera" style="color: var(--primary-accent);"></i> Choose New Display Picture
                        <input type="file" name="avatar_file" id="avatarFileInput" accept="image/*" style="display: none;" onchange="previewAvatarImage(this)">
                    </label>
                    <span id="avatarFileName" style="font-size: 0.8rem; color: var(--text-muted); margin-left: 10px;"></span>
                </div>
            </div>

            <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.06); margin-bottom: 24px;">

            <!-- User Information Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 18px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #cbd5e1; margin-bottom: 7px;">Full Legal Name</label>
                    <div style="position: relative;">
                        <input type="text" name="fullname" value="<?php echo htmlspecialchars((string)($fullname ?? '')); ?>" style="width: 100%; background: #0b100d; border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 13px 16px; border-radius: 14px; font-size: 0.92rem; outline: none;" required>
                    </div>
                </div>

                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #cbd5e1; margin-bottom: 7px;">Phone Number</label>
                    <div style="position: relative;">
                        <input type="text" name="phone" value="<?php echo htmlspecialchars((string)($phone ?? '')); ?>" style="width: 100%; background: #0b100d; border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 13px 16px; border-radius: 14px; font-size: 0.92rem; outline: none;">
                    </div>
                </div>

                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #cbd5e1; margin-bottom: 7px;">Account Username (Read-Only)</label>
                    <div style="position: relative;">
                        <input type="text" value="<?php echo htmlspecialchars((string)($username ?? '')); ?>" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); color: #94a3b8; padding: 13px 16px; border-radius: 14px; font-size: 0.92rem; outline: none;" readonly>
                    </div>
                </div>

                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #cbd5e1; margin-bottom: 7px;">Registered Email Address (Read-Only)</label>
                    <div style="position: relative;">
                        <input type="email" value="<?php echo htmlspecialchars((string)($uemail ?? '')); ?>" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); color: #94a3b8; padding: 13px 16px; border-radius: 14px; font-size: 0.92rem; outline: none;" readonly>
                    </div>
                </div>
            </div>

            <button type="submit" name="update_profile" style="background: var(--primary-accent); color: #000; font-weight: 800; font-size: 0.96rem; border: none; padding: 14px 28px; border-radius: 16px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 8px 24px var(--primary-accent-glow); transition: all 0.2s ease;">
                <i class="fa-solid fa-floppy-disk"></i> Save Profile & Display Picture
            </button>
        </form>
    </div>

    <!-- Security & Password Change Card -->
    <div style="background: #141c17; border: 1px solid var(--card-border); border-radius: 28px; padding: 32px 28px; margin-bottom: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.4);">
        <h3 style="font-weight: 800; font-size: 1.25rem; color: #fff; margin-bottom: 6px;"><i class="fa-solid fa-shield-halved" style="color: var(--primary-accent); margin-right: 8px;"></i> Security & Password Management</h3>
        <p style="font-size: 0.86rem; color: var(--text-muted); margin-bottom: 22px;">Ensure your account is protected with a secure password containing letters, numbers, and symbols.</p>

        <form method="post" action="settings.php">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 18px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #cbd5e1; margin-bottom: 7px;">Current Password</label>
                    <input type="password" name="cur_password" placeholder="••••••••••••" style="width: 100%; background: #0b100d; border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 13px 16px; border-radius: 14px; font-size: 0.92rem; outline: none;" required>
                </div>
                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #cbd5e1; margin-bottom: 7px;">New Password</label>
                    <input type="password" name="new_password" placeholder="Min. 6 characters" style="width: 100%; background: #0b100d; border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 13px 16px; border-radius: 14px; font-size: 0.92rem; outline: none;" required minlength="6">
                </div>
                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #cbd5e1; margin-bottom: 7px;">Confirm New Password</label>
                    <input type="password" name="cfm_password" placeholder="Re-type new password" style="width: 100%; background: #0b100d; border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 13px 16px; border-radius: 14px; font-size: 0.92rem; outline: none;" required minlength="6">
                </div>
            </div>

            <button type="submit" name="change_password" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: #fff; font-weight: 700; font-size: 0.92rem; padding: 13px 26px; border-radius: 16px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s ease;">
                <i class="fa-solid fa-key" style="color: var(--primary-accent);"></i> Update Password
            </button>
        </form>
    </div>

</div>

<script>
function previewAvatarImage(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        document.getElementById('avatarFileName').innerText = file.name;

        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('avatarPreview');
            const fallback = document.getElementById('avatarFallback');
            if (preview) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            if (fallback) {
                fallback.style.display = 'none';
            }
        };
        reader.readAsDataURL(file);
    }
}
</script>

<?php 
include_once __DIR__ . '/footer.php';
?>
