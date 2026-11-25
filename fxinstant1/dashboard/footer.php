<div class="footerContainer">
  <div class="footerInner">
    <div class="ctn-footer-top">
      <div class="ctn-ft-left">
        <p>&copy; <?php echo date('Y') ?> Avix Treims. All Rights Reserved.</p>
      </div>
      <div class="ctn-ft-mid">
        <a href=""><img src="styles/images/ft-top-ic1.png"></a>
      </div>
      <div class="ft-solid">
        <a href="https://www.facebook.com/VisualHyipcom/" target="_blank" class="per"></a>
        <a href="https://twitter.com/" target="_blank" class="bit"></a>
        
      </div>
      
    </div>
  </div>
</div> <!-- end bot footer -->
  
  <script type="text/javascript">
$(document).ready(function() {

    $('.accordion>dl>dt>a').click(function() 
    {
      $(this).toggleClass("rotate0");
    });
      /*------- parse price --------*/
      function parsePriceCrypto()
      {
        returnString = "";
        
        $.post( "https://min-api.cryptocompare.com/data/pricemulti?fsyms=BTC,LTC,ETH,BCH,XRP&tsyms=USD", function( data )
        {
          $('#price_btc').text('$'+data['BTC']['USD']);
          $('#price_ltc').text('$'+data['LTC']['USD']);
          $('#price_eth').text('$'+data['ETH']['USD']);
          $('#price_bch').text('$'+data['BCH']['USD']);
          $('#price_xrp').text('$'+data['XRP']['USD']);
        });
      }
      parsePriceCrypto();
      
      setInterval(function()
      {
        parsePriceCrypto();
      }
      , 5000);
    });
    
    $('.language').click(function() {
      $(this).toggleClass('active');
    });
    
    $('.mobileMenu').click(function() {
      $('.menu').toggleClass('mobile');
      $(this).toggleClass('rotate');
    });


  </script> 

<script src="//code.tidio.co/lcvfvk32el2agiqsjdv2azg7os43tjzg.js" async></script>
<!-- WhatsHelp.io widget -->
<script type="text/javascript">
    (function () {
        var options = {
            whatsapp: "+1(901)283-2236", 
            text: "Hello, how may we help you? Just send us a message now to get assistance.",
            abid:"",// WhatsApp number
           
            call_to_action: "Message us", // Call to action
            position: "left", // Position may be 'right' or 'left'
        };
        var proto = document.location.protocol, host = "whatshelp.io", url = proto + "//static." + host;
        var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = url + '/widget-send-button/js/init.js';
        s.onload = function () { WhWidgetSendButton.init(host, proto, options); };
        var x = document.getElementsByTagName('script')[0]; x.parentNode.insertBefore(s, x);
    })();
</script> 

<!-- Smartsupp Live Chat script -->
<script type="text/javascript">
var _smartsupp = _smartsupp || {};
_smartsupp.key = '6889fcd00961c3ae7023d2241c3d05ac3afb1278';
window.smartsupp||(function(d) {
  var s,c,o=smartsupp=function(){ o._.push(arguments)};o._=[];
  s=d.getElementsByTagName('script')[0];c=d.createElement('script');
  c.type='text/javascript';c.charset='utf-8';c.async=true;
  c.src='https://www.smartsuppchat.com/loader.js?';s.parentNode.insertBefore(c,s);
})(document);
</script>
<noscript> Powered by <a href=“https://www.smartsupp.com” target=“_blank”>Smartsupp</a></noscript>

</body>
</html>