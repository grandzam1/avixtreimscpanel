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
        title="Forex with <?php echo $site_name; ?>  - Forex trading with a reliable broker." href="#" />
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-Z+PhnjjAv6zXzQ7Kj9dfBBpBqKs8g1k6fDFIXfI9CRCEEt37cLJJjLZu+mBNMEQkLCnZwKjzUw9y/h15/6lT9Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />

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
<div id="particles-js"></div>
<div class="banner-area">
    <div id="bootcarousel" class="carousel inc-top-heading slide carousel-fade animate_text" data-ride="carousel">
        <!-- Wrapper for slides -->
        <div class="carousel-inner text-light carousel-zoom">
            <div class="item active">
                <div class="slider-thumb bg-fixe" style="background-image: url('https://vuestreims.com/assets/home/front-1/images/hand.jpg');">
                </div>
                <div class="box-table shadow dark">
                    <div class="box-cell">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="content">
                                        <h1 class="header_h1" data-animation="animated slideInLeft ">We serve you
                                            <span>Better</span>
                                        </h1>
                                        <h3 class="header_h3" data-animation="animated slideInRight">We hire the
                                            best traders to serve you better</h3>
                                        <p>Avix Treims is an institutional digital asset infrastructure and algorithmic trading platform providing secure liquidity solutions, digital asset management, and high-performance financial systems.</p>
                                        <button data-animation="animated slideInUp" id="clicker" style="z-index: 9999"
                                            class="btn btn-theme effect btn-md" href="register.php">Learn
                                            More</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="slider-thumb bg-fixe"
                    style="background-image: url('https://vuestreims.com/assets/home/front-1/images/chart-hand.jpg');"></div>
                <div class="box-table shadow dark">
                    <div class="box-cell">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="content">
                                        <h1 class="header_h1" data-animation="animated slideInDown">Invest with a
                                            firm you can<br> <span>Trust</span></h1>
                                        <h3 class="header_h3" data-animation="animated slideInUp" class="text-orange">
                                            24/7 monitoring of your investment is assured</h3>
                                        <p>Avix Treims is an institutional digital asset infrastructure and algorithmic trading platform providing secure liquidity solutions, digital asset management, and high-performance financial systems.</p>
                                        <a data-animation="animated slideInUp" class="btn btn-theme effect btn-md"
                                            href="register.php">Learn More</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="slider-thumb bg-fixe"
                    style="background-image: url('https://vuestreims.com/assets/home/front-1/img/banner/3.jpg');"></div>
                <div class="box-table shadow dark">
                    <div class="box-cell">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="content">
                                        <h1 class="header_h1" data-animation="animated slideInLeft">More
                                            <span>convenient </span>than others
                                        </h1>
                                        <h3 class="header_h3" data-animation="animated slideInRight">Invest with us
                                            <span>Today</span><br>Let your money do the <span>hardwork.</span>
                                        </h3>
                                        <p>Avix Treims is an institutional digital asset infrastructure and algorithmic trading platform providing secure liquidity solutions, digital asset management, and high-performance financial systems.</p>
                                        <a data-animation="animated slideInUp" class="btn btn-theme effect btn-md"
                                            href="register.php">Learn More</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Wrapper for slides -->

        <!-- Left and right controls -->
        <a class="left carousel-control shadow" href="#bootcarousel" data-slide="prev">
            <i class="fa fa-angle-left"></i>
            <span class="sr-only">Previous</span>
        </a>
        <a class="right carousel-control shadow" href="#bootcarousel" data-slide="next">
            <i class="fa fa-angle-right"></i>
            <span class="sr-only">Next</span>
        </a>
    </div>

</div>
<!-- End Banner -->

<!-- Start Our About -->

<div class="about-area full-width inc-shadow mt default-padding bottom-less">
    <div class="container">
        <div class="row">
            <!-- Start About -->
            <div class="about-content content-left">
                <div class="col-md-6 info">

                    <h2>About Us</h2>
                    <p> <?php echo $site_name; ?> is an independent, privately-owned fee-only investment management firm based in
                        the Flatiron District, New York City. Since 2008, we have collaborated with leading quantitative research firms and financial institutions. We have expanded our global presence with offices in
                        Turkey, Russia, Australia, South Africa, the United States, Egypt, and Dubai, UAE. We provide a unique
                        blend of financial planning and asset management expertise, offering the convenience of a
                        full-service financial resource while maintaining the independence needed to align fully with
                        our clients' interests. </p>
                    <p> <?php echo $site_name; ?> offers both long-term and quantitative trading opportunities. The firm started
                        with trading stocks, shares, and bonds, moved into forex trading in 2010, and quickly adapted to
                        emerging markets, becoming one of the pioneers in digital asset management. Our technological infrastructure provides
                        secure liquidity routing and institutional portfolio execution across global financial markets. </p>

                    <a href="register.php" class="btn btn-theme effect btn-md"
                        data-animation="animated slideInUp">Open Live Account</a>

                </div>
                <div class="col-md-6 thumb">
                    <iframe width="350" height="250" src="https://www.youtube.com/embed/Gc2en3nHxA4"
                        title="YouTube video player" frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen></iframe>
                    <br /> <br />

                    <iframe width="350" height="250" src="https://www.youtube.com/embed/rrYs6xtRKrQ" frameborder="0"
                        allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen></iframe>
                </div>
            </div>
            <!-- End About -->

            <!-- Start Our Features -->
            <div class="col-md-12 text-center about-items">
                <h2>Why choose us</h2>

                <div class="row">
                  <!-- Single Item -->
<div class="col-md-3 col-sm-6 single-item">
    <div class="item text-center">
        <a href="#">
            <h5>Highly Secured</h5>
        </a>
    </div>
</div>
<!-- End Single Item -->

<!-- Single Item -->
<div class="col-md-3 col-sm-6 single-item">
    <div class="item text-center">
        <a href="#">
            <h5>HIGH ROI</h5>
        </a>
    </div>
</div>
<!-- End Single Item -->

<!-- Single Item -->
<div class="col-md-3 col-sm-6 single-item">
    <div class="item text-center">
        <a href="#">
            <h5>GUARANTEED RETURNS</h5>
        </a>
    </div>
</div>
<!-- End Single Item -->

<!-- Single Item -->
<div class="col-md-3 col-sm-6 single-item">
    <div class="item text-center">
        <a href="#">
            <h5>24/7 Live Support</h5>
        </a>
    </div>
</div>
<!-- End Single Item -->

                </div>
            </div>
            <!-- End Our Features -->
        </div>
    </div>
</div>
<!-- End Our About -->

<!-- Start Services  -->
<div class="services-inc-area half-bg carousel-shadow default-padding bg-gray">
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="site-heading text-light text-center">
                    <h2>Our Services</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <!-- Single Item -->

                    <div class="col-sm-4 col-md-4 mb-5">
                        <div class="item">
                            <div class="thumb">
                                <img src="https://vuestreims.com/assets/home/front-1/img/services/cryptocurrencies.jpg" alt="Thumb">
                                <div class="overlay">
                                    <a href="#">
                                       
                                        <h4>Cryptocurrencies</h4>
                                    </a>
                                </div>
                            </div>
                            <div class="info">
                                <p>
                                    Cryptocurrencies are sets of software protocols for generating digital tokens
                                    and for tracking transactions in a way that makes it hard to counterfeit or
                                    re-use tokens.
                                </p>
                               
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 col-md-4 mb-5">
                        <div class="item">
                            <div class="thumb">
                                <img src="https://vuestreims.com/assets/home/front-1/img/services/2.jpg" alt="Thumb">
                                <div class="overlay">
                                    <a href="#">
                                       
                                        <h4>Escrow Service</h4>
                                    </a>
                                </div>
                            </div>
                            <div class="info">
                                <p>
                                    Escrow comes into play when two parties are in the process of completing a
                                    transaction and there is uncertainty over whether one party or another will...
                                </p>
                               
                            </div>
                        </div>
                    </div>
                    <!-- End Single Item -->
                    <!-- Single Item -->
                    <div class="col-sm-4 col-md-4 mb-5">
                        <div class="item">
                            <div class="thumb">
                                <img src="https://vuestreims.com/assets/home/front-1/img/services/3.jpg" alt="Thumb">
                                <div class="overlay">
                                    <a href="#">
                                       
                                        <h4>Loans</h4>
                                    </a>
                                </div>
                            </div>
                            <div class="info">
                                <p>
                                    Getting a loan doesn't have to be <br>intimidating, with the right lender it can
                                    be a simple process. You only need a lender committed to ...
                                </p>
                                
                            </div>
                        </div>
                    </div>
                    <!-- End Single Item -->

                    <!-- Single Item -->
                    <div class="col-sm-4 col-md-4 col-md-offset-2 mb-5">
                        <div class="item">
                            <div class="thumb">
                                <img src="https://vuestreims.com/assets/home/front-1/img/services/5.jpg" alt="Thumb">
                                <div class="overlay">
                                    <a href="#">
                                       
                                        <h4>Forex</h4>
                                    </a>
                                </div>
                            </div>
                            <div class="info">
                                <p>
                                    Forex or FX is an acronym of Foreign Exchange and Forex trading means to trade
                                    on this market. Trading takes place by predicting how one currency performs
                                    against another one
                                </p>
                               
                            </div>
                        </div>
                    </div>
                    <!-- End Single Item -->
                    <!-- Single Item -->
                    <div class="col-sm-4 col-md-4 mb-5">
                        <div class="item">
                            <div class="thumb">
                                <img src="https://vuestreims.com/asset/home/front-1/img/services/1.html" alt="Thumb">
                                <div class="overlay">
                                    <a href="#">
                                       
                                        <h4>Non Farm Payroll [NFP]</h4>
                                    </a>
                                </div>
                            </div>
                            <div class="info">
                                <p>
                                    The non-farm payroll report causes one of the consistently largest rate
                                    movements of any news announcement in the forex market. As a result, many
                                    analysts,
                                </p>
                              
                            </div>
                        </div>
                    </div>
                    <!-- End Single Item -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Services -->


<!-- Start Pricing -->
<div class="services-inc-are half-bg carousel-shado  bg-gra">
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="site-heading text-center mb-0">
                    <h2>OUR AWESOME PLANS</h2>
                </div>
            </div>
        </div>
        <div id="pricing" class="pricing-area bottom-less">
            <div class="demo">
                <div class="container">

                    <div class="row">
                        <div class="col-md-4 col-sm-6">
                            <div class="pricingTable">
                                <div class="pricingTable-header">
                                    <div class="price-value">
                                        <span class="currency">$</span>
                                        <span class="amount">300</span>
                                        <span class="month">10% Weekly</span>
                                    </div>
                                </div>
                                <h3 class="title">Starter</h3>
                                <ul class="pricing-content">
                                    <li>7 Working Days</li>
                                    <li>Minimum: 1000</li>
                                    <li>Maximum: $4,999</li>
                                </ul>
                                <div class="pricingTable-signup">
                                    <a href="register.php">Choose Plan</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="pricingTable green">
                                <div class="pricingTable-header">
                                    <div class="price-value">
                                        <span class="currency">$</span>
                                        <span class="amount">5,000</span>
                                        <span class="month">15% Weekly</span>
                                    </div>
                                </div>
                                <h3 class="title">Standard</h3>
                                <ul class="pricing-content">
                                    <li>7 Working Days</li>
                                    <li>Minimum: $5,000</li>
                                    <li>Maximum: $19,999</li>
                                </ul>
                                <div class="pricingTable-signup">
                                    <a href="register.php">Choose Plan</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="pricingTable golden">
                                <div class="pricingTable-header">
                                    <div class="price-value">
                                        <span class="currency">$</span>
                                        <span class="amount">20,000</span>
                                        <span class="month">25% Weekly</span>
                                    </div>
                                </div>
                                <h3 class="title">Premium</h3>
                                <ul class="pricing-content">
                                    <li>35 Working Days</li>
                                    <li>NFP</li>
                                    <li>Minimum: $20,000</li>
                                    <li>Maximum: $49,999</li>
                                </ul>
                                <div class="pricingTable-signup">
                                    <a href="register.php">Choose Plan</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6">
                        <div class="pricingTable">
                            <div class="pricingTable-header">
                                <div class="price-value">
                                    <span class="currency">$</span>
                                    <span class="amount">50,000</span>
                                    <span class="month">30% Weekly</span>
                                </div>
                            </div>
                            <h3 class="title">Professional</h3>
                            <ul class="pricing-content">
                                <li>35 Working Days</li>
                                <li>NFP</li>
                                <li>Loan Qualification</li>
                                <li>Financial Adviser</li>
                                <li>Minimum: $50,000</li>
                                <li>Maximum: $299,999</li>
                            </ul>
                            <div class="pricingTable-signup">
                                <a href="register.php">Choose Plan</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="pricingTable green">
                            <div class="pricingTable-header">
                                <div class="price-value">
                                    <span class="currency">$</span>
                                    <span class="amount">300,000</span>
                                    <span class="month">35% Weekly</span>
                                </div>
                            </div>
                            <h3 class="title">Business</h3>
                            <ul class="pricing-content">
                                <li>35 Working Days</li>
                                <li>NFP</li>
                                <li>Loan Qualification</li>
                                <li>Financial Adviser</li>
                                <li>Minimum: $300,000</li>
                                <li>Maximum: $1,000,000</li>
                            </ul>
                            <div class="pricingTable-signup">
                                <a href="register.php">Choose Plan</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Pricing -->


<!-- Start Testimonial & Faq -->
<div class="testimonials-faq about-area default-padding">
    <div class="container">
        <div class="row">

            <!-- Start Testimonials -->
            <div class="col-md-6 management-quote">
                <h2>BITruist Officials</h2>
                <div class="management-items management-carousel owl-carousel owl-theme">
                    <!-- Single Item -->
                    <div class="item">
                        <p>
                            " Hungarian-born American billionaire, investor and philanthropist, Born: 12 August
                            1930."
                        </p>
                        <div class="author">
                            <div class="thumb">
                                <img src="https://vuestreims.com/assets/home/front-1/img/team/1.jpg" alt="Thumb">
                            </div>
                            <div class="info">
                                <span>- George Soros</span>
                                <b>Founder</b>
                            </div>
                        </div>
                    </div>
                    <!-- End Single Item -->
                    <!-- Single Item -->
                    <div class="item">
                        <p>
                            "Born in United Kingdom base in United state, she is well known for her kind gestures
                            and compitent, Born: 20 July 1985."
                        </p>
                        <div class="author">
                            <div class="thumb">
                                <img src="https://vuestreims.com/assets/home/front-1/img/team/2.jpg" alt="Thumb">
                            </div>
                            <div class="info">
                                <span>- Sarah Olivia Louis</span>
                                <b>CEO</b>
                            </div>
                        </div>
                    </div>
                    <!-- End Single Item -->
                </div>
            </div>
            <!-- End Testimonials -->

            <!-- Start Testimonials -->
            <div class="col-md-6 management-quote">
                <h2>What Clients say</h2>
                <div class="management-items management-carousel owl-carousel owl-theme">
                    <!-- Single Item -->
                    <div class="item">
                        <p>
                            " Hi,my name is Nkuli Tshirumbula From South Africa. This is really a good profit for a
                            small investment capital. I am so glad I invested with this company(Direct Option
                            Mining) thanks so much Sarah Olivia Louis and along side other Account Managers for
                            being truthful and guiding me all through my investment. Always keep the good work.."
                        </p>
                        <div class="author">
                            <div class="thumb">
                                <img src="https://vuestreims.com/assets/home/front-1/img/team/5.jpg" alt="Thumb">
                            </div>
                            <div class="info">
                                <span>- Nkuli Tshirumbula</span>
                            </div>
                        </div>
                    </div>
                    <!-- End Single Item -->
                    <!-- Single Item -->
                    <div class="item">
                        <p>
                            "Hello, I'm Sarah Mbofana from Johannesburg South Africa. I'm really speechless right
                            now seeing that I have got my profit in my Luno wallet, My heart is filled with joy and
                            happiness thank you so much for taking me from the low level of life for just 2 weeks
                            trade, I must tell my family and friends about your dynamic platform. Thank you once
                            more <?php echo $site_name; ?> company."
                        </p>
                        <div class="author">
                            <div class="thumb">
                                <img src="https://vuestreims.com/assets/home/front-1/img/team/6.jpg" alt="Thumb">
                            </div>
                            <div class="info">
                                <span>- Sarah Mbofana</span>
                            </div>
                        </div>
                    </div>
                    <!-- End Single Item -->
                    <!-- Single Item -->
                    <div class="item">
                        <p>
                            "My name is Joy Gray I'm happy married and Bless with kids and grandkids, Based in
                            Canada. I am really short of words right now. Don't actually know how am gonna
                            appreciate the great work this Alsome company <?php echo $site_name; ?> did for me by trading
                            my money and the returns comes out huge I just made a successful withdrawal to my bank
                            account with the help of this expert trader Sarah Olivia Louis. I pray God bless and
                            reward you for your great work of helping people around the world. Hoping for more
                            successful business."
                        </p>
                        <div class="author">
                            <div class="thumb">
                                <img src="https://vuestreims.com/assets/home/front-1/img/team/7.jpg" alt="Thumb">
                            </div>
                            <div class="info">
                                <span>- Joy Gray</span>
                            </div>
                        </div>
                    </div>
                    <!-- End Single Item -->
                    <!-- Single Item -->
                    <div class="item">
                        <p>
                            "Hey I'm John tubbs from Texas Untied state. Thanks be to God who made possible for me
                            to locate a truthful and a trustworthy bitcoin company (BITruist) whom made
                            it known to me that one can earn through binary options trading. Forever remain grateful
                            to this company and I want to use this means in saying sorry once again for doubting
                            this platform when I started also for having a bad intention over your platform. I’m
                            grateful to the entire team ( coworker's ) that put in great effect to ensure all
                            transactions are successful and to all investors that has been investing since the
                            existence of this great platform."
                        </p>
                        <div class="author">
                            <div class="thumb">
                                <img src="https://vuestreims.com/assets/home/front-1/img/team/8.jpg" alt="Thumb">
                            </div>
                            <div class="info">
                                <span>- John tubbs</span>
                            </div>
                        </div>
                    </div>
                    <!-- End Single Item -->
                    <!-- Single Item -->
                    <div class="item">
                        <p>
                            "My name is Annika Bandile Thanks to this wonderful platform <?php echo $site_name; ?> , Now
                            I fully understand why everyone keeps speaking good of this platform. Only God repay all
                            the teams for the good work to help other's.I'm really happy I didn't miss out on this
                            opportunity for greatness. Thank you once on my successful withdrawal. God bless."
                        </p>
                        <div class="author">
                            <div class="thumb">
                                <img src="https://vuestreims.com/assets/home/front-1/img/team/9.jpg" alt="Thumb">
                            </div>
                            <div class="info">
                                <span>- Annika Bandile</span>
                            </div>
                        </div>
                    </div>
                    <!-- End Single Item -->
                </div>
            </div>
            <!-- End Testimonials -->

            <!-- Start Faq -->
            <div class="col-md-6 faq-area">
                <div class="heading">
                    <h2>Answer & Questions</h2>
                </div>
                <div class="acd-items acd-arrow">
                    <div class="panel-group symb" id="accordion">

                        <!-- Single Item -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#ac1">
                                        How long does it take for my deposit to be credited into my account?
                                    </a>
                                </h4>
                            </div>
                            <div id="ac1" class="panel-collapse collapse in">
                                <div class="panel-body">
                                    <p>
                                        As soon as deposit is received. Typically takes 1-2 hour(s)
                                    </p>
                                </div>
                            </div>
                        </div>
                        <!-- End Single Item -->

                        <!-- Single Item -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#ac2">
                                        How long does the withdrawal process take?
                                    </a>
                                </h4>
                            </div>
                            <div id="ac2" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <p>
                                        Usually just a few hours though there might be delays from financial houses,
                                        we try our best to ensure withdrawals are speedily disbursed
                                    </p>
                                </div>
                            </div>
                        </div>
                        <!-- End Single Item -->

                        <!-- Single Item -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#ac3">
                                        What is the possibility that I will
                                    </a>
                                </h4>
                            </div>
                            <div id="ac3" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <p>
                                        There is always risk of losing funds but we ensure that all invested funds
                                        are capital insured implying that should trading go south that the amount
                                        invested at the time of trading is covered by Insurance also we also have an
                                        Employee Negligence cover to the tune of one million dollars
                                    </p>
                                </div>
                            </div>
                        </div>
                        <!-- End Single Item -->

                        <!-- Single Item -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#ac5">
                                        Do you give referral bonuses?
                                    </a>
                                </h4>
                            </div>
                            <div id="ac5" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <p>
                                        Yes, we give a <strong>2%</strong> referral bonus for any user you refer to
                                        our services.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <!-- Single Item -->

                        <!-- Single Item -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#ac4">
                                        Do you charge fees for services?
                                    </a>
                                </h4>
                            </div>
                            <div id="ac4" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <p>
                                        No we do not charge for our services as we retain our profits before
                                        crediting investors their predetermined profit rate, some investment options
                                        may require fees in such cases the investor is advised properly
                                    </p>
                                </div>
                            </div>
                        </div>
                        <!-- Single Item -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Testimonial & Faq -->

<!-- Start Clients Area -->
<div class="clients-area bg-dark default-padding">
    <div class="container">
        <div class="row">
            <div class="col-md-6 clients-box">
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
                        <a href="#"><img src="https://vuestreims.com/assets/home/front-1/img/clients/01.png" alt="Clients"></a>
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
                        <a href="#"><img src="https://vuestreims.com/assets/home/front-1/img/clients/04.php" alt="Clients"></a>
                    </div>
                </div>
            </div>


            <div class="col-md-6 text-light contact-form">
                <h2 class="text-light">Contact Us</h2>
                <p>
                    We have a culture of always treating people with respect listening and always working in our
                    clients best interests.
                </p>


                <form action="https://directoptionmining.com/index/home/sMailer" method="POST" class="contact-form">
                    <input type="hidden" name="ci_csrf_token" value="" />
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <input class="form-control" required name="name" placeholder="Name" type="text">
                                <span class="alert-error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input class="form-control" required name="email" placeholder="Email*" type="email">
                                <span class="alert-error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input class="form-control" required name="phone" placeholder="Phone" type="text">
                                <span class="alert-error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group comments">
                                <textarea cols="4" class="form-control" required name="message"
                                    placeholder=""></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <button class="btn btn-theme effect btn-sm" type="submit" name="submit">
                                Send Message
                            </button>
                        </div>
                    </div>
                    <!-- Alert Message -->
                    <div class="col-md-12 alert-notification">
                        <div id="message" class="alert-msg"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include("footer.php"); ?>

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