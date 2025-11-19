<?php
require_once __DIR__ . '/functions.php';
$user = current_user();
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>thara74 Store</title>
  <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>body{font-family:'Kanit',sans-serif} .maroon{background-color:#800000;color:#fff}</style>
</head>
<body class="bg-gray-50 min-h-screen">
<header class="maroon p-4">
  <div class="container mx-auto flex justify-between items-center">
    <a href="/thara74/shop_project/" class="text-xl font-bold">thara74 ร้านค้า</a>
    <nav class="space-x-4">
      <a href="/thara74/shop_project/">หน้าแรก</a>
      <?php if(!$user): ?>
        <a href="/thara74/shop_project/register.php">สมัครสมาชิก</a>
        <a href="/thara74/shop_project/login.php">เข้าสู่ระบบ</a>
      <?php else: ?>
        <?php if($user['role'] === 'seller'): ?>
          <a href="/thara74/shop_project/seller/dashboard.php">Seller Dashboard</a>
        <?php endif; ?>
        <?php if($user['role'] === 'admin'): ?>
          <a href="/thara74/shop_project/admin/dashboard.php">Admin Dashboard</a>
        <?php endif; ?>
        <a href="/thara74/shop_project/cart.php">ตะกร้า</a>
        <a href="/thara74/shop_project/logout.php">ออก (<?php echo esc($user['name']); ?>)</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<main class="container mx-auto p-4">
