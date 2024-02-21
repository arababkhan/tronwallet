<?php
include_once 'vendor/autoload.php';
include 'config.php';

use IEXBase\TronAPI\Tron;

$fullNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.nileex.io');
$solidityNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.nileex.io');
$eventServer = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.nileex.io');
$tron = new Tron($fullNode, $solidityNode, $eventServer);  

function daemonFunc($conn, $tron, $main_address, $test_address) {
    try {   
        $unchecked_accounts = $conn->query("SELECT * FROM wallet_accounts where balance_checked=false;");
        echo $unchecked_accounts;
        if ($unchecked_accounts->num_rows > 0) {
            while($row = $unchecked_accounts->fetch_assoc()) {
                $balance = $tron->getBalance($main_address, true);
                if($balance > 1) {
                    $testTransaction = $tron->sendTrx($test_address, 1, $row['public_addr']);
                    $testTransactionInfo = $tron->getTransactionInfo($testTransaction);

                    $energyCost = $testTransactionInfo['ret'][0]['energy_usage_total'];
                    $bandwidthCost = $testTransactionInfo['ret'][0]['bandwidth_usage'];

                    $energyPrice = $tron->getEnergy();
                    $bandwidthPrice = $tron->getBandwidth();
                    $estimatedGasFee = $energyPrice * $energyCost + $bandwidthPrice * $bandwidthCost;
                    if($balance > $estimatedGasFee) {
                        $transaction = $tron->transactionBuilder()->sendTrx(
                            $main_address,
                            $balance - $estimatedGasFee,
                            $row['public_addr'],
                            $estimatedGasFee
                        );
                        $signedTransaction = $tron->signTransaction($transaction, $row['private_key']);
                        $broadcast = $tron->sendRawTransaction($signedTransaction);
                        $conn->query("UPDATE wallet_accounts SET balance_checked = true WHERE id = ". $row['id']);
                    }
                }
            }
            mysqli_close($conn);
        } else {
            mysqli_close($conn);
        }
        $message = "Tasks occurred at " . date('Y-m-d H:i:s') . "\n";
        file_put_contents('log.txt', $message, FILE_APPEND);
    } catch (\IEXBase\TronAPI\Exception\TronException $e) {
        mysqli_close($conn);
        $message = $e->getMessage() . " occurred at " . date('Y-m-d H:i:s') . "\n";
        file_put_contents('log.txt', $message, FILE_APPEND);
    }
}

while (true) {
    daemonFunc($conn, $tron, $main_address, $test_address);
    sleep(120);
}

?>
