<?php
require_once __DIR__ . '/../inc/functions.php';
require_login();
$user = current_user();
if($user['role'] !== 'admin'){ die('Access denied'); }

require __DIR__ . '/../inc/config.php';

// ดึงสินค้าทั้งหมด
$stmt = $pdo->prepare('SELECT p.*, s.shop_name FROM products p JOIN sellers s ON s.id = p.seller_id ORDER BY p.created_at DESC');
$stmt->execute();
$products = $stmt->fetchAll();

// ดึงร้านขายของ
$stmt2 = $pdo->query('SELECT id, shop_name FROM sellers');
$sellers = $stmt2->fetchAll();

require __DIR__ . '/../inc/header.php';
?>

<h1 class="text-2xl font-bold mb-4">Admin Dashboard</h1>

<!-- Add Product Form -->
<h2 class="text-lg font-semibold mt-4">เพิ่มสินค้า</h2>
<form id="addProductForm" enctype="multipart/form-data">
    <select name="seller_id" required>
        <?php foreach($sellers as $s) echo "<option value='{$s['id']}'>{$s['shop_name']}</option>"; ?>
    </select>
    <input type="text" name="title" placeholder="ชื่อสินค้า" required>
    <textarea name="description" placeholder="รายละเอียด"></textarea>
    <input type="number" name="price" placeholder="ราคา" step="0.01" required>
    <input type="number" name="stock" value="1" required>
    <input type="file" name="image">
    <label><input type="checkbox" name="published"> เผยแพร่</label>
    <button type="submit">เพิ่มสินค้า</button>
</form>

<h2 class="text-lg font-semibold mt-4">สินค้าทั้งหมด</h2>
<div id="productList">
<?php foreach($products as $p): ?>
<div id="product-<?= $p['id'] ?>" class="bg-white p-3 rounded shadow mb-2">
    <div class="flex justify-between items-center">
        <div>
            <strong><?= esc($p['title']) ?></strong> (<?= esc($p['shop_name']) ?>) - <?= esc($p['price']) ?> บาท
        </div>
        <div>
            <button onclick="editProduct(<?= $p['id'] ?>)">แก้ไข</button>
            <button onclick="deleteProduct(<?= $p['id'] ?>)">ลบ</button>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>

<!-- Edit Form Modal -->
<div id="editForm" style="display:none; background:#fff; padding:10px; border:1px solid #ccc; margin-top:10px;">
    <h3>แก้ไขสินค้า</h3>
    <form id="editProductForm">
        <input type="hidden" name="id" id="edit_id">
        <select name="seller_id" id="edit_seller_id" required>
            <?php foreach($sellers as $s) echo "<option value='{$s['id']}'>{$s['shop_name']}</option>"; ?>
        </select>
        <input type="text" name="title" id="edit_title" required>
        <textarea name="description" id="edit_description"></textarea>
        <input type="number" name="price" id="edit_price" step="0.01" required>
        <input type="number" name="stock" id="edit_stock" required>
        <label><input type="checkbox" name="published" id="edit_published"> เผยแพร่</label>
        <button type="submit">บันทึก</button>
        <button type="button" onclick="document.getElementById('editForm').style.display='none'">ยกเลิก</button>
    </form>
</div>

<script>
// Add Product
document.getElementById('addProductForm').addEventListener('submit', function(e){
    e.preventDefault();
    let formData = new FormData(this);
    formData.append('action','add');
    fetch('ajax_product.php', {method:'POST', body:formData})
    .then(res=>res.json())
    .then(data=>{
        if(data.success){
            alert('เพิ่มสินค้าเรียบร้อย');
            location.reload(); // หรือจะอัปเดต DOM โดยไม่ reload
        }else{
            alert('เกิดข้อผิดพลาด: '+data.msg);
        }
    });
});

// Edit Product - เปิด form
function editProduct(id){
    const products = <?php echo json_encode($products); ?>;
    const p = products.find(x=>x.id==id);
    document.getElementById('edit_id').value = p.id;
    document.getElementById('edit_title').value = p.title;
    document.getElementById('edit_description').value = p.description;
    document.getElementById('edit_price').value = p.price;
    document.getElementById('edit_stock').value = p.stock;
    document.getElementById('edit_seller_id').value = p.seller_id;
    document.getElementById('edit_published').checked = p.published==1;
    document.getElementById('editForm').style.display='block';
}

// Edit Product - submit
document.getElementById('editProductForm').addEventListener('submit', function(e){
    e.preventDefault();
    let formData = new FormData(this);
    formData.append('action','edit');
    fetch('ajax_product.php', {method:'POST', body:formData})
    .then(res=>res.json())
    .then(data=>{
        if(data.success){
            alert('แก้ไขสินค้าเรียบร้อย');
            location.reload(); // หรืออัปเดต DOM
        }else{
            alert('เกิดข้อผิดพลาด: '+data.msg);
        }
    });
});

// Delete Product
function deleteProduct(id){
    if(!confirm('คุณแน่ใจจะลบสินค้านี้?')) return;
    let formData = new FormData();
    formData.append('action','delete');
    formData.append('id',id);
    fetch('ajax_product.php', {method:'POST', body:formData})
    .then(res=>res.json())
    .then(data=>{
        if(data.success){
            alert('ลบสินค้าเรียบร้อย');
            document.getElementById('product-'+id).remove();
        }else{
            alert('เกิดข้อผิดพลาด: '+data.msg);
        }
    });
}
</script>

<?php require __DIR__ . '/../inc/footer.php'; ?>
