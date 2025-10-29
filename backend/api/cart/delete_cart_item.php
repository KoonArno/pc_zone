<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include '../database/db.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่าเป็น POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit;
}

try {
    // รับข้อมูล JSON จาก request body
    $data = json_decode(file_get_contents("php://input"));
    
    // ตรวจสอบข้อมูลที่จำเป็น
    if (!isset($data->id) || !isset($data->user_id)) {
        echo json_encode(["status" => "error", "message" => "Missing required data"]);
        exit;
    }
    
    // ตรวจสอบว่าเป็น cart item ของ user นี้หรือไม่ (ป้องกันการลบข้อมูลของ user อื่น)
    $check_sql = "SELECT * FROM cart WHERE id = ? AND user_id = ?";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([$data->id, $data->user_id]);
    
    if (!$check_stmt->fetch()) {
        echo json_encode(["status" => "error", "message" => "Cart item not found or not authorized"]);
        exit;
    }
    
    // ลบสินค้าออกจากรถเข็น
    $delete_sql = "DELETE FROM cart WHERE id = ? AND user_id = ?";
    $delete_stmt = $pdo->prepare($delete_sql);
    $delete_stmt->execute([$data->id, $data->user_id]);
    
    echo json_encode([
        "status" => "success", 
        "message" => "Cart item deleted successfully",
        "id" => $data->id
    ]);
    
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>