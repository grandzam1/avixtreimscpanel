<footer class="bg-dark">
    <!-- Start Footer Top -->
    <div class="footer-top bg-gray">
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-sm-4 logo">
                    <a href="index.php" style="display: flex; align-items: center; gap: 10px; text-decoration: none;">
                        <?php if (!empty($logo) && file_exists($logo)) { ?>
                            <img src="<?php echo htmlspecialchars($logo); ?>" alt="<?php echo htmlspecialchars($site_name); ?>" style="max-height: 40px; width: auto; object-fit: contain;">
                        <?php } else { ?>
                            <span style="width: 14px; height: 14px; border-radius: 50%; background: var(--theme-accent, #ccff00); box-shadow: 0 0 10px var(--theme-accent, #ccff00);"></span>
                            <span style="font-size: 1.5rem; font-weight: 800; color: #fff;"><?php echo htmlspecialchars($site_name); ?></span>
                        <?php } ?>
                    </a>
                </div>
                <div class="col-md-8 col-sm-8 form text-right">
                    <form action="#">
                        <div class="input-group stylish-input-group">
                            <input type="email" placeholder="Enter your e-mail here" class="form-control" name="email">
                            <span class="input-group-addon">
                                <button type="submit" style="background: var(--theme-accent, #ccff00); color: #000; font-weight: bold; border: none; padding: 6px 16px; border-radius: 4px;">
                                    Subscribe
                                </button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Footer Top -->
    <div class="container">
        <div class="row">
            <div class="f-items default-padding">

                <!-- Single Item -->
                <div class="col-md-4 equal-height item">
                    <div class="f-item">
                        <h4>About Us</h4>
                        <p>
                            <?php echo htmlspecialchars($site_name); ?> is an independent, high-yield digital asset trading and wealth management firm. We provide institutional-grade financial execution, multi-asset security, and intelligent market participation.
                        </p>
                    </div>
                </div>
                <!-- End Single Item -->
                <!-- Single Item -->
                <div class="col-md-4 equal-height item">
                    <div class="f-item link">
                        <h4>Services</h4>
                        <ul>
                            <li><a href="register.php">Cryptocurrency Trading</a></li>
                            <li><a href="register.php">Automated Portfolio</a></li>
                            <li><a href="register.php">Multi-Asset Wallets</a></li>
                            <li><a href="register.php">Institutional Liquidity</a></li>
                            <li><a href="register.php">VIP Escrow Protection</a></li>
                        </ul>
                    </div>
                </div>
                <!-- End Single Item -->
                <!-- Single Item -->
                <div class="col-md-4 item">
                    <div class="f-item ">
                        <h4>Contact Details</h4>
                        <ul>
                            <li class="mb-3">
                                <span><i class="fa-solid fa-envelope me-2" style="color: var(--theme-accent, #ccff00);"></i> <a href="mailto:<?php echo htmlspecialchars($email); ?>"><?php echo htmlspecialchars($email); ?></a></span>
                            </li>
                            <li class="mb-3">
                                <span><i class="fa-solid fa-location-dot me-2" style="color: var(--theme-accent, #ccff00);"></i> <a href="#"><strong><?php echo htmlspecialchars($address); ?></strong></a></span>
                            </li>
                            <?php if (!empty($phone)) { ?>
                            <li class="mb-3">
                                <span><i class="fa-solid fa-phone me-2" style="color: var(--theme-accent, #ccff00);"></i> <a href="tel:<?php echo htmlspecialchars($phone); ?>"><?php echo htmlspecialchars($phone); ?></a></span>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
                <!-- End Single Item -->
            </div>
        </div>
    </div>
    <!-- Start Footer Bottom -->
    <div class="footer-bottom bg-dark text-light">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; Copyright <?php echo date('Y'); ?>. All Rights Reserved by <a href="/" style="color: var(--theme-accent, #ccff00);"><?php echo htmlspecialchars($site_name); ?></a></p>
                </div>
                <div class="col-md-6 text-right link">
                    <ul>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms & Conditions</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Dynamic WhatsApp Floating Button (Connected to Admin) -->
<?php 
$wa_num = !empty($whatsapp) ? $whatsapp : (!empty($phone) ? $phone : '15552345678');
$clean_wa_footer = preg_replace('/[^0-9]/', '', $wa_num);
if (empty($clean_wa_footer)) {
    $clean_wa_footer = '15552345678';
}
?>
<a href="https://wa.me/<?php echo $clean_wa_footer; ?>?text=Hello%20Admin,%20I%20have%20an%20inquiry%20regarding%20my%20account." target="_blank" style="position: fixed; bottom: 25px; right: 25px; width: 55px; height: 55px; border-radius: 50%; background: #25d366; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; box-shadow: 0 6px 20px rgba(37, 211, 102, 0.4); z-index: 9999; text-decoration: none;" title="Chat Directly with Admin on WhatsApp">
    <i class="fa-brands fa-whatsapp"></i>
</a>
