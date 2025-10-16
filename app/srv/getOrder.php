<?php
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['error' => 'Method Not Allowed']);
  exit;
}

$orderId = trim($_POST['orderId'] ?? '');
if ($orderId === '') {
  http_response_code(400);
  echo json_encode(['error' => 'Missing orderId']);
  exit;
}

$appDir  = dirname(__DIR__);
$rootDir = dirname($appDir);
$dbFile  = $rootDir . DIRECTORY_SEPARATOR . 'onlineOrders' . DIRECTORY_SEPARATOR . 'onlineOrders.db';

if (!is_file($dbFile)) {
  http_response_code(404);
  echo json_encode(['error' => 'No orders file']);
  exit;
}

$found = null;
$fh = fopen($dbFile, 'rb');
if ($fh) {
  while (($line = fgets($fh)) !== false) {
    $data = json_decode(trim($line), true);
    if (is_array($data) && isset($data['orderId']) && $data['orderId'] === $orderId) {
      $found = $data;
      break;
    }
  }
  fclose($fh);
}

if (!$found) {
  http_response_code(404);
  echo json_encode(['error' => 'Order not found']);
  exit;
}

echo json_encode($found, JSON_UNESCAPED_UNICODE);
