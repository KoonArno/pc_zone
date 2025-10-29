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
    // ตรวจสอบว่ามีการส่ง order_id มาหรือไม่
    if (!isset($_GET['order_id'])) {
        echo json_encode(["status" => "error", "message" => "Order ID is required"]);
        exit;
    }
    
    $order_id = $_GET['order_id'];
    
    // ดึงข้อมูลรายละเอียดของคำสั่งซื้อ
    $sql = "SELECT oi.order_item_id, oi.order_id, oi.product_id, oi.quantity, 
                   p.product_name, p.price, p.image
            FROM orders_item oi
            JOIN product p ON oi.product_id = p.product_id
            WHERE oi.order_id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$order_id]);
    $order_items = $stmt->fetchAll();
    
    if (empty($order_items)) {
        echo json_encode([
            "status" => "error",
            "message" => "No items found for this order"
        ]);
        exit;
    }
    
    // คำนวณยอดรวมของแต่ละรายการ
    foreach ($order_items as &$item) {
        $item['subtotal'] = $item['price'] * $item['quantity'];
    }
    
    echo json_encode([
        "status" => "success",
        "message" => "Order details retrieved successfully",
        "order_items" => $order_items
    ]);
    
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>