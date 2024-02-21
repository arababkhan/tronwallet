<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Tron Wallet</title>
</head>
<body>
    <div class="container row">
        <div class="header">Tron Deposit</div>
        <div class="column">
            <div class="button" id="generate">Generate New Child A/C for Deposit</div>
            <center>
                <div class="qr-code" id="qr_image">
                    <!-- QR Code Image Here -->
                </div>
                <div class="account-number">
                    Deposit Account Number
                </div>
                <div id="account"></div>
            </center>
        </div>
        <div class="column">
            <div class="button" id="getBalance">
                <img id="loading" src="loading-1.gif" style="display:none" width="20px" height="20px"/>
                <span id="balance_label">Check Admin Balance<span>
            </div>
            <center>
                <div class="admin-account" id="admin_account">
                </div>
                <div class="balance" id="balance">
                </div>
            </center>
        </div>
    </div>
    <script>
        document.getElementById("generate").addEventListener("click", function() {
            createAccount();
        });

        document.getElementById("getBalance").addEventListener("click", function() {
            getBalance();
        })
        function createAccount() {
            // Use AJAX to send data to the server
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "account.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.send();

            // Handle the server's response
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var w_res = JSON.parse(xhr.responseText);
                    document.getElementById("account").innerHTML = w_res[0];
                    document.getElementById("qr_image").innerHTML = '<img src="' + w_res[1] + '" alt="QR Code">';
                }
            };
        }

        function getBalance() {
            showLoadingIcon();
            // Use AJAX to send data to the server
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "balance.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.send();

            // Handle the server's response
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    console.log(xhr.responseText)
                    var w_res = JSON.parse(xhr.responseText);
                   
                    document.getElementById("admin_account").innerHTML = w_res[0];
                    document.getElementById("balance").innerHTML = w_res[1] + " TRX";
                    hideLoadingIcon();
                }
            };
        }

        function showLoadingIcon() {
            const loadingImage = document.getElementById('loading');
            loadingImage.style.display = 'inline-block';
            const balanceLabel = document.getElementById('balance_label');
            balanceLabel.style.position = 'relative';
            balanceLabel.style.top = '-3px';
            balanceLabel.style.left = '10px';
            const getBalanceBtn = document.getElementById('getBalance');
            getBalanceBtn.style.padding = '7px 20px';
        }

        function hideLoadingIcon() {
            const loadingImage = document.getElementById('loading');
            loadingImage.style.display = 'none';
            const balanceLabel = document.getElementById('balance_label');
            balanceLabel.style.position = 'static';
            balanceLabel.style.top = '0px';
            balanceLabel.style.left = '0px';
            const getBalanceBtn = document.getElementById('getBalance');
            getBalanceBtn.style.padding = '10px 20px';
        }
    </script>
</body>
</html>


