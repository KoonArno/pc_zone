<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include '../database/db.php'; // เชื่อมต่อฐานข้อมูล

try {
    // ตรวจสอบว่ามี product_id ถูกส่งมาหรือไม่
    if (!isset($_GET['product_id'])) {
        echo json_encode(["status" => "error", "message" => "Missing product_id"]);
        exit;
    }

    $product_id = $_GET['product_id'];

    // คำสั่ง SQL ดึงข้อมูลของสินค้ารายตัวจาก product_id
    $sql = "SELECT product_id, product_name, description, image, price FROM product WHERE product_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$product_id]);

    // ดึงข้อมูลเป็น associative array
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // ตรวจสอบว่าพบสินค้าหรือไม่
    if ($product) {
        echo json_encode(["status" => "success", "product" => $product]);
    } else {
        echo json_encode(["status" => "error", "message" => "Product not found"]);
    }
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
