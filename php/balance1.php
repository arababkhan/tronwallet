<?php
include_once 'vendor/autoload.php';
include 'config.php';

use IEXBase\TronAPI\Tron;

$fullNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.nileex.io');
$solidityNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.nileex.io');
$eventServer = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.nileex.io');

try {
    header('Content-Type: application/json');
    $tron = new Tron($fullNode, $solidityNode, $eventServer);  
    
    $balance = $tron->getBalance($main_address, true);

    $sql = "SELECT * FROM wallet_accounts WHERE balance_checked=false;";
    $result = $conn->query($sql);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $balance = $balance + $tron->getBalance($row['public_addr'], true);
        }
        $result->free();
    } 
    $conn->close();
    echo json_encode([$main_address, round($balance)]);
} catch (\IEXBase\TronAPI\Exception\TronException $e) {
    $conn->close();
    header('Content-Type: application/json');
    echo json_encode(['Error', $e->getMessage()]);
}
?>