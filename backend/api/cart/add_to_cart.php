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
    if (!isset($data->user_id) || !isset($data->product_id) || !isset($data->quantity)) {
        echo json_encode(["status" => "error", "message" => "Missing required data"]);
        exit;
    }
    
    // ตรวจสอบว่าสินค้านี้มีอยู่ในรถเข็นแล้วหรือไม่
    $check_sql = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([$data->user_id, $data->product_id]);
    $existing_item = $check_stmt->fetch();
    
    if ($existing_item) {
        // ถ้ามีสินค้านี้อยู่แล้ว ให้เพิ่มจำนวน
        $new_quantity = $existing_item['quantity'] + $data->quantity;
        $update_sql = "UPDATE cart SET quantity = ? WHERE id = ?";
        $update_stmt = $pdo->prepare($update_sql);
        $update_stmt->execute([$new_quantity, $existing_item['id']]);
        
        echo json_encode([
            "status" => "success", 
            "message" => "Cart updated successfully",
            "id" => $existing_item['id'],
            "quantity" => $new_quantity
        ]);
    } else {
        // ถ้ายังไม่มีสินค้านี้ ให้เพิ่มรายการใหม่
        $insert_sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
        $insert_stmt = $pdo->prepare($insert_sql);
        $insert_stmt->execute([$data->user_id, $data->product_id, $data->quantity]);
        
        $new_id = $pdo->lastInsertId();
        
        echo json_encode([
            "status" => "success", 
            "message" => "Product added to cart successfully",
            "id" => $new_id,
            "quantity" => $data->quantity
        ]);
    }
    
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>