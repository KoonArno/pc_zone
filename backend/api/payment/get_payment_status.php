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
    
    // ดึงข้อมูลสถานะการชำระเงินจากตาราง payment
    $sql = "SELECT payment_status FROM payment WHERE order_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$order_id]);
    $payment_status = $stmt->fetch();
    
    if (!$payment_status) {
        echo json_encode([
            "status" => "error",
            "message" => "No payment record found for this order"
        ]);
        exit;
    }
    
    echo json_encode([
        "status" => "success",
        "message" => "Payment status retrieved successfully",
        "payment_status" => $payment_status['payment_status']
    ]);
    
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>