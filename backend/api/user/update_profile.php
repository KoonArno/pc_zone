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
    
    if (!isset($data->user_id) || !isset($data->username) || !isset($data->email)) {
        echo json_encode(["status" => "error", "message" => "Missing required data"]);
        exit;
    }
    
    // ตรวจสอบว่าเป็น user นี้หรือไม่
    $check_sql = "SELECT * FROM users WHERE user_id = ?";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([$data->user_id]);
    
    if (!$check_stmt->fetch()) {
        echo json_encode(["status" => "error", "message" => "User not found or not authorized"]);
        exit;
    }
    
    // อัปเดตข้อมูลโปรไฟล์
    $update_sql = "UPDATE users SET username = ?, email = ? WHERE user_id = ?";
    $update_stmt = $pdo->prepare($update_sql);
    $update_stmt->execute([$data->username, $data->email, $data->user_id]);
    
    echo json_encode([
        "status" => "success", 
        "message" => "Profile updated successfully",
        "user_id" => $data->user_id
    ]);
    
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>