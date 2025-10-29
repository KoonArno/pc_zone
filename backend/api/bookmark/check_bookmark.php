<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include '../database/db.php';

// ตรวจสอบว่าเป็น GET request
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit;
}

try {
    // รับข้อมูลจาก query parameters
    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
    $product_id = isset($_GET['product_id']) ? $_GET['product_id'] : null;
    
    // ตรวจสอบข้อมูลที่จำเป็น
    if (!$user_id || !$product_id) {
        echo json_encode(["status" => "error", "message" => "Missing required data"]);
        exit;
    }
    
    // ตรวจสอบว่าสินค้านี้ถูก bookmark แล้วหรือไม่
    $check_sql = "SELECT * FROM bookmark WHERE user_id = ? AND product_id = ?";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([$user_id, $product_id]);
    $existing_item = $check_stmt->fetch();
    
    if ($existing_item) {
        // ถ้ามีสินค้านี้อยู่แล้ว
        echo json_encode(["status" => "success", "bookmarked" => true]);
    } else {
        // ถ้ายังไม่มีสินค้านี้
        echo json_encode(["status" => "success", "bookmarked" => false]);
    }
    
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>