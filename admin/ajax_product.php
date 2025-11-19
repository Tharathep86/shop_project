<?php
require_once __DIR__ . '/../inc/functions.php';
require_login();
$user = current_user();
if($user['role'] !== 'admin'){ die(json_encode(['success'=>false,'msg'=>'Access denied'])); }

require __DIR__ . '/../inc/config.php';

$action = $_POST['action'] ?? '';
$response = ['success'=>false];

try{
    if($action==='add'){
        $stmt = $pdo->prepare('INSERT INTO products (seller_id,title,description,price,stock,image,published) VALUES (?,?,?,?,?,?,?)');
        $imageName = 'assets/img/no-image.png';
        if(!empty($_FILES['image']['name'])){
            $imageName = 'assets/img/'.basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../'.$imageName);
        }
        $stmt->execute([
            $_POST['seller_id'],
            $_POST['title'],
            $_POST['description'],
            $_POST['price'],
            $_POST['stock'],
            $imageName,
            isset($_POST['published'])?1:0
        ]);
        $response['success'] = true;
    } elseif($action==='edit'){
        $stmt = $pdo->prepare('UPDATE products SET seller_id=?, title=?, description=?, price=?, stock=?, published=? WHERE id=?');
        $stmt->execute([
            $_POST['seller_id'],
            $_POST['title'],
            $_POST['description'],
            $_POST['price'],
            $_POST['stock'],
            isset($_POST['published'])?1:0,
            $_POST['id']
        ]);
        $response['success'] = true;
    } elseif($action==='delete'){
        $stmt = $pdo->prepare('DELETE FROM products WHERE id=?');
        $stmt->execute([$_POST['id']]);
        $response['success'] = true;
    } else{
        $response['msg'] = 'Action invalid';
    }
}catch(Exception $e){
    $response['msg'] = $e->getMessage();
}

echo json_encode($response);
