<?php
header('Content-Type: text/plain; charset=utf-8');
header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo "Method Not Allowed";
  exit;
}

$orderId  = trim($_POST['orderId']  ?? '');
$fullName = trim($_POST['fullName'] ?? '');
$address  = trim($_POST['address']  ?? '');
$email    = trim($_POST['email']    ?? '');
$phone    = trim($_POST['phone']    ?? '');
$q1       = intval($_POST['q1']     ?? 0);
$q2       = intval($_POST['q2']     ?? 0);
$q3       = intval($_POST['q3']     ?? 0);
$q4       = intval($_POST['q4']     ?? 0);

if ($orderId === '' || $fullName === '' || $address === '' || $email === '' || $phone === '') {
  http_response_code(400);
  echo "Missing fields";
  exit;
}
if (($q1 + $q2 + $q3 + $q4) === 0) {
  http_response_code(400);
  echo "No products selected";
  exit;
}

$catalog = [
  'p1' => ['name' => 'Diamond Ring',   'price' => 1200.00],
  'p2' => ['name' => 'Gold Necklace',  'price' =>  800.00],
  'p3' => ['name' => 'Silver Bracelet','price' =>  150.00],
  'p4' => ['name' => 'Luxury Watch',   'price' => 2500.00],
];

$subtotal = 0.0;
$items = [];

if ($q1 > 0) { $subtotal += $catalog['p1']['price'] * $q1; $items[] = ['id'=>'p1','name'=>$catalog['p1']['name'],'qty'=>$q1,'unitPrice'=>$catalog['p1']['price']]; }
if ($q2 > 0) { $subtotal += $catalog['p2']['price'] * $q2; $items[] = ['id'=>'p2','name'=>$catalog['p2']['name'],'qty'=>$q2,'unitPrice'=>$catalog['p2']['price']]; }
if ($q3 > 0) { $subtotal += $catalog['p3']['price'] * $q3; $items[] = ['id'=>'p3','name'=>$catalog['p3']['name'],'qty'=>$q3,'unitPrice'=>$catalog['p3']['price']]; }
if ($q4 > 0) { $subtotal += $catalog['p4']['price'] * $q4; $items[] = ['id'=>'p4','name'=>$catalog['p4']['name'],'qty'=>$q4,'unitPrice'=>$catalog['p4']['price']]; }

$vatRate = 0.21;
$totalWithVAT = round($subtotal * (1.0 + $vatRate), 2);

$appDir  = dirname(__DIR__);        
$rootDir = dirname($appDir);         
$dbDir   = $rootDir . DIRECTORY_SEPARATOR . 'onlineOrders';
$dbFile  = $dbDir  . DIRECTORY_SEPARATOR . 'onlineOrders.db';

if (!is_dir($dbDir)) {
  @mkdir($dbDir, 0775, true);
}

$orderData = [
  'orderId'      => $orderId,
  'fullName'     => $fullName,
  'address'      => $address,
  'email'        => $email,
  'phone'        => $phone,
  'items'        => $items,
  'subtotal'     => round($subtotal, 2),
  'vat'          => 21,
  'totalWithVAT' => $totalWithVAT,
  'timestamp'    => date('c')
];

$line = json_encode($orderData, JSON_UNESCAPED_UNICODE);

$fh = @fopen($dbFile, 'ab');
if ($fh === false) {
  http_response_code(500);
  echo "Cannot write file";
  exit;
}
fwrite($fh, $line . PHP_EOL);
fclose($fh);

echo number_format($totalWithVAT, 2, '.', '');
