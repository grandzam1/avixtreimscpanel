<?php include 'session.php'; ?>
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
<link rel="shortcut icon" href="../images/bitcoin31.png">

<script src='style/jquery.js' type='text/javascript'></script>
<script src='style/setting2.js' type='text/javascript'></script>
<script src='style/tab.js' type='text/javascript'></script>
<script src='style/bootstrap.min.js' type='text/javascript'></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

</head>

<body class="loginarea" >
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

<div class="inside_inner_account">
<div class="member_wrap">
<div class="membersidebar">
   <ul>
    <li><a href="customer.php">My Account</a></li><li><a href="deposit.php">Deposit</a></li><li><a href="deposit_history.php">Deposits History</a></li><li><a href="withdraw.php">Withdraw</a></li><li></li><li><a href="referals.php">Referrals</a></li><li><a href="logout.php">Logout</a></li>
</ul>
</div>
<div class="member-container">
<div class="account_top">
  <div class="user_left">
    <h2>Welcome, <span><?php echo strtoupper($fullname) ?></span></h2>
  </div>
  <div class="affiliate_top">Affiliate Link:<a href="#" class="ref-link"><?php echo $siteurl ?>/ref.php?ref=<?php echo strtolower($username) ?> </a></div>
  <div class="get_banners"><a href="referals.php">Get Banners</a></div>
</div>
<div class="member_right">
  <?php 
    $msg = $wrn = $err = "";
   ?>
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
<?php 
  if ($err != "") {
?>
 <script type="text/javascript">
    $(document).ready(function() {
        swal({
            title: "Error",
            text: "<?php echo $err ?>",
            icon: "error",
            button: "Ok",
            timer: 5000
        });
    });
</script>
<?php } ?>
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
            timer: 5000
        });
    });
</script>
<?php } ?>

<?php 
  if (isset($_GET['success']) ) {
?>
 <script type="text/javascript">
    $(document).ready(function() {
        swal({
            title: "Success",
            text: "You have registered, Login to access your dashboard",
            icon: "success",
            button: "Ok",
            timer: 5000
        });    
       });

</script>
<?php } ?>
