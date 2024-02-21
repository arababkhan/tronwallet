<?php
include_once 'vendor/autoload.php';
include 'config.php';

use IEXBase\TronAPI\Tron;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

$fullNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://nile.trongrid.io');
$solidityNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://nile.trongrid.io');
$eventServer = new \IEXBase\TronAPI\Provider\HttpProvider('https://nile.trongrid.io');

try {
    header('Content-Type: application/json');

    /*********  creating account and qr code  *****/
    $tron = new Tron($fullNode, $solidityNode, $eventServer);  
    // Generate a new account
    $newAccount = $tron->createAccount();
    // Create a QR code
    $qrCode = new QrCode($newAccount->getAddress(true));
    $qrCode->setSize(300);
    // Create a QR code writer
    $writer = new PngWriter();
    // Encode the QR code as base64
    $dataUri = $writer->write($qrCode)->getDataUri();
    $newPublicKey = $newAccount->getAddress(true);
    $newPrivateKey = $newAccount->getPrivateKey();

    /**********  insert account into db   ******/
    $created_date = date('Y-m-d H:i:s');
    $sql = "INSERT INTO wallet_accounts (public_addr, private_key, created_date, balance_checked) VALUES ('$newPublicKey', '$newPrivateKey', '$created_date', 0)";
    if (mysqli_query($conn, $sql)) {
        echo json_encode([$newPublicKey, $dataUri]);
    } else {
        echo json_encode(['DB error', $sql]);
        die();
    }
    mysqli_close($conn);
} catch (\IEXBase\TronAPI\Exception\TronException $e) {
    header('Content-Type: application/json');
    echo json_encode(['Error', '']);
}
?>