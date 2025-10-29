<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include '../database/db.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่าเป็น GET request
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit;
}

try {
    // ตรวจสอบว่ามีการส่ง user_id มาหรือไม่
    if (!isset($_GET['user_id'])) {
        echo json_encode(["status" => "error", "message" => "User ID is required"]);
        exit;
    }
    
    $user_id = $_GET['user_id'];
    
    // ดึงข้อมูลสินค้าในรถเข็น
    $sql = "SELECT c.id, c.user_id, c.product_id, c.quantity, p.product_name, p.price, p.image 
            FROM cart c 
            JOIN product p ON c.product_id = p.product_id 
            WHERE c.user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll();
    
    // คำนวณราคารวม
    $total_price = 0;
    foreach ($cart_items as $item) {
        $total_price += $item['price'] * $item['quantity'];
    }
    
    echo json_encode([
        "status" => "success",
        "message" => "Cart items retrieved successfully",
        "cart_items" => $cart_items,
        "total_price" => $total_price
    ]);
    
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>