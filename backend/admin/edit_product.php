<?php
// เริ่มต้น session
session_start();
include '../db.php'; // นำเข้าไฟล์เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่ามีการส่ง product_id มาหรือไม่
if (!isset($_GET['id'])) {
    echo "ไม่พบสินค้าที่ต้องการแก้ไข";
    exit();
}

$product_id = $_GET['id'];

// ดึงข้อมูลสินค้าที่ต้องการแก้ไข
$stmt = $pdo->prepare("SELECT * FROM product WHERE product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

// ตรวจสอบว่าพบสินค้าหรือไม่
if (!$product) {
    echo "ไม่พบสินค้าที่ต้องการแก้ไข";
    exit();
}

// ถ้าผู้ใช้ส่งข้อมูลเพื่อแก้ไข
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = $_POST['product_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $type = $_POST['type'];

    // จัดการกับการอัพโหลดรูปภาพใหม่
    $image = $product['image']; // ใช้รูปภาพเดิมถ้าไม่มีการอัปโหลดใหม่
    if (!empty($_FILES['image']['name'])) {
        $image = basename($_FILES['image']['name']);
        $target_image_path = "../images/" . $image;
        move_uploaded_file($_FILES['image']['tmp_name'], $target_image_path);
    }

    // อัปเดตข้อมูลในฐานข้อมูล
    $stmt = $pdo->prepare("UPDATE product SET product_name = ?, description = ?, image = ?, price = ?, type = ? WHERE product_id = ?");
    if ($stmt->execute([$product_name, $description, $image, $price, $type, $product_id])) {
        $_SESSION['success_message'] = "แก้ไขข้อมูลสำเร็จ";
        header('Location: manage_products.php');
        exit();
    } else {
        echo "เกิดข้อผิดพลาดในการแก้ไขข้อมูล";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-5">
        <h2 class="text-center">Edit Product</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="product_name" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="product_name" name="product_name"
                    value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description"
                    required><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Image</label>
                <input type="file" class="form-control" id="image" name="image">
                <?php if ($product['image']): ?>
                <img src="../images/<?php echo htmlspecialchars($product['image']); ?>" alt="Current Image" width="100"
                    class="mt-2">
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="text" class="form-control" id="price" name="price"
                    value="<?php echo htmlspecialchars($product['price']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="type" class="form-label">Type</label>
                <select class="form-control" id="type" name="type" required>
                    <option value="mouse" <?php echo ($product['type'] == 'mouse') ? 'selected' : ''; ?>>Mouse</option>
                    <option value="keyboard" <?php echo ($product['type'] == 'keyboard') ? 'selected' : ''; ?>>Keyboard</option>
                    <option value="mouse_pad" <?php echo ($product['type'] == 'mouse_pad') ? 'selected' : ''; ?>>Mouse Pad</option>
                    <option value="mic" <?php echo ($product['type'] == 'mic') ? 'selected' : ''; ?>>Mic</option>
                    <option value="monitor" <?php echo ($product['type'] == 'monitor') ? 'selected' : ''; ?>>Monitor</option>
                    <option value="headset" <?php echo ($product['type'] == 'headset') ? 'selected' : ''; ?>>Headset</option>
                    <option value="other" <?php echo ($product['type'] == 'other') ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Save changes</button>
            <a href="manage_products.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>