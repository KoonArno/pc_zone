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
    $data = json_decode(file_get_contents("php://input"));
    
    // ตรวจสอบข้อมูลที่จำเป็น
    if (!isset($data->user_id) || !isset($data->total_price) || !isset($data->address_id) || !isset($data->image)) {
        echo json_encode(["status" => "error", "message" => "Missing required data"]);
        exit;
    }
    
    // ตรวจสอบการมีอยู่ของผู้ใช้
    $check_user = "SELECT * FROM users WHERE user_id = ?";
    $user_stmt = $pdo->prepare($check_user);
    $user_stmt->execute([$data->user_id]);
    
    if (!$user_stmt->fetch()) {
        echo json_encode(["status" => "error", "message" => "User not found"]);
        exit;
    }
    
    // ตรวจสอบการมีอยู่ของที่อยู่
    $check_address = "SELECT * FROM address WHERE address_id = ? AND user_id = ?";
    $address_stmt = $pdo->prepare($check_address);
    $address_stmt->execute([$data->address_id, $data->user_id]);
    
    if (!$address_stmt->fetch()) {
        echo json_encode(["status" => "error", "message" => "Address not found or doesn't belong to this user"]);
        exit;
    }
    
    // ตรวจสอบการมีอยู่ของออเดอร์ที่รอการชำระเงิน
    $check_order = "SELECT order_id FROM orders WHERE user_id = ? AND order_status = 'processing' ORDER BY order_id DESC LIMIT 1";
    $order_stmt = $pdo->prepare($check_order);
    $order_stmt->execute([$data->user_id]);
    $order = $order_stmt->fetch();
    
    if (!$order) {
        echo json_encode(["status" => "error", "message" => "No pending order found"]);
        exit;
    }
    
    $order_id = $order['order_id'];
    
    // เริ่มทำ transaction
    $pdo->beginTransaction();
    
    // สร้างชื่อไฟล์ที่ไม่ซ้ำกัน
    $image_name = 'payment_' . $data->user_id . '_' . time() . '.jpg';
    $upload_dir = '../../uploads/payments/';
    
    // ตรวจสอบและสร้างไดเรกทอรีหากไม่มี
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // ถอดรหัส base64 และบันทึกไฟล์
    $image_data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data->image));
    $file_path = $upload_dir . $image_name;
    file_put_contents($file_path, $image_data);
    
    // เพิ่มข้อมูลการชำระเงิน
    $insert_payment = "INSERT INTO payment (order_id, proof_image, payment_status) VALUES (?, ?, 'pending')";
    $payment_stmt = $pdo->prepare($insert_payment);
    $payment_stmt->execute([$order_id, $image_name]);
    
    // ดึง payment_id ที่เพิ่งเพิ่ม
    $payment_id = $pdo->lastInsertId();
    
    // อัปเดตข้อมูลคำสั่งซื้อ
    $update_order = "UPDATE orders SET payment_id = ?, address_id = ? WHERE order_id = ?";
    $update_order_stmt = $pdo->prepare($update_order);
    $update_order_stmt->execute([$payment_id, $data->address_id, $order_id]);
    
    // ถ้าต้องการล้างตะกร้าหลังจากที่ชำระเงินแล้ว
    if (isset($data->clear_cart) && $data->clear_cart === true) {
        $clear_cart = "DELETE FROM cart WHERE user_id = ?";
        $cart_stmt = $pdo->prepare($clear_cart);
        $cart_stmt->execute([$data->user_id]);
    }
    
    // ยืนยัน transaction
    $pdo->commit();
    
    echo json_encode([
        "status" => "success", 
        "message" => "Payment processed successfully",
        "payment_id" => $payment_id,
        "order_id" => $order_id // ส่งค่า order_id กลับไปด้วย
    ]);
    
} catch (PDOException $e) {
    // ถ้าเกิดข้อผิดพลาด ยกเลิก transaction
    $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
} catch (Exception $e) {
    // ถ้าเกิดข้อผิดพลาดอื่นๆ ยกเลิก transaction
    $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => "Error: " . $e->getMessage()]);
}
?>