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
    
    // ดึงข้อมูลคำสั่งซื้อของผู้ใช้ และข้อมูลการชำระเงิน
    $sql = "SELECT o.*, p.payment_id, p.payment_status 
            FROM orders o 
            LEFT JOIN payment p ON o.order_id = p.order_id 
            WHERE o.user_id = ? 
            ORDER BY o.created_at DESC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll();
    
    echo json_encode([
        "status" => "success",
        "message" => "Orders retrieved successfully",
        "orders" => $orders
    ]);
    
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>