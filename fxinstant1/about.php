<?php include("front-config.php"); ?>
<!DOCTYPE html5>
<html lang="en">

<meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->
<meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description"
        content="<?php echo $site_name; ?>  allows you to actively trade most popular crytocurrencies such as Bitcoin, Ethereum, Ripple, Litecoin and more, profit from market rallies and declines, or hedge your existing crytocurrency holdings." />

    <link rel="shortcut icon" href="https://vuestreims.com/assets/home/front-1/img/favicon.png" type="image/x-icon">
    <link rel="icon" href="https://vuestreims.com/assets/home/front-1/img/favicon.png" type="image/x-icon" />

    <meta property="og:title" content="Trade with <?php echo $site_name; ?> " />
    <meta property="og:description"
        content="<?php echo $site_name; ?>  allows you to actively trade most popular crytocurrencies such as Bitcoin, Ethereum, Ripple, Litecoin and more, profit from market rallies and declines, or hedge your existing crytocurrency holdings." />
    <meta property="og:image"
        content="born2invest.com/wp-content/uploads/2019/03/Benefits-and-risks-of-using-a-true-ECN-forex-broker-820x550.php" />
    <link rel="search" type="application/opensearchdescription+xml" href="#"
        title="Forex with <?php echo $site_name; ?>  - Forex trading with a reliable broker." />
    <link rel="alternate" type="application/rss+xml"
        title="Forex with<?php echo $site_name; ?>  - Forex trading with a reliable broker." href="#" />
    <title><?php echo $site_name; ?> - Bitcoin Trading - 100x Leverage </title>

    <!-- Scripts -->
    <script src="https://vuestreims.com/assets/home/front-1/js/app.js" defer></script>

    <style>
    [wire\:loading] {
        display: none;
    }

    [wire\:offline] {
        display: none;
    }

    [wire\:dirty]:not(textarea):not(input):not(select) {
        display: none;
    }
    </style>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com/">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="https://vuestreims.com/assets/home/front-1/css/app.css" rel="stylesheet" />

    <!-- ========== Start Stylesheet ========== -->
    <link href="https://vuestreims.com/assets/home/front-1/css/styles.css" rel="stylesheet" />
    <script src="ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>

    <!-- ========== End Stylesheet ========== -->
    <style>
    .no-js #loader {
        display: none;
    }

    .js #loader {
        display: block;
        position: absolute;
        left: 100px;
        top: 0;
    }

    .le-pre-con {
        position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 9999;
        background: url(https://vuestreims.com/assets/home/front-1/img/xxc.gif) center no-repeat #0b0b0b;
    }

    .brand-logo {
        display: flex;
        align-items: center;
    }

    .brand-items {
        display: flex;
        flex-direction: column;
        gap: 5px;
        margin-left: -1.5rem;
    }

    .brand-items span {
        font-size: 3rem;
        font-weight: 900;
        color: #3E6B76;
    }

    .brand-items span:first-child {
        color: royalblue !important
    }

    .brand-items span:nth-child(2) {
        color: red !important
    }

    @media only screen and (max-width:950px) {

        .brand-logo {
            display: flex;
            align-items: center;
        }

        .brand-items span {
            font-size: 1.3rem !important;
        }

        .brand-items {
            display: flex;
            flex-direction: column;
            gap: 0;
            margin-left: 0
        }
    }
    </style>

    <script>
    //paste this code under head tag or in a seperate js file.
    // Wait for window load
    $(window).load(function() {
        // Animate loader off screen
        setTimeout(function() {
            $(".le-pre-con").fadeOut('slow', function() {});
        }, 2000); // set the time here
        // $(".le-pre-con").fadeOut("slow");
    });
    </script>

</head>

<body style="position:relative;">
    <style>
    .btcwdgt-headlines .btcwdgt-header,
    .btcwdgt-headlines .btcwdgt-footer {
        display: none !important;
    }

    .btcwdgt {
        display: block !important;
        background-color: #111724 !important;
        color: #aaa !important;
        width: auto !important;
        min-width: 240px !important;
        max-width: 640px !important;
        margin: unset !important;
        box-shadow: unset !important;
        font-size: 16px !important;
        font-family: 'gilroy', Helvetica, Arial, sans-serif !important;
        font-weight: 300 !important;
        line-height: 1.0em !important;
    }

    @media(max-width:767px) {
        .btcwdgt-headlines .btcwdgt-body .price span {
            font-size: 0.7em !important;
        }

        .btcwdgt-headlines .btcwdgt-body .price .p-wrap p {
            font-size: 1.1em !important;
        }

        .btcwdgt-headlines .btcwdgt-body .price span {
            display: block !important;
        }
    }

    @media(min-width:768px) {
        .btcwdgt-headlines .btcwdgt-body {
            margin: unset !important;
            padding: 1.1em 0 !important;
            overflow: hidden !important;
            position: relative !important;
        }
    }

    @media (min-width: 1024px) {
        nav.navbar.bootsnav ul.nav>li>a:hover {
            color: #FF5722;
        }

        nav.navbar.bootsnav.sticked .navbar-brand {
            padding: 15px;
            max-width: 200px;
        }
    }

    .invalid-feedback {
        color: red;
    }

    .btn-theme {
        background-color: #FF5722;
        border: 2px solid #FF5722;
        border-radius: 15px;
    }

    .about-area .about-info li i,
    .about-area .about-items .item i {
        color: #FF5722;
    }

    .pricing-area .pricing-item {
        border: 1px solid #FF5722;
        border-radius: 10px;
    }

    .pricing-area .pricing-item.active li.pricing-header::after {
        background: none 0px 0px repeat scroll #FF5722;
    }

    .achivement-area .achivement-items .fun-fact .timer {
        color: #FF5722;
    }

    .about-area .author .info b {
        color: #FF5722;
    }

    .about-area .management-quote .management-items .owl-dots .owl-dot.active span {
        background: none 0px 0px repeat scroll #FF5722;
    }

    .footer-top .form form .input-group-addon button {
        background: none 0px 0px repeat scroll #FF5722;
    }

    .banner-area .content h1 span {
        color: #FF5722;
    }

    .breadcrumb-area .breadcrumb li.active {
        color: #FF5722;
    }

    .breadcrumb-area .breadcrumb li.active {
        color: #FF5722;
    }

    .site-heading h2::before {
        background: #FF5722 none repeat scroll 0 0;
    }

    .contact-area .address-items li .icon i {
        color: #FF5722;
    }

    .contact-area .contact-form p::after {
        background: #FF5722 none repeat scroll 0 0;
    }

    .contact-area .contact-form button {
        background: #FF5722 none repeat scroll 0 0;
        border-radius: 15px;
    }

    .btn-theme.effect:focus,
    .btn-theme.effect:hover {
        background: #FF5722 none repeat scroll 0 0;
        border: 2px solid #FF5722;
    }

    form.white-popup-block button[type=submit] {
        background: #FF5722 none repeat scroll 0 0;
    }

    .text-primary {
        color: #FF5722 !important;
    }

    .float {
        position: fixed;
        width: 60px;
        height: 60px;
        bottom: 40px;
        right: 40px;
        background-color: #25d366;
        color: #FFF;
        border-radius: 50px;
        text-align: center;
        font-size: 30px;
        box-shadow: 2px 2px 3px #999;
        z-index: 100;
    }

    .my-float {
        margin-top: 16px;
    }
    </style>

    <!-- Preloader Start 
    <div class="le-pre-con"></div>
    Preloader Ends -->

   <?php include("header.php"); ?>
   
    <style>
    .pricingTable {
        color: #fff;
        background: #212121;
        font-family: 'Open Sans', sans-serif;
        text-align: center;
        padding: 0 0 30px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    }

    .pricingTable .pricingTable-header {
        background: linear-gradient(to right, #BF557B, #8B2D6A);
        padding: 35px 10px 55px;
    }

    .pricingTable .price-value .currency {
        font-size: 60px;
        font-weight: 700;
        line-height: 60px;
        display: inline-block;
    }

    .pricingTable .price-value .amount {
        font-size: 80px;
        font-weight: 700;
        line-height: 80px;
        display: inline-block;
    }

    .pricingTable .price-value .month {
        font-size: 25px;
        font-weight: 400;
        opacity: 0.8;
        display: block;
    }

    .pricingTable .title {
        background: linear-gradient(to right, #A14566, #6C2652);
        font-size: 35px;
        font-weight: 600;
        text-transform: capitalize;
        letter-spacing: 1px;
        padding: 10px 20px;
        margin: -50px 0 35px;
        display: inline-block;
    }

    .pricingTable .pricing-content {
        padding: 0 30px;
        margin: 0 0 25px;
        list-style: none;
    }

    .pricingTable .pricing-content li {
        font-size: 17px;
        font-weight: 500;
        line-height: 20px;
        text-transform: capitalize;
        padding-bottom: 15px;
        margin: 0 0 15px;
        border-bottom: 2px solid #595959;
    }

    .pricingTable .pricing-content li:last-child {
        margin: 0;
        border: none;
    }

    .pricingTable .pricingTable-signup a {
        color: #fff;
        background: linear-gradient(to right, #BF557B, #8B2D6A);
        font-size: 20px;
        font-weight: 600;
        text-transform: capitalize;
        padding: 13px 0;
        margin: 0 30px;
        display: block;
        position: relative;
        z-index: 1;
        transition: all 0.3s;
    }

    .pricingTable .pricingTable-signup a:before {
        content: '';
        background-color: #212121;
        position: absolute;
        left: 2px;
        right: 2px;
        top: 2px;
        bottom: 2px;
        z-index: -1;
        transition: all 0.3s ease 0s
    }

    .pricingTable .pricingTable-signup a:hover:before {
        transform: scaleX(0);
    }

    .pricingTable .pricingTable-signup a:hover {
        text-shadow: 5px 5px 0 rgba(0, 0, 0, 0.3);
        box-shadow: 0 0 10px #000;
    }

    .pricingTable.green .pricingTable-header,
    .pricingTable.green .pricingTable-signup a {
        background: linear-gradient(to right, #6CB3A4, #498998);
    }

    .pricingTable.green .title {
        background: linear-gradient(to right, #509288, #3E6B76);
    }

    .pricingTable.golden .pricingTable-header,
    .pricingTable.golden .pricingTable-signup a {
        background: linear-gradient(to right, #E99B22, #D77C2B);
    }

    .pricingTable.golden .title {
        background: linear-gradient(to right, #B98320, #AC6116);
    }

    @media only screen and (max-width: 990px) {
        .pricingTable {
            margin-bottom: 40px;
        }
    }
    </style>
<div class="breadcrumb-area shadow dark bg-fixed text-center text-light"
    style="background-image: url('https://vuestreims.com/assets/home/front-1/images/woman-suit.jpg');">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <h1>About Us</h1>
                <ul class="breadcrumb">
                    <li><a href="/"><i class="fas fa-home"></i> Home</a></li>
                    <li class="active">About</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- End Breadcrumb -->

<!-- Start Our About
    ============================================= -->
<div class="about-area inc-video default-padding">
    <div class="container">
        <div class="row">
            <!-- Start About -->
            <div class="about-content">
                <div class="col-md-12 info">
                    <h2>About Us</h2>
                    <p>
                       <?php echo $site_name; ?> is an independent, privately-owned fee-only investment management firm based in
                        Flatiron District, New York City since 2008 with offices in Turkey, Russia, Australia and Dubai
                        UAE. We provide an unusual combination of financial planning and investment management
                        experience, the convenience of a full-service financial resource, and the benefits of being an
                        independent firm clearly aligned with our clients’ interests.
                    </p>
                    <p>
                       <?php echo $site_name; ?> offers long term and short term investment opportunities, The firm started by
                        trading stocks, shares and bonds, it got involved in forex trading in 2010 and was among the
                        pioneer traders of Crypto-currencies in the advent of Bitcoin in 2010.
                    </p>
                    <p>
                       <?php echo $site_name; ?> is a service-intensive firm. We make every effort to understand each client’s
                        specific needs and tailor our activities accordingly. Clients receive monthly statements from an
                        unaffiliated custodian and have 24/7 online access to investor information through our website.
                        In addition, clients always have direct access to our principals. We meet with clients regularly
                        and respond promptly to inquiries.
                    </p>
                    <p>We invest client capital in a blend of active and passive investment strategies in an effort to
                        earn positive returns in even the most challenging markets. Passive investments include exchange
                        traded funds (ETFs) and broadly diversified mutual funds which track the performance of various
                        market indices; active investments include more concentrated stock and bond portfolios, managed
                        by investment professionals that have consistently outperformed the market. We use proprietary
                        databases, independent third-party research and our own due diligence to evaluate individual
                        fund managers based on such criteria as performance, turnover, fees and risk profile.</p>
                    <p>Employing both active and passive investment strategies is only one method we use to reduce
                        overall portfolio volatility. We also mitigate risk by diversifying client investments by asset
                        class (equities, fixed income and cash), investment style (growth, value and blend) and
                        capitalization size (large cap, mid cap, and small cap), balanced with investments in
                        international equities, real estate investment trusts (REITs), commodities, private equity and
                        hedge funds. At<?php echo $site_name; ?>, we believe risk management is just as important as growing
                        capital.</p>
                    <ul>
                </div>
            </div>
            <!-- End About -->
        </div>
    </div>
</div>
<!-- End Our About -->

<div class="clients-area bg-dark default-padding">
    <div class="container">
        <div class="row">
            <div class="clients-items owl-carousel owl-theme text-center">
                <div class="single-item">
                    <a href="#"><img src="https://vuestreims.com/assets/home/front-1/img/clients/01.png" alt="Clients"></a>
                </div>
                <div class="single-item">
                    <a href="#"><img src="https://vuestreims.com/assets/home/front-1/img/clients/02.png" alt="Clients"></a>
                </div>
                <div class="single-item">
                    <a href="#"><img src="https://vuestreims.com/assets/home/front-1/img/clients/03.png" alt="Clients"></a>
                </div>
                <div class="single-item">
                    <a href="#"><img src="https://vuestreims.com/assets/home/front-1/img/clients/04.html" alt="Clients"></a>
                </div>
                <div class="single-item">
                    <a href="#"><img src="https://vuestreims.com/assets/home/front-1/img/clients/05.png" alt="Clients"></a>
                </div>
                <div class="single-item">
                    <a href="#"><img src="https://vuestreims.com/assets/home/front-1/img/clients/06.png" alt="Clients"></a>
                </div>
                <div class="single-item">
                    <a href="#"><img src="https://vuestreims.com/assets/home/front-1/img/clients/07.png" alt="Clients"></a>
                </div>
                <div class="single-item">
                    <a href="#"><img src="https://vuestreims.com/assets/home/front-1/img/clients/08.png" alt="Clients"></a>
                </div>
                <div class="single-item">
                    <a href="#"><img src="https://vuestreims.com/assets/home/front-1/img/clients/01.png" alt="Clients"></a>
                </div>
                <div class="single-item">
                    <a href="#"><img src="https://vuestreims.com/assets/home/front-1/img/clients/04.html" alt="Clients"></a>
                </div>
            </div>
        </div>
    </div>
</div>
<footer class="bg-dark">
    <!-- Start Footer Top -->
    <div class="footer-top bg-gray">
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-sm-4 logo">
                    <a href="/"><img src="https://vuestreims.com/assets/home/front-1/img/logo.png" alt="Logo"></a>
                </div>
                <div class="col-md-8 col-sm-8 form text-right">
                    <form action="#">
                        <div class="input-group stylish-input-group">
                            <input type="email" placeholder="Enter your e-mail here" class="form-control" name="email">
                            <span class="input-group-addon">
                                <button type="submit">
                                    Subscribe <i class="fa fa-paper-plane"></i>
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
                        <h4>About</h4>
                        <p>
                            <?php echo $site_name; ?> is an independent, privately-owned fee-only investment management
                            firm based in Flatiron District, New york since 2008 with offices in Turkey, Russia,
                            Australia and Dubai UAE. We provide an unusual combination of financial planning and
                            investment management experience, the convenience of a full-service financial resource,
                            and the benefits of being an independent firm clearly aligned with our clients’
                            interests.
                        </p>
                    </div>
                </div>
                <!-- End Single Item -->
                <!-- Single Item -->
                <div class="col-md-4 equal-height item">
                    <div class="f-item link">
                        <h4>Services</h4>
                        <ul>
                            <li>
                                <a href="cryptocurrencies.php">Cryptocurrencies</a>
                            </li>
                            <li>
                                <a href="escrow.php">Escrow service</a>
                            </li>
                            <li>
                                <a href="loans.php">Loans</a>
                            </li>
                            <li>
                                <a href="forex.php">Forex</a>
                            </li>
                            <li>
                                <a href="nfp.php">NFP</a>
                            </li>


                        </ul>
                    </div>
                </div>
                <!-- End Single Item -->
                <!-- Single Item -->
                <div class="col-md-4 item">
                    <div class="f-item ">
                        <h4>Contacts</h4>
                        <ul>
                            <li class="mb-3">
                                <span><img class="mr-3" width="20" height="20"
                                        src="https://vuestreims.com/assets/home/front-1/svg/envelope.svg" /><a
                                        href="#"><?php echo $email; ?></a></span>
                            </li>
                            <li class="mb-3">
                                <span><img class="mr-3" width="20" height="20"
                                        src="https://vuestreims.com/assets/home/front-1/svg/address.svg" /><a href="#"><strong><?php echo $address; ?></a></span>
                            </li>
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
                    <p>&copy; Copyright
                        <script>
                        document.write(new Date().getFullYear())
                        </script>. All Rights Reserved by <a href="/"><?php echo $site_name; ?></a>
                    </p>
                </div>
                <div class="col-md-6 text-right link">
                    <ul>
                        <li>
                            <a href="#">Company Policies</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- End Footer -->
<script src="https://vuestreims.com/assets/home/front-1/js/all.js"></script>
<!-- <script src="https://directoptionmining.com/assets/home/front-1/preloader.js"></script> -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>

<script type="text/javascript">
function iframeLoaded() {
    var iFrameID = document.getElementById('iFFF');

    if (iFrameID) {
        // here you can make the height, I delete it first, then I make it again
        iFrameID.height = "";
        iFrameID.height = iFrameID.contentWindow.document.body.scrollHeight + "px";
    }
}
</script>

<script>
(function(b, i, t, C, O, I, N) {
    window.addEventListener('load', function() {
        if (b.getElementById(C)) return;
        I = b.createElement(i), N = b.getElementsByTagName(i)[0];
        I.src = t;
        I.id = C;
        N.parentNode.insertBefore(I, N);
    }, false)
})(document, 'script', 'widgets.bitcoin.com/widget.js', 'btcwdgt');
</script>

<script>
$("#clicker").on('click', function() {
    window.location = "http://www.google.com/";
});
</script>

</body>


</html>