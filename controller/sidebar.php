            <!-- ========== Left Sidebar Start ========== -->
            <div class="vertical-menu">

                <div data-simplebar class="h-100">

                    <!--- Sidemenu -->
                    <div id="sidebar-menu">
                        <!-- Left Menu Start -->
                        <ul class="metismenu list-unstyled" id="side-menu">
                            <li class="menu-title" data-key="t-menu">Menu</li>

                            <li>
                                <a href="home.php">
                                    <i data-feather="home"></i>
                                    <span data-key="t-dashboard">Dashboard</span>
                                </a>
                            </li>

                            <li>
                                <a href="users.php">
                                    <i data-feather="users"></i>
                                    <span data-key="t-profile">Users</span>
                                </a>
                            </li>

                            <li>
                                <a href="kyc.php">
                                    <i data-feather="shield"></i>
                                    <span data-key="t-kyc">KYC Verification</span>
                                    <?php 
                                    if (isset($link) && $link) {
                                        $q_kyc_b = @mysqli_query($link, "SELECT COUNT(*) as c FROM kyc_applications WHERE status = 'PENDING'");
                                        if ($q_kyc_b && $kb_r = mysqli_fetch_assoc($q_kyc_b)) {
                                            if ((int)$kb_r['c'] > 0) {
                                                echo '<span class="badge rounded-pill bg-warning text-dark float-end">' . (int)$kb_r['c'] . '</span>';
                                            }
                                        }
                                    }
                                    ?>
                                </a>
                            </li>

                            <li>
                                <a href="mail-center.php">
                                    <i data-feather="mail"></i>
                                    <span data-key="t-mail">Mail Center</span>
                                    <?php 
                                    if (isset($link) && $link) {
                                        $q_badge = @mysqli_query($link, "SELECT COUNT(*) as c FROM support_threads WHERE status = 'user_replied'");
                                        if ($q_badge && $b_row = mysqli_fetch_assoc($q_badge)) {
                                            if ((int)$b_row['c'] > 0) {
                                                echo '<span class="badge rounded-pill bg-success float-end">' . (int)$b_row['c'] . '</span>';
                                            }
                                        }
                                    }
                                    ?>
                                </a>
                            </li>

                            <li>
                                <a href="notifications.php">
                                    <i data-feather="bell"></i>
                                    <span data-key="t-notifications">Send Notifications</span>
                                </a>
                            </li>

                            <li>
                                <a href="deposits.php">
                                    <i data-feather="layout"></i>
                                    <span data-key="t-dashboard">Deposits</span>
                                </a>
                            </li>

                            <li>
                                <a href="shares.php">
                                    <i data-feather="trending-up"></i>
                                    <span data-key="t-shares">Shares Orders</span>
                                    <?php 
                                    if (isset($link) && $link) {
                                        $q_sb = @mysqli_query($link, "SELECT COUNT(*) as c FROM shares_orders WHERE status = 'Pending'");
                                        if ($q_sb && $sb_r = mysqli_fetch_assoc($q_sb)) {
                                            if ((int)$sb_r['c'] > 0) {
                                                echo '<span class="badge rounded-pill bg-warning text-dark float-end">' . (int)$sb_r['c'] . '</span>';
                                            }
                                        }
                                    }
                                    ?>
                                </a>
                            </li>

                            <li>
                                <a href="withdrawals.php">
                                    <i data-feather="grid"></i>
                                    <span data-key="t-dashboard">Withdrawals</span>
                                </a>
                            </li>

                            <li>
                                <a href="plans.php">
                                    <i data-feather="layers"></i>
                                    <span data-key="t-dashboard">Investment Plans</span>
                                </a>
                            </li>

                            <li>
                                <a href="account.php">
                                    <i data-feather="users"></i>
                                    <span data-key="t-profile">Account</span>
                                </a>
                            </li>

                            <li>
                                <a href="settings.php">
                                    <i data-feather="settings"></i>
                                    <span data-key="t-profile">Settings</span>
                                </a>
                            </li>

                            <li>
                                <a href="logout.php">
                                    <i data-feather="log-out"></i>
                                    <span data-key="t-profile">Logout</span>
                                </a>
                            </li>

                        </ul>

                    </div>
                    <!-- Sidebar -->
                </div>
            </div>
            <!-- Left Sidebar End -->
