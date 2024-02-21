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

    $conn->close();
    echo json_encode([$main_address, round($balance * 10)/10]);
} catch (\IEXBase\TronAPI\Exception\TronException $e) {
    $conn->close();
    header('Content-Type: application/json');
    echo json_encode(['Error', $e->getMessage()]);
}
?>