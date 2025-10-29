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
    
    // ตรวจสอบว่าสินค้านี้ถูก bookmark แล้วหรือไม่
    $check_sql = "SELECT * FROM bookmark WHERE user_id = ? AND product_id = ?";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([$data->user_id, $data->product_id]);
    $existing_item = $check_stmt->fetch();
    
    if ($existing_item) {
        // ถ้ามีสินค้านี้อยู่แล้ว
        echo json_encode(["status" => "error", "message" => "Product already bookmarked"]);
    } else {
        // ถ้ายังไม่มีสินค้านี้ ให้เพิ่มรายการใหม่
        $insert_sql = "INSERT INTO bookmark (user_id, product_id) VALUES (?, ?)";
        $insert_stmt = $pdo->prepare($insert_sql);
        $insert_stmt->execute([$data->user_id, $data->product_id]);
        
        $new_id = $pdo->lastInsertId();
        
        echo json_encode([
            "status" => "success", 
            "message" => "Product bookmarked successfully",
            "id" => $new_id
        ]);
    }
    
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>