<?php 
include 'header.php';
?>

<br><br><br><br><br> 
 <h4 align="center">Send your bitcoin deposit to the address below:</h4><br><br>

<div style="background-color:#0000ff;"  onclick="myFunctions()" align="center"><font color="yellow" size="6"><b>

<input type="" style="background-color:#0000ff;" readonly value="<?php echo $wallet ?>" id="myInputs">
<script>
function myFunctions() {
    var copyText = document.getElementById("myInputs");
    copyText.select();
    document.execCommand("copy");
        // alert("Copied the wallet address: " + copyText.value);
        swal({
            title: "Success",
            text: "Copied the wallet address: " + copyText.value,
            icon: "success",
            button: "Ok",
            timer: 7000
        });    
    }
</script>
</strong></b></font>
</div><br><br><br>
<br><br><font color="red" size="5"><marquee>Ensure to copy address properly to avoid payment error
</marquee></font><br><br><br><br>
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