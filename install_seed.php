<?php
require 'inc/config.php';

try {

    /* -------------------------
       INSERT USERS
    -------------------------- */
    $users = [
        ['Admin','admin@example.com','1234','admin'],
        ['Seller','seller@example.com','1234','seller'],
        ['User','user@example.com','1234','user'],
    ];

    $ins = $pdo->prepare("INSERT INTO users (name,email,password,role) VALUES (?,?,?,?)");

    foreach($users as $u){
        $ins->execute([
            $u[0],
            $u[1],
            password_hash($u[2], PASSWORD_DEFAULT),
            $u[3]
        ]);
    }

    /* -------------------------
       GET SELLER USER ID
    -------------------------- */
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute(['seller@example.com']);
    $seller_user_id = $stmt->fetchColumn();

    /* -------------------------
       INSERT SELLER
    -------------------------- */
    $pdo->prepare("
        INSERT INTO sellers (user_id,shop_name,description,approved) 
        VALUES (?,?,?,?)
    ")->execute([
        $seller_user_id,
        "thara74 Shop",
        "ร้านตัวอย่าง thara74",
        1
    ]);

    $seller_id = $pdo->lastInsertId();

    /* -------------------------
       PRODUCT 5 ชิ้น ตามของเดิม
    -------------------------- */
    $GPU = [
        ['GIGABYTE GEFORCE RTX 3050 DUAL O6G',' RTX 3050 DUAL O6G - 6GB GDDR6',6900,10,'assets/img/rtx3050.jpg'],
        ['GIGABYTE RADEON RX 7600XT','RX 7600XT - 16GB GDDR6',9800,8,'assets/img/rx7600.jpg'],
        ['ASUS RADEON RX 9060XT PRIME O8G','RX 9060XT PRIME - 8GB GDDR6',11690,12,'assets/img/rx9060.jpg'],
        ['GIGABYTE GEFORCE RTX 5060 TI AORUS ELITE','RTX 5060 TI AORUS - 16GB GDDR7',18350,5,'assets/img/rtx5060.jpg'],
        ['XFX RADEON RX 9070XT MERCURY AIR OC','RX 9070XT MERCURY - 16GB GDDR6',28260,7,'assets/img/rx9070.jpg'], 
    ];

    $pstmt = $pdo->prepare("
        INSERT INTO products (seller_id,title,description,price,stock,image,published)
        VALUES (?,?,?,?,?,?,1)
    ");

    foreach($GPU as $item){
        $pstmt->execute([
            $seller_id,
            $item[0],
            $item[1],
            $item[2],
            $item[3],
            $item[4]
        ]);
    }

    echo "Seed complete!\nUsers + Seller + 5 Products inserted.";

} catch (Exception $e){
    echo "Error: " . $e->getMessage();
}
