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
    
    // บันทึก log เพื่อการตรวจสอบ
    error_log("Received order data: " . print_r($data, true));
    
    // ตรวจสอบข้อมูลที่จำเป็น
    if (!isset($data->user_id) || !isset($data->total_price) || !isset($data->cart_items) || empty($data->cart_items)) {
        echo json_encode(["status" => "error", "message" => "Missing required data"]);
        exit;
    }
    
    // ตรวจสอบว่า user_id เป็นตัวเลขที่ถูกต้อง
    $user_id = intval($data->user_id);
    if ($user_id <= 0) {
        echo json_encode(["status" => "error", "message" => "Invalid user ID"]);
        exit;
    }
    
    // ตรวจสอบความถูกต้องของข้อมูลในตะกร้า
    $valid_items = [];
    $product_ids = []; // เก็บรายการ product_id ที่ถูกเลือกเพื่อใช้ในการลบ
    
    foreach ($data->cart_items as $item) {
        if (!isset($item->product_id) || !isset($item->quantity)) {
            continue; // ข้ามรายการที่ไม่มีข้อมูลครบ
        }
        
        $product_id = intval($item->product_id);
        $quantity = intval($item->quantity);
        
        // ตรวจสอบความถูกต้องของ product_id และ quantity
        if ($product_id <= 0 || $quantity <= 0) {
            continue; // ข้ามรายการที่ไม่ถูกต้อง
        }
        
        $valid_items[] = [
            'product_id' => $product_id,
            'quantity' => $quantity
        ];
        
        // เก็บ product_id ที่จะต้องลบออกจากรถเข็น
        $product_ids[] = $product_id;
    }
    
    // ถ้าไม่มีรายการสินค้าที่ถูกต้อง
    if (empty($valid_items)) {
        echo json_encode(["status" => "error", "message" => "No valid items found in cart"]);
        exit;
    }
    
    // เริ่ม transaction
    $pdo->beginTransaction();
    
    // บันทึกข้อมูลลงในตาราง orders
    $order_sql = "INSERT INTO orders (user_id, total_price, order_status, created_at) 
                  VALUES (?, ?, 'processing', CURRENT_TIMESTAMP())";
    $order_stmt = $pdo->prepare($order_sql);
    $order_stmt->execute([$user_id, $data->total_price]);
    
    // รับ order_id ที่เพิ่งถูกสร้าง
    $order_id = $pdo->lastInsertId();
    
    // บันทึกข้อมูลลงในตาราง order_items
    $item_sql = "INSERT INTO orders_item (order_id, product_id, quantity) VALUES (?, ?, ?)";
    $item_stmt = $pdo->prepare($item_sql);
    
    foreach ($valid_items as $item) {
        $item_stmt->execute([$order_id, $item['product_id'], $item['quantity']]);
    }
    
    // ลบเฉพาะสินค้าที่ถูกเลือกออกจากรถเข็น
    // ตรวจสอบว่ามีสินค้าที่ต้องลบหรือไม่
    if (!empty($product_ids)) {
        // สร้างเงื่อนไขสำหรับรายการ product_id
        $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
        
        // ลบเฉพาะรายการที่ถูกเลือกออกจากรถเข็น
        $clear_cart_sql = "DELETE FROM cart WHERE user_id = ? AND product_id IN ($placeholders)";
        $clear_cart_stmt = $pdo->prepare($clear_cart_sql);
        
        // รวม parameter สำหรับ execute
        $params = array_merge([$user_id], $product_ids);
        $clear_cart_stmt->execute($params);
    }
    
    // ยืนยัน transaction
    $pdo->commit();
    
    echo json_encode([
        "status" => "success", 
        "message" => "Order placed successfully",
        "order_id" => $order_id,
        "order_status" => "processing"
    ]);
    
} catch (PDOException $e) {
    // กรณีเกิดข้อผิดพลาด ให้ rollback
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Database error in create_order.php: " . $e->getMessage());
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>