<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-Z+PhnjjAv6zXzQ7Kj9dfBBpBqKs8g1k6fDFIXfI9CRCEEt37cLJJjLZu+mBNMEQkLCnZwKjzUw9y/h15/6lT9Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <div class="top-bar-area bg-light">
        <div class="container">
            <div class="row">
                <div class="col-md-8 address-info text-left">
                    <div class="info box">
                        <ul>
                            <li>
                                
                                <div class="info">
                                    <span>Address</span><?php echo $address; ?>
                                </div>
                            </li>
                            <li>
                               
                                <div class="info">
                                    <span>Email</span><?php echo $email; ?>
                                </div>
                            </li>

                        </ul>
                    </div>
                </div>
                <div class="col-md-4 social text-right">
                    <!--
                    <ul>
                        <li>
                            <a href="#" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        </li>
                        <li>
                            <a href="#" target="_blank"><i class="fab fa-twitter"></i></a>
                        </li>
                        <li>
                            <a href="#" target="_blank"><i class="fab fa-dribbble"></i></a>
                        </li>
                        <li>
                            <a href="#" target="_blank"><i class="fab fa-pinterest"></i></a>
                        </li>
                        <li>
                            <a href="#" target="_blank"><i class="fab fa-google-plus-g"></i></a>
                        </li>
                    </ul>
                    -->
                </div>
            </div>
        </div>
    </div>
    <!-- End Header Top -->
    <!-- Header ============================================= -->
    
    
    <header id="home">

        <!-- Start Navigation -->
        <nav class="navbar navbar-default navbar-sticky bootsnav">

            <!-- Start Top Search -->
            <div class="container">
                <div class="row">
                    <div class="top-search">
                        <div class="input-group">
                            <form action="#">
                                <input type="text" name="text" class="form-control" placeholder="Search">
                                <button type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Top Search -->

            <div class="container">

                <!-- Start Header Navigation -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-menu">
                        <i class="fa fa-bars"></i>
                    </button>
                    <a class="navbar-brand brand-logo" href="/">
                        <img src="<?php echo $logo; ?>" class="logo" alt="Logo">
                        <div class="brand-items">
                            <span><?php echo $site_name; ?></span>
                        </div>
                    </a>
                    <div class="hidden-md hidden-lg">
                        <div class="btcwdgt-news" bw-cur="usd" bw-entries="1" bw-theme="light"></div>
                    </div>
                </div>
                <!-- End Header Navigation -->
                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="navbar-menu">
                    <ul class="nav navbar-nav navbar-right" data-in="#" data-out="#">

                        <li class="hidden-xs hidden-sm">
                            <div class="btcwdgt-news" bw-cur="usd" bw-entries="1" bw-theme="light"></div>
                        </li>

                        <li>
                            <a href="/" class="active">Home</a>
                        </li>
                          <li>
                            <a href="about.php" class="active">About Us</a>
                        </li>
                        <!--
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle active" data-toggle="dropdown">Company</a>
                            <ul class="dropdown-menu">
                                <li><a href="about.php">About Us</a></li>
                                <li><a href="policy.php">Our Policies</a></li>
                                <li><a href="contact.php">Contact</a></li>
                            </ul>
                        </li>
                       
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle active" data-toggle="dropdown">Services</a>
                            <ul class="dropdown-menu">
                                <li><a href="#">Cryptocurrencies</a></li>
                                <li><a href="#">Forex</a></li>
                                <li><a href="#">Loans</a></li>
                                <li><a href="#">NFP</a></li>
                                <li><a href="#">Escrow Service</a></li>
                            </ul>

                        </li>
 -->
                        <li>
                            <a href="signin.php">Login</a>
                        </li>
                        <li>
                            <a href="register.php">Signup</a>
                        </li>

                        <li>
                            <!-- GTranslate: https://gtranslate.io/ -->

                            <style type="text/css">
                            a.gflag {
                                vertical-align: middle;
                                font-size: 16px;
                                padding: 1px 0;
                                background-repeat: no-repeat;
                                background-image: url(../gtranslate.net/flags/16.png);
                            }

                            a.gflag img {
                                border: 0;
                            }

                            a.gflag:hover {
                                background-image: url(../gtranslate.net/flags/16a.png);
                            }

                            #goog-gt-tt {
                                display: none !important;
                            }

                            .goog-te-banner-frame {
                                display: none !important;
                            }

                            .goog-te-menu-value:hover {
                                text-decoration: none !important;
                            }

                            body {
                                top: 0 !important;
                            }

                            #google_translate_element2 {
                                display: none !important;
                            }
                            </style>

                            <select class="select" onchange="doGTranslate(this);">
                                <option value="">Translate Language</option>
                                <option value="en|af">Afrikaans</option>
                                <option value="en|sq">Albanian</option>
                                <option value="en|ar">Arabic</option>
                                <option value="en|hy">Armenian</option>
                                <option value="en|az">Azerbaijani</option>
                                <option value="en|eu">Basque</option>
                                <option value="en|be">Belarusian</option>
                                <option value="en|bg">Bulgarian</option>
                                <option value="en|ca">Catalan</option>
                                <option value="en|zh-CN">Chinese (Simplified)</option>
                                <option value="en|zh-TW">Chinese (Traditional)</option>
                                <option value="en|hr">Croatian</option>
                                <option value="en|cs">Czech</option>
                                <option value="en|da">Danish</option>
                                <option value="en|nl">Dutch</option>
                                <option value="en|en">English</option>
                                <option value="en|et">Estonian</option>
                                <option value="en|tl">Filipino</option>
                                <option value="en|fi">Finnish</option>
                                <option value="en|fr">French</option>
                                <option value="en|gl">Galician</option>
                                <option value="en|ka">Georgian</option>
                                <option value="en|de">German</option>
                                <option value="en|el">Greek</option>
                                <option value="en|ht">Haitian Creole</option>
                                <option value="en|iw">Hebrew</option>
                                <option value="en|hi">Hindi</option>
                                <option value="en|hu">Hungarian</option>
                                <option value="en|is">Icelandic</option>
                                <option value="en|id">Indonesian</option>
                                <option value="en|ga">Irish</option>
                                <option value="en|it">Italian</option>
                                <option value="en|ja">Japanese</option>
                                <option value="en|ko">Korean</option>
                                <option value="en|lv">Latvian</option>
                                <option value="en|lt">Lithuanian</option>
                                <option value="en|mk">Macedonian</option>
                                <option value="en|ms">Malay</option>
                                <option value="en|mt">Maltese</option>
                                <option value="en|no">Norwegian</option>
                                <option value="en|fa">Persian</option>
                                <option value="en|pl">Polish</option>
                                <option value="en|pt">Portuguese</option>
                                <option value="en|ro">Romanian</option>
                                <option value="en|ru">Russian</option>
                                <option value="en|sr">Serbian</option>
                                <option value="en|sk">Slovak</option>
                                <option value="en|sl">Slovenian</option>
                                <option value="en|es">Spanish</option>
                                <option value="en|sw">Swahili</option>
                                <option value="en|sv">Swedish</option>
                                <option value="en|th">Thai</option>
                                <option value="en|tr">Turkish</option>
                                <option value="en|uk">Ukrainian</option>
                                <option value="en|ur">Urdu</option>
                                <option value="en|vi">Vietnamese</option>
                                <option value="en|cy">Welsh</option>
                                <option value="en|yi">Yiddish</option>
                            </select>
                            <div id="google_translate_element2"></div>
                            <script type="text/javascript">
                            function googleTranslateElementInit2() {
                                new google.translate.TranslateElement({
                                    pageLanguage: 'en',
                                    autoDisplay: false
                                }, 'google_translate_element2');
                            }
                            </script>
                            <script type="text/javascript"
                                src="../translate.google.com/translate_a/element7876.js?cb=googleTranslateElementInit2">
                            </script>


                            <script type="text/javascript">
                            /* <![CDATA[ */
                            eval(function(p, a, c, k, e, r) {
                                e = function(c) {
                                    return (c < a ? '' : e(parseInt(c / a))) + ((c = c % a) > 35 ? String
                                        .fromCharCode(c + 29) : c.toString(36))
                                };
                                if (!''.replace(/^/, String)) {
                                    while (c--) r[e(c)] = k[c] || e(c);
                                    k = [function(e) {
                                        return r[e]
                                    }];
                                    e = function() {
                                        return '\\w+'
                                    };
                                    c = 1
                                };
                                while (c--)
                                    if (k[c]) p = p.replace(new RegExp('\\b' + e(c) + '\\b', 'g'), k[c]);
                                return p
                            }('6 7(a,b){n{4(2.9){3 c=2.9("o");c.p(b,f,f);a.q(c)}g{3 c=2.r();a.s(\'t\'+b,c)}}u(e){}}6 h(a){4(a.8)a=a.8;4(a==\'\')v;3 b=a.w(\'|\')[1];3 c;3 d=2.x(\'y\');z(3 i=0;i<d.5;i++)4(d[i].A==\'B-C-D\')c=d[i];4(2.j(\'k\')==E||2.j(\'k\').l.5==0||c.5==0||c.l.5==0){F(6(){h(a)},G)}g{c.8=b;7(c,\'m\');7(c,\'m\')}}',
                                43, 43,
                                '||document|var|if|length|function|GTranslateFireEvent|value|createEvent||||||true|else|doGTranslate||getElementById|google_translate_element2|innerHTML|change|try|HTMLEvents|initEvent|dispatchEvent|createEventObject|fireEvent|on|catch|return|split|getElementsByTagName|select|for|className|goog|te|combo|null|setTimeout|500'
                                .split('|'), 0, {}))
                            /* ]]> */
                            </script>

                        </li>
                    </ul>
                </div>
                <!-- /.navbar-collapse -->
            </div>

        </nav>



        <!-- End Navigation -->
    </header>