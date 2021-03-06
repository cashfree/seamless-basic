<?php

$mode = "TEST";
$appId = "<API KEY>";
$secretKey = "<Secret Key>";

$orderId = time();
$orderAmount = 100;
$customerName = "John Doe";
$customerPhone = "9900012345";
$customerEmail = "jdoe@gmail.com";
$notifyUrl = "<Notify Url>";
$orderNote = "Extra Info";
$orderCurrency = "INR";
$paymentModes = "";
$pc = "";

 // get secret key from your config
$tokenData = "appId=".$appId."&orderId=".$orderId."&orderAmount=".$orderAmount."&customerEmail=".$customerEmail."&customerPhone=".$customerPhone."&orderCurrency=".$orderCurrency;
$token = hash_hmac('sha256', $tokenData, $secretKey, true);
$paymentToken = base64_encode($token);

?>

<html>
  <head>
    <title>PayForm</title>
    <script src="http://www.cashfree.com/assets/cashfree.sdk.v1.1.js" type="text/javascript"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  </head>
  <body>
    <script type="text/javascript">  
      var payCard = null;
      var payBank = null;
      var payWallet = null;
      var payUpi = null;

      (function() {

        var data = {};
        data.appId = "<?php echo $appId; ?>";
        data.orderId = "<?php echo $orderId; ?>";
        data.orderAmount = <?php echo $orderAmount; ?>;
        data.customerName = "<?php echo $customerName; ?>";
        data.customerPhone = "<?php echo $customerPhone; ?>";
        data.customerEmail = "<?php echo $customerEmail; ?>";
        data.notifyUrl = "<?php echo $notifyUrl; ?>";
        data.orderNote = "<?php echo $orderNote; ?>";
        data.pc = "<?php echo $pc; ?>";
        data.orderCurrency = "INR";
        data.paymentToken = "<?php echo $paymentToken; ?>";
        
        var config = {};
        config.layout = {};
        config.checkout = "transparent";
        config.mode = "<?php echo $mode; ?>";
        var response = CashFree.init(config);

        if (response.status != "OK") {
          // Handle error in initializing 
        }

        var postPaymentCallback = function (event) {
          console.log(event);
          // Callback method that handles Payment 
          if (event.name == "PAYMENT_RESPONSE" && event.status == "SUCCESS") {
            // Handle Success 
          } 
          else if (event.name == "PAYMENT_RESPONSE" && event.status == "CANCELLED") {
            // Handle Cancelled
          } 
          else if (event.name == "PAYMENT_RESPONSE" && event.status == "FAILED") {
            // Handle Failed
          } 
          else if (event.name == "VALIDATION_ERROR") { 
            // Incorrect inputs
          }
        };

        var pc = function () {
            // CashFree.initPopup(); This will not work because browsers block popup requests being initiated from a callback
            data.paymentOption = "card";
            data.card = {};
            data.card.number = document.getElementById("card-num").value; 
            data.card.expiryMonth = document.getElementById("card-mm").value;
            data.card.expiryYear = document.getElementById("card-yyyy").value;
            data.card.holder = document.getElementById("card-name").value;
            data.card.cvv = document.getElementById("card-cvv").value;
            CashFree.paySeamless(data, postPaymentCallback);
            return false;
        }

        // PayCard shows how to implement it if there is a callback involved in your payment click.
        payCard = function() {
          CashFree.initPopup(); // This is required for the popup to work even in case of callback.
            $.ajax({
            url: "https://reqres.in/api/users?page=2", // This is an open endpoint.
            type: "GET",
            success: function(response){
                console.log(response);
                pc();

            }
        });
        };

        payBank = function() {
          CashFree.initPopup();
          data.paymentOption = "nb";
          data.nb = {};
          data.nb.code = document.getElementById("bank-code").value;

          CashFree.paySeamless(data , postPaymentCallback);
          return false;
        };

        payWallet = function() {
          data.paymentOption = "wallet";
          data.wallet = {};
          data.wallet.code = document.getElementById("wallet-code").value;

          CashFree.paySeamless(data, postPaymentCallback);
          return false;
        };

        payUpi = function() {
          data.paymentOption = "upi";
          data.upi = {};
          data.upi.vpa = document.getElementById("upi-vpa").value;

          CashFree.paySeamless(data, postPaymentCallback);
          return false;
        };

      })();

    </script>
    <h1>Payment Form</h1>
    <table border = "3" cellpadding = "5" cellspacing = "5">
      <tr>
        <th>Type</th>
        <th>Details</th>
        <th>Submit</th>
      </tr>
      <tr>
        <td>Cards</td>
        <td>
          <form>
            <p>Card Number: <input type="text" id="card-num" value ="4111111111111111"/>
            CVV:<input type="text" id="card-cvv" value="123"/></p>
            <p>MM:<input type="text" id="card-mm" value="09"/>
            YYYY:<input type="text" id="card-yyyy" value="2018"/></p>
            Name:<input type="text" id="card-name" value="test"/>
          </form>
        </td>
        <td>
          <button onclick="payCard()">Pay with Card</button>      
        </td>
      </tr>
      <tr>
        <td>Net Banking</td>
        <td>
          Select Bank: 
          <select id="bank-code">
            <option value="3333">TEST Bank</option>
            <option value="3003">Axis Bank</option>
            <option value="3028">IndusInd Bank</option>
            <option value="3057">Vijaya Bank</option>
          </select>
        </td>
        <td>
          <button onclick="payBank()">Pay with Net Banking</button>      
        </td>
      </tr>
      <tr>
        <td>Wallet</td>
        <td>
          Select Wallet: 
          <select id="wallet-code">
            <option value="4001">FreeCharge</option>
            <option value="4002">MobiKwik</option>
            <option value="4003">Ola Money</option>
          </select>
        </td>
        <td>
          <button onclick="payWallet()">Pay with Wallet</button>      
        </td>
      </tr>
      <tr>
        <td>UPI</td>
        <td>
          Your UPI VPA: 
          <input type="text" id="upi-vpa"/>
        </td>
        <td>
          <button onclick="payUpi()">Pay with UPI</button>      
        </td>
      </tr>
    </table>
  </body>
</html>