<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include '../database/db.php'; // เชื่อมต่อฐานข้อมูล

// รับข้อมูล JSON จาก request
$data = json_decode(file_get_contents("php://input"));

// ตรวจสอบว่าเป็น POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit;
}

try {
    // ตรวจสอบว่ามีข้อมูลที่จำเป็นครบถ้วน
    if (!isset($data->order_id) || !isset($data->order_status)) {
        echo json_encode(["status" => "error", "message" => "Order ID and status are required"]);
        exit;
    }
    
    $order_id = $data->order_id;
    $order_status = $data->order_status;
    
    // อัปเดตสถานะคำสั่งซื้อ
    $sql = "UPDATE orders SET order_status = ? WHERE order_id = ?";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$order_status, $order_id]);
    
    if ($result) {
        echo json_encode([
            "status" => "success",
            "message" => "Order status updated successfully"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to update order status"
        ]);
    }
    
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>