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
    
    // ดึงข้อมูลที่อยู่จัดส่งจากตาราง orders และ address
    $sql = "SELECT a.* 
            FROM orders o
            JOIN address a ON o.address_id = a.address_id
            WHERE o.order_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$order_id]);
    $address = $stmt->fetch();
    
    if (!$address) {
        echo json_encode(["status" => "error", "message" => "Address not found for this order"]);
        exit;
    }

    echo json_encode([
        "status" => "success",
        "message" => "Address retrieved successfully",
        "address" => $address
    ]);
    
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>