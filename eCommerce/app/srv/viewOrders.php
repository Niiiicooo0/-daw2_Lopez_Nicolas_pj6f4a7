<?php
header('Content-Type: text/html; charset=utf-8');

$appDir  = dirname(__DIR__);
$rootDir = dirname($appDir);
$dbFile  = $rootDir . DIRECTORY_SEPARATOR . 'onlineOrders' . DIRECTORY_SEPARATOR . 'onlineOrders.db';

$orders = [];
if (is_file($dbFile)) {
  $fh = fopen($dbFile, 'rb');
  if ($fh) {
    while (($line = fgets($fh)) !== false) {
      $data = json_decode(trim($line), true);
      if (is_array($data)) $orders[] = $data;
    }
    fclose($fh);
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>All Orders • López Jeweler's</title>
  <style>
    body{font-family:Arial,Helvetica,sans-serif;background:linear-gradient(180deg,#f8f9fa 0%,#e9ecef 100%);margin:0;color:#222;display:flex;justify-content:center;align-items:flex-start;min-height:100vh}
    .wrap{width:92%;max-width:900px;background:#fff;border-radius:18px;box-shadow:0 15px 40px rgba(0,0,0,.1);margin:32px auto;padding:24px}
    h1{margin:0 0 12px}
    .subtitle{color:#6c757d;margin:0 0 16px}
    .line{padding:8px 0;border-bottom:1px solid #eee;white-space:nowrap;overflow:auto}
    .btns{display:flex;gap:12px;justify-content:center;margin-top:18px}
    .btn{background:#f8f9fa;border:1px solid #ccc;border-radius:8px;padding:10px 18px;text-decoration:none;color:#222}
    .btn:hover{background:#e2e6ea}
    .empty{padding:14px;background:#fff8e1;border:1px solid #f0d78a;border-radius:10px}
    footer{text-align:center;color:#6c757d;font-size:.85rem;margin:16px 0}
  </style>
</head>
<body>
  <main class="wrap">
    <h1>All Orders</h1>
    <p class="subtitle">One order per line — fields separated by “:”.</p>

    <?php if (empty($orders)): ?>
      <div class="empty">There are no orders yet.</div>
    <?php else: ?>
      <?php foreach ($orders as $o):
        $orderId  = $o['orderId']      ?? '';
        $fullName = $o['fullName']     ?? '';
        $address  = $o['address']      ?? '';
        $email    = $o['email']        ?? '';
        $phone    = $o['phone']        ?? '';
        $total    = isset($o['totalWithVAT']) ? number_format((float)$o['totalWithVAT'], 2, '.', '') : '0.00';
        $line = "ID: {$orderId}. Name: {$fullName}. Address: {$address}. Email: {$email}. Phone: {$phone}. €{$total}";
      ?>
        <div class="line"><?php echo htmlspecialchars($line, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></div>
      <?php endforeach; ?>
    <?php endif; ?>

    <div class="btns">
      <a class="btn" href="../cli/operations.html">Back to Operations</a>
      <a class="btn" href="../cli/index.html">Back to Home</a>
    </div>

    <footer>© 2025 López Jeweler's Inc.</footer>
  </main>
</body>
</html>
