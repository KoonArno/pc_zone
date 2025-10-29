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
    
    // ดึงข้อมูลคำสั่งซื้อล่าสุดที่อยู่ในตะกร้า (ยังไม่ได้ชำระเงิน)
    // ต้องปรับ SQL query ตามโครงสร้างฐานข้อมูลจริง ๆ ของคุณ
    // สมมติว่ามีฟิลด์ status เพื่อระบุสถานะของออเดอร์
    $sql = "SELECT total_price FROM orders WHERE user_id = ? AND status = 'pending' ORDER BY created_at DESC LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $order = $stmt->fetch();
    
    if ($order) {
        echo json_encode([
            "status" => "success",
            "message" => "Order total retrieved successfully",
            "total_price" => $order['total_price']
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "No pending order found for this user"
        ]);
    }
    
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>