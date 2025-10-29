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
    if (!isset($data->user_id) || !isset($data->product_id)) {
        echo json_encode(["status" => "error", "message" => "Missing required data"]);
        exit;
    }
    
    // ลบ bookmark
    $delete_sql = "DELETE FROM bookmark WHERE user_id = ? AND product_id = ?";
    $delete_stmt = $pdo->prepare($delete_sql);
    $result = $delete_stmt->execute([$data->user_id, $data->product_id]);
    
    if ($result) {
        echo json_encode([
            "status" => "success", 
            "message" => "Bookmark removed successfully"
        ]);
    } else {
        echo json_encode([
            "status" => "error", 
            "message" => "Failed to remove bookmark"
        ]);
    }
    
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>