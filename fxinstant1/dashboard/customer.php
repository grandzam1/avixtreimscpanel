<?php 
include 'header.php';
?>

<center>
<!-- TradingView Widget BEGIN -->
<div class="tradingview-widget-container">
  <div id="tradingview_f933e"></div>
  <div class="tradingview-widget-copyright"><a href="#" rel="noopener" target="_blank"><span class="blue-text"></span> <span class="blue-text">Personal trading chart</span></a></div>
  <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
  <script type="text/javascript">
  new TradingView.widget(
  {
  "width": 1050,
  "height": 500,
  "symbol": "BITFINEX:BTCUSD",
  "interval": "1",
  "timezone": "Etc/UTC",
  "theme": "Dark",
  "style": "9",
  "locale": "en",
  "toolbar_bg": "#f1f3f6",
  "enable_publishing": false,
  "hide_side_toolbar": false,
  "allow_symbol_change": true,
  "calendar": true,
  "studies": [
    "BB@tv-basicstudies"
  ],
  "container_id": "tradingview_f933e"
}
  );
  </script>
</div>
<!-- TradingView Widget END -->



<h3 class="account_main">Account Overview</h3>

<div class="account_overview_wrap">
   <div class="user-info">
        <div class="ctn-invesment-part ctn-invesment-part1">
          <p>Your username:</p>
          <h4><?php echo $username ?></h4>
        </div>
        <div class="ctn-invesment-part ctn-invesment-part2">
          <p>Registration Date:</p>
          <h4><?php echo $date ?></h4>
        </div>
        <div class="ctn-invesment-part ctn-invesment-part3">
          <p>Last Access:</p>
          <h4><?php echo $last_access ?></h4>
        </div>
      </div>
</div>  
<div class="account_overview_wrap">
  <div class="ctn-invesment-part ctn-invesment-part1"> <span class="imageblock"></span>
    <h4>$<?php echo number_format($dbalance) ?></h4>
    <p>ACTIVE DEPOSITS</p>
    <div class="links"><a href="deposit.php">Make A Deposit</a></div>
  </div>
  <div class="ctn-invesment-part ctn-invesment-part2"> <span class="imageblock"></span>
    <h4>$<?php echo number_format($mbalance) ?></h4>
    <p>YOUR BALANCE</p>
    <div class="links"><a href="withdraw.php">Withdraw Funds</a></div>
  </div>
  <div class="ctn-invesment-part ctn-invesment-part3"> <span class="imageblock"></span>
    <h4>$<?php echo number_format($abonus) ?></h4>
    <p>ADDED BONUS</p>
    <div class="links"><a href="deposit.php">My Deposits</a></div>
  </div>
</div>
<br />
<h3 class="account_main">Your Deposits/Withdrawals</h3>
<div class="account_stat">
  <div class="contentLeft">
    <?php 
        $sql211= "SELECT amount FROM deposits WHERE email = '$uemail' ORDER BY id DESC LIMIT 1 ";
        $result211 = mysqli_query($link,$sql211);
        $row11 = mysqli_fetch_assoc($result211);
        $last_amount = $row11['amount'];
        if($last_amount != ""){
            $last_amount = $row11['amount'];
        }else{
            $last_amount = 0.00;
        }
        
    ?>
    <div class="ctn-inves-row invers-part5">
      <p>$<?php echo $last_amount ?></p>
      <h1>Last Deposit</h1>
    </div>

    <div class="ctn-inves-row invers-part8">
      <p>$0.00</p>
      <h1>Withdrawal</h1>
    </div>
  </div>
</div>

</div><!--end column-70-->
</div>
</div>
</div>

</div><div class="contentBotContainer">
  <div class="contentBotInner">
    <div class="ctn-top-solid">
      <h1>btc network </h1>
      <h2> wallets: </h2>
      <div class="solid-top">
      <a href="https://www.coinbase.com/" class="solidTop1"></a> 
      <a href="https://blockchain.info/" class="solidTop2"></a>
      <a href="https://xapo.com/" class="solidTop3"></a>
      <a href="https://airbitz.co/" class="solidTop4"></a>
      </div>
    </div>
    <div class="clearfix"></div>
    <div class="ctn-bot-solid">
      <p>Market Cap: <span>$77829716960.00</span></p>
      <p>Hashrate: <span>7108353254.76</span></p>
      <p>Difficulty: <span>888171856257</span></p>
      <p>Total Blocks: <span>482882</span> </p>
      <p>Network Speed: <span>108.5 PH/s</span></p>
    </div>
  </div>
</div>


<?php 
include 'footer.php';
?>