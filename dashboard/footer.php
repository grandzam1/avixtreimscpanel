    </main>
</div> <!-- end .app-container -->

<!-- Dynamic WhatsApp Floating Pill (Connected to Admin) -->
<?php 
$wa_active = !empty($whatsapp) ? $whatsapp : (!empty($phone) ? $phone : '15552345678');
$clean_wa_dash = preg_replace('/[^0-9]/', '', $wa_active);
if (empty($clean_wa_dash)) {
    $clean_wa_dash = '15552345678';
}
?>
<a href="https://wa.me/<?php echo $clean_wa_dash; ?>?text=Hello%20Admin,%20I%20have%20an%20inquiry%20regarding%20my%20dashboard." target="_blank" style="position: fixed; bottom: 25px; right: 25px; width: 55px; height: 55px; border-radius: 50%; background: #25d366; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; box-shadow: 0 6px 20px rgba(37, 211, 102, 0.4); z-index: 998; text-decoration: none;" title="Chat with Admin on WhatsApp">
    <i class="fa-brands fa-whatsapp"></i>
</a>

<script>
// Eye toggle balance privacy function
let balanceVisible = true;
function toggleBalancePrivacy() {
    const balEl = document.getElementById('userMainBalance');
    const eyeEl = document.getElementById('balanceEyeIcon');
    if (!balEl) return;
    
    if (balanceVisible) {
        balEl.dataset.actual = balEl.innerText;
        balEl.innerText = "$••••••••";
        if (eyeEl) {
            eyeEl.classList.remove('fa-eye');
            eyeEl.classList.add('fa-eye-slash');
        }
        balanceVisible = false;
    } else {
        balEl.innerText = balEl.dataset.actual || "$<?php echo number_format($total_balance, 2); ?>";
        if (eyeEl) {
            eyeEl.classList.remove('fa-eye-slash');
            eyeEl.classList.add('fa-eye');
        }
        balanceVisible = true;
    }
}

// Notification Dropdown Touch & Click Handler
document.addEventListener('DOMContentLoaded', function() {
    const wrappers = document.querySelectorAll('.notification-dropdown-wrapper');
    wrappers.forEach(wrap => {
        const bell = wrap.querySelector('.notification-bell-btn');
        if (bell) {
            bell.addEventListener('touchstart', function(e) {
                if (!wrap.classList.contains('active-menu')) {
                    e.preventDefault();
                    document.querySelectorAll('.notification-dropdown-wrapper.active-menu').forEach(w => {
                        if (w !== wrap) w.classList.remove('active-menu');
                    });
                    wrap.classList.add('active-menu');
                }
            }, {passive: false});
        }
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.notification-dropdown-wrapper')) {
            document.querySelectorAll('.notification-dropdown-wrapper.active-menu').forEach(w => {
                w.classList.remove('active-menu');
            });
        }
    });
});
</script>

<!-- Native Mobile App Bottom Dock Navigation -->
<nav class="pwa-mobile-dock" aria-label="Mobile App Navigation">
    <a href="customer.php" class="pwa-dock-item <?php echo ($current_page == 'customer.php') ? 'active' : ''; ?>">
        <i class="fa-solid fa-chart-pie"></i>
        <span>Home</span>
    </a>
    <a href="plans.php" class="pwa-dock-item <?php echo ($current_page == 'plans.php') ? 'active' : ''; ?>">
        <i class="fa-solid fa-layer-group"></i>
        <span>Plans</span>
    </a>
    <a href="deposit.php" class="pwa-dock-item <?php echo ($current_page == 'deposit.php') ? 'active' : ''; ?>">
        <i class="fa-solid fa-circle-down"></i>
        <span>Deposit</span>
    </a>
    <a href="withdraw.php" class="pwa-dock-item <?php echo ($current_page == 'withdraw.php') ? 'active' : ''; ?>">
        <i class="fa-solid fa-wallet"></i>
        <span>Withdraw</span>
    </a>
    <a href="navigation.php" class="pwa-dock-item <?php echo ($current_page == 'navigation.php') ? 'active' : ''; ?>">
        <i class="fa-solid fa-bars"></i>
        <span>Menu</span>
    </a>
</nav>

<!-- Progressive Web App Controller -->
<script src="../pwa.js"></script>

</body>
</html>
