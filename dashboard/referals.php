<?php 
include_once __DIR__ . '/header.php';
?>

<div style="max-width: 850px; margin: 0 auto;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h2 style="font-weight: 800; font-size: 1.8rem; color: #fff; margin-bottom: 4px;">Affiliate & Referrals</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0;">Invite friends and earn up to <?php echo htmlspecialchars($ref_bonus); ?>% instant commission on every investment.</p>
        </div>
        <a href="customer.php" class="icon-btn-round" title="Back to Dashboard">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
    </div>

    <!-- Referral Stats Grid -->
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 28px;">
        <div style="background: #141c17; border: 1px solid var(--card-border); border-radius: 20px; padding: 20px; text-align: center;">
            <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 6px;">Total Referrals</div>
            <div style="font-size: 2rem; font-weight: 800; color: #fff;"><?php echo $refals; ?></div>
        </div>

        <div style="background: #141c17; border: 1px solid var(--card-border); border-radius: 20px; padding: 20px; text-align: center;">
            <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 6px;">Active Partners</div>
            <div style="font-size: 2rem; font-weight: 800; color: var(--primary-accent);"><?php echo $refals; ?></div>
        </div>

        <div style="background: #141c17; border: 1px solid var(--card-border); border-radius: 20px; padding: 20px; text-align: center;">
            <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 6px;">Earned Commission</div>
            <div style="font-size: 2rem; font-weight: 800; color: #fff;">$<?php echo number_format($refbalance, 2); ?></div>
        </div>
    </div>

    <!-- Referral Link Box -->
    <div style="background: #141c17; border: 1px solid var(--card-border); border-radius: 28px; padding: 28px; margin-bottom: 28px;">
        <h4 style="font-weight: 800; color: #fff; margin-bottom: 8px;">Your Unique Affiliate Link</h4>
        <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 18px;">Share this link to automatically track new registrations directly under your affiliate network.</p>

        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <input type="text" id="affiliateLinkInput" readonly value="<?php echo htmlspecialchars($siteurl); ?>/register.php?ref=<?php echo htmlspecialchars(strtolower($username)); ?>" style="flex: 1; min-width: 260px; background: #0c120f; border: 1px solid var(--card-border); color: #fff; padding: 14px 18px; border-radius: 16px; font-size: 0.95rem; font-weight: 600; outline: none;">
            <button type="button" onclick="copyAffiliateLink()" style="background: var(--primary-accent); color: #000; border: none; font-weight: 800; padding: 14px 24px; border-radius: 16px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                <i class="fa-regular fa-copy"></i> Copy Link
            </button>
        </div>
    </div>

</div>

<script>
function copyAffiliateLink() {
    const linkInput = document.getElementById('affiliateLinkInput');
    linkInput.select();
    navigator.clipboard.writeText(linkInput.value).then(() => {
        swal('Copied!', 'Affiliate link copied to clipboard.', 'success');
    }).catch(() => {
        document.execCommand('copy');
        swal('Copied!', linkInput.value, 'success');
    });
}
</script>

<?php 
include_once __DIR__ . '/footer.php';
?>
