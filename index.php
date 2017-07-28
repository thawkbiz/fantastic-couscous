<?php
session_start();
include 'csrf.class.php';

$csrf = new csrf();

$success = FALSE;

// Generate Token Id and Valid
$token_id = $csrf->get_token_id();
$token_value = $csrf->get_token($token_id);

// Generate Random Form Names
$form_names = $csrf->form_names(array('email'), false);

if (isset($_POST[$form_names['email']])) {
  // Check if token id and token value are valid.
  if($csrf->check_valid('post')) {
    // Get the Form Variables.
    $email = trim($_POST[$form_names['email']]);

    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    // Form Function Goes Here
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

        // Ok, the email should be valid, but check analytics too.
        //recaptcha test
      $curl = curl_init();
      curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'https://www.google.com/recaptcha/api/siteverify',
        CURLOPT_POST => 1,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_POSTFIELDS => [
        'secret' => '6LdcqioUAAAAAPe97vLFsEtLTm6c1mnMVJc5E0ec',
        'response' => $_POST['g-recaptcha-response'],
        ],
        ]);
      $response = json_decode(curl_exec($curl));

        //UPDATE: if your check pass, go on
      if ($response->success) {
        $myFile = "../emails.txt";
        $fh = fopen($myFile, 'a') or die("can't open file");
        $stringData = $email . "\n";
        fwrite($fh, $stringData);
        fclose($fh);
        $success = TRUE;
      } else {
            // died("Recaptcha missing.");
      }

    }
  }
    // Regenerate a new random value for the form.
  $form_names = $csrf->form_names(array('email'), true);
}

?><!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="It's tea, but in a token.">

  <title>TeaToken</title>

  <!-- Twitter Card data -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:site" content="@TeaToken">
  <meta name="twitter:title" content="Tea Token">
  <meta name="twitter:description" content="It's tea, but in a token.">
  <meta name="twitter:creator" content="@TeaToken">
  <!-- Twitter summary card with large image must be at least 280x150px -->
  <meta name="twitter:image:src" content="http://www.teatoken.io/img/logo.png">

  <!-- Open Graph data -->
  <meta property="og:title" content="Tea Token" />
  <meta property="og:type" content="article" />
  <meta property="og:url" content="http://www.teatoken.io/" />
  <meta property="og:image" content="http://www.teatoken.io/img/logo.png" />
  <meta property="og:description" content="It's tea, but in a token." />

  <!-- Bootstrap Core CSS -->
  <link href="lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Catamaran:100,200,300,400,500,600,700,800,900" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Muli" rel="stylesheet">

  <!-- Plugin CSS -->
  <link rel="stylesheet" href="lib/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="lib/simple-line-icons/css/simple-line-icons.css">
  <link rel="stylesheet" href="lib/device-mockups/device-mockups.min.css">

  <!-- Theme CSS -->
  <link href="css/new-age.min.css" rel="stylesheet">

  <link rel="apple-touch-icon" sizes="180x180" href="favicons/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="favicons/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="favicons/favicon-16x16.png">
  <link rel="manifest" href="favicons/manifest.json">
  <link rel="mask-icon" href="favicons/safari-pinned-tab.svg" color="#b91d47">
  <link rel="shortcut icon" href="favicons/favicon.ico">
  <meta name="apple-mobile-web-app-title" content="TeaToken">
  <meta name="application-name" content="TeaToken">
  <meta name="msapplication-config" content="favicons/browserconfig.xml">
  <meta name="theme-color" content="#ffffff">

  <style>
    #clockdiv{
      font-family: sans-serif;
      color: #fff;
      display: inline-block;
      font-weight: 100;
      text-align: center;
      font-size: 30px;
    }

    #clockdiv > div{
      padding: 10px;
      border-radius: 3px;
      background: transparent;
      display: inline-block;
    }

    #clockdiv div > span{
      padding: 15px;
      border-radius: 3px;
      background: green;
      display: inline-block;
    }

    .smalltext{
      padding-top: 5px;
      font-size: 16px;
    }
  </style>

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Animate on Scroll -->
    <link href="https://cdn.rawgit.com/michalsnik/aos/2.1.1/dist/aos.css" rel="stylesheet">

    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
      ga('create', 'UA-103346620-1', 'auto');
      ga('send', 'pageview');
    </script>

  </head>

  <body id="page-top">

    <?php
    if ($success === TRUE): ?>
    <!-- Modal -->
    <div id="myModal" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Email Registered</h4>
          </div>
          <div class="modal-body">
            <p>Thank you! Your email has been submitted.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>

      </div>
    </div>
    <?php
    endif;
    ?>

    <nav id="mainNav" class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
          data-target="#bs-example-navbar-collapse-1">
          <span class="sr-only">Toggle navigation</span> Menu <i class="fa fa-bars"></i>
        </button>
        <a class="navbar-brand page-scroll" href="#page-top">TeaToken.io</a>
      </div>

      <!-- Collect the nav links, forms, and other content for toggling -->
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav navbar-right">
          <li>
            <a class="page-scroll" href="#download">Countdown</a>
          </li>
          <li>
            <a class="page-scroll" href="#features">Features</a>
          </li>
          <li>
            <a class="page-scroll" href="#contact">Contact</a>
          </li>
          <li>
            <a href="faq.html">FAQ</a>
          </li>
        </ul>
      </div>
      <!-- /.navbar-collapse -->
    </div>
    <!-- /.container-fluid -->
  </nav>

  <header>
    <div class="container">
      <div class="row">
        <div class="col-sm-7">
          <div data-aos="fade-right" class="header-content">
            <div class="header-content-inner">
              <h1>Farmers and Tea Enthusiasts can find common ground for all things tea-related</h1>
              <a href="doc/whitepaper.pdf" class="btn btn-outline btn-xl page-scroll">
                Read our whitepaper!
              </a>
            </div>
          </div>
        </div>
        <div class="col-sm-5">
          <div data-aos="fade-left" class="header-content">
            <div class="header-content-inner">
              <img src="img/logo.png" class="img-responsive" alt="Tea Token Logo">
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>

  <section id="download" class="download bg-primary text-center">
    <div class="container">
      <div class="row">
        <div class="col-md-12 text-center">
          <h2 data-aos="fade-down" class="section-heading aos-init aos-animate">
            Pre-Sale Begins Aug. 7
          </h2>
          <div id="clockdiv">
            <div>
              <span class="days">10</span>
              <div class="smalltext">Days</div>
            </div>
            <div>
              <span class="hours">11</span>
              <div class="smalltext">Hours</div>
            </div>
            <div>
              <span class="minutes">58</span>
              <div class="smalltext">Minutes</div>
            </div>
            <div>
              <span class="seconds">25</span>
              <div class="smalltext">Seconds</div>
            </div>
          </div>
        </div>
      </div>


      <?php
      if ($success !== TRUE): ?>

        <div class="row">
          <div class="modal-content col-sm-10 col-sm-offset-1" style="color:black; margin-top: 2rem;">
            <div class="modal-header">
              <h3 class="modal-title" id="myModalLabel">Receive email updates!</h3>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-xs-6">
                  <div class="well">

                    <form id="loginForm" method="POST" action="index.php">
                      <div class="form-group">
                        <label for="<?php print $form_names['email']; ?>" class="control-label">Email address</label>
                        <input type="text" class="form-control" id="<?php print $form_names['email']; ?>" name="<?php print $form_names['email']; ?>" value="" required="" title="Please enter you email" placeholder="example@gmail.com">
                        <span class="help-block"></span>

                        <div class="g-recaptcha" data-sitekey="6LdcqioUAAAAANMVAi3QpFPAkzW_5WU4aUdxRnuo"></div>
                      </div>
                      <input type="hidden" name="<?php print $token_id; ?>" value="<?php print $token_value; ?>" />
                      <button type="submit" class="btn btn-success btn-block">Submit</button>
                    </form>
                  </div>
                </div>
                <div class="col-xs-6">
                  <p class="lead">Sign Up for Email Alerts</p>
                  <ul class="list-unstyled text-left" style="line-height: 2">
                    <li><span class="glyphicon glyphicon-ok"></span> Get the latest news about TeaToken</li>
                    <li><span class="glyphicon glyphicon-ok"></span> Get alerts about the upcoming ICO</li>
                    <li><span class="glyphicon glyphicon-ok"></span> We will never sell or rent your information!</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>

      <?php
      endif;
      ?>

    </section>

    <section id="features" class="features">
      <div class="container">
        <div class="row">
          <div class="col-lg-12 text-center">
            <div class="section-heading">
              <h2>Rethinking Tea</h2>
              <p class="text-muted">
                For thousands of years tea has been a simple, enjoyable, relaxing staple in our diets.
                <br>
                We think it's time tea got an upgrade.
              </p>
              <hr>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="device-container">
              <div class="device-mockup iphone6_plus portrait white">
                <div class="device">
                  <div class="screen">
                    <img src="img/demo-screen-2.jpg" class="img-responsive" alt="iPhone with a photo of tea showing"></div>
                    <div class="button">
                      <!-- You can hook the "home button" to some JavaScript events or just remove it -->
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-8">
              <div class="container-fluid">
                <div class="row">
                  <div class="col-md-6">
                    <div data-aos="flip-left" class="feature-item">
                      <i class="icon-wallet text-primary"></i>
                      <h3>ERC20 Compliant</h3>
                      <p class="text-muted">
                        TeaToken is ERC20 compliant. So you can safely store
                        your tokens in any Ethereum Wallet.
                      </p>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div data-aos="flip-right" data-aos-delay="300" class="feature-item">
                      <i class="icon-globe text-primary"></i>
                      <h3>Global Trade</h3>
                      <p class="text-muted">
                        Small businesses will be able to support artisan teas from countries all over the
                        world, driving business as demand grows for this healthy coffee alternative.
                      </p>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div data-aos="flip-left" class="feature-item">
                      <i class="icon-cup text-primary"></i>
                      <h3>Brew Perfect</h3>
                      <p class="text-muted">
                        The freedom of choice is to know what you consume.
                        Connect directly with farmers to ensure your tea is grown to your preference.
                      </p>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div data-aos="flip-right" data-aos-delay="300" class="feature-item">
                      <i class="icon-organization text-primary"></i>
                      <h3>Shipping Logistics</h3>
                      <p class="text-muted">
                        Tea Token aims to become an essential player in the commodities market,
                        to meet growing demand and help create direct pathways between consumer and
                        providers.
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="cta">
        <div class="cta-content">
          <div class="container">
            <h2>
              <span data-aos="fade-down">Stop waiting.</span>
              <br>
              <span data-aos="fade-down">Start sipping.</span>
              <br>
              <span data-aos="fade-down">Start sharing.</span>
            </h2>
            <a href="#download" class="btn btn-outline btn-xl page-scroll" data-aos="flip-left">Let's Get Started!<span class="sr-only"> Download the Tea Token Whitepaper</span></a>
          </div>
        </div>
        <div class="overlay"></div>
      </section>

      <section id="contact" class="contact bg-primary">
        <div class="container">
          <h2>We <i class="fa fa-heart"><span class="sr-only">love</span></i> new friends!</h2>
          <ul class="list-inline list-social">
            <li class="social-twitter">
              <a href="https://twitter.com/TeaToken" target="_blank"><i class="fa fa-twitter"><span class="sr-only">Visit tea token on Twitter</span></i></a>
            </li>
            <li class="social-facebook">
              <a href="https://www.facebook.com/teatoken" target="_blank"><i class="fa fa-facebook"><span class="sr-only">Like tea token on Facebook</span></i></a>
            </li>
            <li class="social-google-plus">
              <a href="http://reddit.com/r/teatoken" target="_blank"><i class="fa fa-reddit" aria-hidden="true"><span class="sr-only">Visit tea token on Reddit</span></i></a>
            </li>
          </ul>
        </div>
      </section>

      <footer>
        <div class="container">
          <p>&copy; <span class="year">2017</span> TEATOKEN.IO. All Rights Reserved.</p>
          <ul class="list-inline">
            <li>
              <a href="privacy.html">Privacy</a>
            </li>
            <li>
              <a href="terms.html">Terms</a>
            </li>
            <li>
              <a href="faq.html">FAQ</a>
            </li>
            <li>
              <a href="mailto:info@teatoken.io">Email Us</a>
            </li>
          </ul>
        </div>
      </footer>
      <!-- jQuery -->
      <script src="lib/jquery/jquery.min.js"></script>

      <!-- Bootstrap Core JavaScript -->
      <script src="lib/bootstrap/js/bootstrap.min.js"></script>

      <!-- Plugin JavaScript -->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>

      <!-- Theme JavaScript -->
      <script src="js/new-age.min.js"></script>

      <!-- Animate on Scroll -->
      <script src="https://cdn.rawgit.com/michalsnik/aos/2.1.1/dist/aos.js"></script>

      <script>
        // Dynamic date
        document.querySelector('.year').textContent = new Date().getFullYear().toString()

        AOS.init({
          disable: 'mobile'
        })
      </script>

      <?php
        if ($success === TRUE): ?>
          <!-- Modal -->
          <script type="text/javascript">
            $(window).on('load',function(){
              $('#myModal').modal('show');
            });
          </script>
      <?php
        else: ?>
          <!-- Validate email -->
          <script type="text/javascript">

            function isEmail(email) {

              var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
              return pattern.test(email);

            }

            $('#loginForm').on('submit', function(e){
              if(!isEmail($('#<?php print $form_names['email']; ?>').value)) {
                e.preventDefault();
                $('.help-block').text('Email must be valid.').attr('role', 'alert');
              }
              else {
                $(this).unbind('submit').submit();
              }
            });

            // Check email every time they change it.
            $('#<?php print $form_names['email']; ?>').on('change', function(e){
              if(!isEmail($('#<?php print $form_names['email']; ?>').value)) {
                $('.help-block').text('Email must be valid.').attr('role', 'alert');
              }
              else {
                $('.help-block').text('').attr('role', '');
              }
            });
          </script>
      <?php
        endif;
      ?>


      <script>
        function getTimeRemaining(endtime) {
          var t = Date.parse(endtime) - Date.parse(new Date());
          var seconds = Math.floor((t / 1000) % 60);
          var minutes = Math.floor((t / 1000 / 60) % 60);
          var hours = Math.floor((t / (1000 * 60 * 60)) % 24);
          var days = Math.floor(t / (1000 * 60 * 60 * 24));
          return {
            'total': t,
            'days': days,
            'hours': hours,
            'minutes': minutes,
            'seconds': seconds
          };
        }

        function initializeClock(id, endtime) {
          var clock = document.getElementById(id);
          var daysSpan = clock.querySelector('.days');
          var hoursSpan = clock.querySelector('.hours');
          var minutesSpan = clock.querySelector('.minutes');
          var secondsSpan = clock.querySelector('.seconds');

          function updateClock() {
            var t = getTimeRemaining(endtime);

            daysSpan.innerHTML = t.days;
            hoursSpan.innerHTML = ('0' + t.hours).slice(-2);
            minutesSpan.innerHTML = ('0' + t.minutes).slice(-2);
            secondsSpan.innerHTML = ('0' + t.seconds).slice(-2);

            if (t.total <= 0) {
              clearInterval(timeinterval);
            }
          }

          updateClock();
          var timeinterval = setInterval(updateClock, 1000);
        }

        var deadline = new Date(1502064000000);
        initializeClock('clockdiv', deadline);
      </script>

      <script src='https://www.google.com/recaptcha/api.js'></script>

  </body>

</html>
