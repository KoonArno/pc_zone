<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include '../database/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit;
}

try {
    $data = json_decode(file_get_contents("php://input"));
    
    if (!isset($data->address_id) || !isset($data->user_id)) {
        echo json_encode(["status" => "error", "message" => "Missing required data"]);
        exit;
    }
    
    // ตรวจสอบว่าเป็นที่อยู่ของ user นี้หรือไม่
    $check_sql = "SELECT * FROM address WHERE address_id = ? AND user_id = ?";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([$data->address_id, $data->user_id]);
    
    if (!$check_stmt->fetch()) {
        echo json_encode(["status" => "error", "message" => "Address not found or not authorized"]);
        exit;
    }
    
    // อัปเดตที่อยู่ทั้งหมดของ user นี้ให้ไม่ใช่ที่อยู่หลัก
    $reset_sql = "UPDATE address SET is_default = 1 WHERE user_id = ?";
    $reset_stmt = $pdo->prepare($reset_sql);
    $reset_stmt->execute([$data->user_id]);
    
    // ตั้งที่อยู่ที่เลือกให้เป็นที่อยู่หลัก
    $set_default_sql = "UPDATE address SET is_default = 0 WHERE address_id = ? AND user_id = ?";
    $set_default_stmt = $pdo->prepare($set_default_sql);
    $set_default_stmt->execute([$data->address_id, $data->user_id]);
    
    echo json_encode([
        "status" => "success", 
        "message" => "Default address set successfully",
        "address_id" => $data->address_id
    ]);
    
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>