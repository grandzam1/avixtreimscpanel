<?php 
include 'session.php';

$wrn = "";
$msg = "";
$mods = "";

if (isset($_POST['submit'])) {
    $method = @trim($_POST['des']);
    $amt = @trim($_POST['amt']);
    $wallett = @trim($_POST['wallet']);
    $bname = @trim($_POST['bnk']);
    $anum = @trim($_POST['num']);
    $aname = @trim($_POST['name']);

    if (!empty($method) && !empty($amt)) {
        if ($amt > $mbalance) {
            $wrn = "Please your balance is too low, make a deposit!";
        } elseif ($upgrade == 0) {
            // Send upgrade warning email
            $subject = "Withdrawal Alert!";
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: $sitename <$sitemail>" . "\r\n";

            $body = '
            <div style="background: #f5f7f8;width: 100%;height: 100%; font-family: sans-serif; font-weight: 100;">
                <div style="background:#fff;max-width: 600px;margin: 0px auto;padding: 30px;">
                    <div style="float: left;"> <img src="'.$siteurl.'/logo.png"> </div>
                    <div style="float: right"> <p>Dear: '.$username.'</p> </div> 
                    <div style="clear: both;"></div> 
                    <div style="background: #1976d2; padding: 20px; color: #fff;margin-top: 10px;">
                        <h1>Thank you for choosing '.$sitename.'</h1>
                    </div> 
                    <div style="padding: 20px;"> 
                        <p style="line-height: 25px; color:#000;"> 
                        Your withdrawal request of '.$amt.' USD via '.$method.' was not successful. Your account needs to be upgraded because your current investment plan does not support withdrawals.
                        <br>Thank you.
                        </p>
                    </div>
                    <div style="border-bottom: 1px solid #ccc;"></div>
                    <div style="background: #1976d2; padding: 20px; color: #fff;margin-top: 10px;">
                        <p>Please do not reply to this email. Emails sent to this address will not be answered.<br>
                        Copyright '.date('Y').' '.$sitename.'.</p>
                    </div> 
                </div>
            </div>';

            mail($uemail, $subject, $body, $headers);

            $wrn = "Please Upgrade your account, your current investment plan does not support this action. Check your email for more info!";
        } else {
            $_SESSION['method'] = $method;
            $_SESSION['amt'] = $amt;
            $_SESSION['wallett'] = $wallett;
            $_SESSION['bname'] = $bname;
            $_SESSION['anum'] = $anum;
            $_SESSION['aname'] = $aname;
            $mods = "show";
        }
    }
}

if (isset($_POST['pin'])) {
    $pin = trim($_POST['wpin']);
    if (!empty($pin)) {
        if ($wpin != $pin) {
            $wrn = 'Please enter withdrawal authorization pin to continue with this request';
        } else {
            $method = $_SESSION['method'];
            $amt = $_SESSION['amt'];
            $wallett = $_SESSION['wallett'];
            $bname = $_SESSION['bname'];
            $anum = $_SESSION['anum'];
            $aname = $_SESSION['aname'];

            $query = mysqli_query($link, "INSERT INTO withdrawals (email, amount, wallet, method, bank_name, acct_number, acct_name) VALUES ('$uemail', '$amt', '$wallett', '$method', '$bname', '$anum', '$aname')");

            mysqli_query($link, "UPDATE users SET mbalance = mbalance - $amt WHERE email = '$uemail'");

            // Send withdrawal processing email
            $subject = "Withdrawal Alert!";
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: $sitename <$sitemail>" . "\r\n";

            $body = '
            <div style="background: #f5f7f8;width: 100%;height: 100%; font-family: sans-serif; font-weight: 100;">
                <div style="background:#fff;max-width: 600px;margin: 0px auto;padding: 30px;">
                    <div style="float: left;"> <img src="'.$siteurl.'/logo.png"> </div>
                    <div style="float: right"> <p>Dear: '.$username.'</p> </div> 
                    <div style="clear: both;"></div> 
                    <div style="background: #1976d2; padding: 20px; color: #fff;margin-top: 10px;">
                        <h1>Thank you for choosing '.$sitename.'</h1>
                    </div> 
                    <div style="padding: 20px;"> 
                        <p style="line-height: 25px; color:#000;"> 
                        Your withdrawal request of '.$amt.' USD via '.$method.' is under review. Your account will be credited when the withdrawal is approved.
                        <br>Thank you.
                        </p>
                    </div>
                    <div style="border-bottom: 1px solid #ccc;"></div>
                    <div style="background: #1976d2; padding: 20px; color: #fff;margin-top: 10px;">
                        <p>Please do not reply to this email. Emails sent to this address will not be answered.<br>
                        Copyright '.date('Y').' '.$sitename.'.</p>
                    </div> 
                </div>
            </div>';

            mail($uemail, $subject, $body, $headers);

            $msg = "Withdrawal request has been submitted";
            echo "<meta http-equiv='refresh' Content='1; url=withdraw.php' />";
        }
    }
}
?>

 
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
<!-- font-family: 'Open Sans', sans-serif; -->
<link href='style/bootstrap.min.css' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="" />
<link href='style/custom.css' rel='stylesheet' type='text/css'>
<link href='style/default.css' rel='stylesheet' type='text/css'>
<link href='style/tab.css' rel='stylesheet' type='text/css'>
<link rel="icon" href="../styles/images/favicon.png">
<script src='style/jquery.js' type='text/javascript'></script>
<script src='style/setting2.js' type='text/javascript'></script>
<script src='style/tab.js' type='text/javascript'></script>
<script src='style/bootstrap.min.js' type='text/javascript'></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<?php 
  if($mods != ""){
    echo "<script>  
    $(window).on('load', function() {
          $('#toast').modal('show');
      }) 
      </script> ";
  }
?>
</head>

<body class="loginarea" style="zoom: 1;">
<div class="wrapper-account">
  <div class="headerContainer">
    <div class="headerInner"> 
      <div class="hdRight">
        <div class="mainNavRight">
          <div class="navbar">
            <div class="navbar-inner">
              
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
 </div>

<html><head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Withdrawal</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script type='text/javascript'>

    
</script>
</head>
<style>
body {
  font-family: Arial;
  font-size: 17px;
  padding: 8px;
}

* {
  box-sizing: border-box;
}

.row {
  display: -ms-flexbox; /* IE10 */
  display: flex;
  -ms-flex-wrap: wrap; /* IE10 */
  flex-wrap: wrap;
  margin: 0 -16px;
}

.col-25 {
  -ms-flex: 25%; /* IE10 */
  flex: 25%;
}

.col-50 {
  -ms-flex: 50%; /* IE10 */
  flex: 50%;
}

.col-75 {
  -ms-flex: 75%; /* IE10 */
  flex: 75%;
}

.col-25,
.col-50,
.col-75 {
  padding: 0 16px;
}

.container {
  background-color: #f2f2f2;
  padding: 5px 20px 15px 20px;
  border: 1px solid lightgrey;
  border-radius: 3px;
}

input[type=text] {
  width: 100%;
  margin-bottom: 20px;
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 3px;
}
input[type=number] {
  width: 100%;
  margin-bottom: 20px;
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 3px;
}

label {
  margin-bottom: 10px;
  display: block;
}

.icon-container {
  margin-bottom: 20px;
  padding: 7px 0;
  font-size: 24px;
}

.btn {
  background-color: #4CAF50;
  color: white;
  padding: 12px;
  margin: 10px 0;
  border: none;
  width: 100%;
  border-radius: 3px;
  cursor: pointer;
  font-size: 17px;
}

.btn:hover {
  background-color: #45a049;
}

a {
  color: #2196F3;
}

hr {
  border: 1px solid lightgrey;
}

span.price {
  float: right;
  color: grey;
}

/* Responsive layout - when the screen is less than 800px wide, make the two columns stack on top of each other instead of next to each other (also change the direction - make the "cart" column go on top) */
@media (max-width: 800px) {
  .row {
    flex-direction: column-reverse;
  }
  .col-25 {
    margin-bottom: 20px;
  }
}
</style>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>






<!--<script>-->
<!--function validateForm() {-->
<!--  var x = document.forms["myForm"]["balance"].value;-->
<!--  if (x == "0.00") {-->
<!--    alert("Your Balance is too low make a deposit");-->
<!--    return false;-->
<!--  }-->
<!--}-->
<!--</script>-->

<!--<script>-->
<!--function validateForm() {-->
<!--  var x = document.forms["frank"]["balance"].value;-->
<!--  if (x == "0.00") {-->
<!--    alert("Your Balance is too low make a deposit");-->
<!--    return false;-->
<!--  }-->
<!--}-->
<!--</script>-->

<!--<script>-->
<!--function validateForm() {-->
<!--  var x = document.forms["franks"]["balance"].value;-->
<!--  if (x == "0.00") {-->
<!--    alert("Your Balance is too low make a deposit");-->
<!--    return false;-->
<!--  }-->
<!--}-->
<!--</script>-->








<body>
    

      
<?php 
  if ($wrn != "") {
?>
 <script type="text/javascript">
    $(document).ready(function() {
        swal({
            title: "Warning",
            text: "<?php echo $wrn ?>",
            icon: "warning",
            button: "Ok",
            timer: 7000
        });
    });
</script>
<?php } ?>

<?php 
  if ($msg != "") {
?>
 <script type="text/javascript">
    $(document).ready(function() {
        swal({
            title: "Success",
            text: "<?php echo $msg ?>",
            icon: "success",
            button: "Ok",
            timer: 7000
        });
    });
</script>
<?php } ?>



<h2 align="center" style="color:white;"><b>Make a Withdrawal Request.</b></h2>
<h6 align="center" style="color:white;">Available Balance: <b>$<?php echo $dbalance ?></b></h6>
<h6 align="center" style="color:white;">Withdrawal: <b>$<?php echo $mbalance ?> </b></h6>
<br>
<p align="center" style="color:white;">Withdrawal/<a href="customer.php"style="color:yellow;">Home</a></p><br>
    
     <h4 style="color: red;">    </h4>

<div class="row">
  <div class="col-75">
    <div class="container">
      
        <div class="row">
         

          <div class="col-50">
             
            <h3>Select your preferred withdrawal payment option :</h3>
 <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#demo"><img src="b.png" width="50px"> Bitcoin (recommended)</button>
  <div id="demo" class="collapse"><br>
  <br>
  
  
     <form name="myForm" action="withdraw.php" onsubmit="return validateForm()"  method="post" ]>
         
         
        <input type="hidden" name="id" value="<?php echo $id ?>">
        <input type="hidden" name="des" value="Bitcoin Withdrawal">
          <input name="deposit" type="hidden" value="0.00">
         <input name="email" type="hidden" value="<?php echo $uemail ?>"> 
         <input name="balance" type="hidden" value="0.00">
        <div class="row">
          <div class="col-50">
            <label for="fname"><i class="fa fa-amount"></i> Input Amount</label>
            <input type="number" id="fname" name="amt" placeholder="Amount" required="">
<label for="fname"><i class="fa fa-wallet"></i> Wallet Address</label>
            <input type="text" id="fname" name="wallet" placeholder="Wallet Address" required="">
          </div>
          
        </div>
      
        <input type="submit" value="Proceed" class="btn" name="submit">
      </form>
  </div>
  <hr>
   <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#demo2"><img src="trans.png" width="60px">  Transfer</button>
  <div id="demo2" class="collapse"><br>
  <br>
     <form action="withdraw.php" method="POST" name="frank" onsubmit="return validateForm()">
            <input type="hidden" name="id" value="<?php echo $id ?>">
            <input type="hidden" name="des" value="Transfer Withdrawal">
          <input name="deposit" type="hidden" value="0.00">
         <input name="email" type="hidden" value="<?php echo $uemail ?>"> 
         <input name="balance" type="hidden" value="0.00">
        <div class="row">
          <div class="col-50">
              
            <label for="cname">Name</label>
            <input type="text" id="cname" name="name" placeholder="Name" required="">
            <label for="ccnum">Account number</label>
            <input type="number" id="ccnum" name="num" placeholder="Account Number" required="">
            <label for="ccnum">Bank</label>
            <input type="text" id="ccnum" name="bnk" placeholder="Bank" required="">
            <label for="ccnum">Amount</label>
            <input type="text" id="ccnum" name="amt" placeholder="Input Amount" required="">
          </div>
          
        </div>
      
        <input type="submit" value="Proceed" class="btn" name="submit">
      </form>
  </div>
  
   <hr>
   <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#demo3"><img src="cash.png" width="60px">Cashapp</button>
  <div id="demo3" class="collapse"><br>
  <br>
     <form action="withdraw.php" method="POST" name="franks" onsubmit="return validateForm()">
    <input type="hidden" name="id" value="<?php echo $id ?>">
    <input type="hidden" name="des" value="Cashapp Withdrawal">
          <input name="deposit" type="hidden" value="0.00">
         <input name="email" type="hidden" value="<?php echo $uemail ?>"> 
         <input name="balance" type="hidden" value="0.00">
        <div class="row">
          <div class="col-50">
            
            <label for="cname">Amount</label>
            <input type="text" id="cname" name="amt" placeholder="Amount" required="">
            <label for="ccnum">Cashapp Tag</label>
            <input type="text" id="ccnum" name="wallet" placeholder="Cashapp Tag" required="">
           
            
          </div>
          
        </div>
      
        <input type="submit" value="Proceed" class="btn" name="submit">
      </form>
  </div>
  <form action="withdraw.php" method="POST" name="franks" onsubmit="return validateForm()">
  <hr>
   <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#demo4"><img src="s.png" width="50px"> Skrill</button>
  <div id="demo4" class="collapse"><br>
  <br>
    
          <input type="hidden" name="id" value="<?php echo $id ?>">
          <input type="hidden" name="des" value="Skrill Withdrawal">
          <input name="deposit" type="hidden" value="0.00">
         <input name="email" type="hidden" value="<?php echo $uemail ?>"> 
         <input name="balance" type="hidden" value="0.00">
        <div class="row">
          <div class="col-50">
            <label for="fname"><i class="fa fa-amount"></i> Input Amount</label>
            <input type="number" id="fname" name="amt" placeholder="Amount" required="">

          </div>
          
        </div>
      
        <input type="submit" value="Proceed" class="btn" name="submit">
      </form>
  </div>
   <hr>
   <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#demo5"><img src="west.png" width="50px"> Western Union</button>
  <div id="demo5" class="collapse"><br>
  <br>
     <form action="withdraw.php" method="POST" name="myForm" onsubmit="return validateForm()">
      
      <input type="hidden" name="id" value="<?php echo $id ?>">
      <input type="hidden" name="des" value="Western Union Withdrawal">
          <input name="deposit" type="hidden" value="0.00">
         <input name="email" type="hidden" value="<?php echo $uemail ?>"> 
         <input name="balance" type="hidden" value="0.00">
        <div class="row">
          <div class="col-50">
            <label for="fname"><i class="fa fa-amount"></i> Input Amount</label>
            <input type="number" id="fname" name="amt" placeholder="Amount" required="">

          </div>
          
        </div>
      
        <input type="submit" value="Proceed" class="btn" name="submit">
      </form>
  </div>
  <hr>
   <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#demo6"><img src="p.png" width="50px"> Paypal</button>
  <div id="demo6" class="collapse"><br>
  <br>
     <form action="withdraw.php" method="POST" name="myForm" onsubmit="return validateForm()">
    
       <input type="hidden" name="id" value="<?php echo $id ?>">
       <input type="hidden" name="des" value="Paypal Withdrawal">
          <input name="deposit" type="hidden" value="0.00">
         <input name="email" type="hidden" value="<?php echo $uemail ?>"> 
         <input name="balance" type="hidden" value="0.00">
        <div class="row">
          <div class="col-50">
            <label for="fname"><i class="fa fa-amount"></i> Input Amount</label>
            <input type="number" id="fname" name="amt" placeholder="Amount" required="">

          </div>
          
        </div>
      
        <input type="submit" value="Proceed" class="btn" name="submit">
      </form>
  </div>
  
  
  
 
          </div>
          
        </div>

    </div>
  </div>
  
</div>
<hr>
<br>
<p align="center" style="color:white;">
"If you are interested in learning more about the withdrawal process or have an issue please contact support; info@devcapitols.com to get a detailed description of the process."</p><br>



 <!-- Modal -->
 <form method="post" action="withdraw.php">
 <div class="modal fade" id="toast" tabindex="-1" aria-labelledby="toast" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="toast">Withdrawal</h5>
      </div>
      <div class="modal-body">

      <input type="text" name="wpin" placeholder="Enter withdrawal pin">
      </div>

      <div class="modal-footer">
       
        <button type="submit"  name="pin" class="btn btn-primary">CONTINUE</button>
      </div>
    </div>
  </div>
</div>
</form>
<!-- Modal -->


<!-- WhatsHelp.io widget -->
<script type="text/javascript">
    (function () {
        var options = {
            whatsapp: "+1(901)283-2236", 
            text: "Hello, how may we help you? Just send us a message now to get assistance.",
            abid:"+1(901)283-2236",// WhatsApp number
           
            call_to_action: "Message us", // Call to action
            position: "left", // Position may be 'right' or 'left'
        };
        var proto = document.location.protocol, host = "whatshelp.io", url = proto + "//static." + host;
        var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = url + '/widget-send-button/js/init.js';
        s.onload = function () { WhWidgetSendButton.init(host, proto, options); };
        var x = document.getElementsByTagName('script')[0]; x.parentNode.insertBefore(s, x);
    })();
</script>
<!-- /WhatsHelp.io widget -->




   </html>