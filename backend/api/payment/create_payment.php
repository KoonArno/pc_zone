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
    // รับข้อมูลจาก POST request
    $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : null;
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : null;
    $address_id = isset($_POST['address_id']) ? $_POST['address_id'] : null;
    
    // ตรวจสอบว่ามีการส่งข้อมูลที่จำเป็นมาครบหรือไม่
    if (!$order_id || !$user_id || !$address_id) {
        echo json_encode(["status" => "error", "message" => "Missing required data"]);
        exit;
    }
    
    // ตรวจสอบว่ามีการอัปโหลดไฟล์รูปภาพหรือไม่
    if (!isset($_FILES['proof_image']) || $_FILES['proof_image']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(["status" => "error", "message" => "Payment proof image is required"]);
        exit;
    }
    
    // ตรวจสอบว่าคำสั่งซื้อมีอยู่จริงหรือไม่
    $check_order = $pdo->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
    $check_order->execute([$order_id, $user_id]);
    $order = $check_order->fetch();
    
    if (!$order) {
        echo json_encode(["status" => "error", "message" => "Order not found or does not belong to this user"]);
        exit;
    }
    
    // ตรวจสอบว่าที่อยู่มีอยู่จริงหรือไม่
    $check_address = $pdo->prepare("SELECT * FROM address WHERE address_id = ? AND user_id = ?");
    $check_address->execute([$address_id, $user_id]);
    $address = $check_address->fetch();
    
    if (!$address) {
        echo json_encode(["status" => "error", "message" => "Address not found or does not belong to this user"]);
        exit;
    }
    
    // อัปเดตที่อยู่ในคำสั่งซื้อ
    $update_order = $pdo->prepare("UPDATE orders SET address_id = ? WHERE order_id = ?");
    $update_order->execute([$address_id, $order_id]);
    
    // อัปโหลดรูปภาพ
    $upload_dir = '../../uploads/payment_proofs/';
    
    // สร้างโฟลเดอร์ถ้ายังไม่มี
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // กำหนดชื่อไฟล์ใหม่เพื่อป้องกันการซ้ำ
    $file_extension = pathinfo($_FILES['proof_image']['name'], PATHINFO_EXTENSION);
    $new_filename = 'payment_' . $order_id . '_' . time() . '.' . $file_extension;
    $upload_path = $upload_dir . $new_filename;
    
    // ย้ายไฟล์ที่อัปโหลดไปยังโฟลเดอร์ที่กำหนด
    if (move_uploaded_file($_FILES['proof_image']['tmp_name'], $upload_path)) {
        // บันทึกข้อมูลการชำระเงินลงในตาราง payment
        $insert_payment = $pdo->prepare("INSERT INTO payment (order_id, proof_image, payment_status) VALUES (?, ?, 'pending')");
        $insert_payment->execute([$order_id, $new_filename]);
        
        $payment_id = $pdo->lastInsertId();
        
        // อัปเดตสถานะคำสั่งซื้อเป็น 'paid'
        $update_order_status = $pdo->prepare("UPDATE orders SET status = 'paid' WHERE order_id = ?");
        $update_order_status->execute([$order_id]);
        
        echo json_encode([
            "status" => "success",
            "message" => "Payment submitted successfully",
            "payment_id" => $payment_id,
            "proof_image" => $new_filename
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to upload payment proof"]);
    }
    
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Error: " . $e->getMessage()]);
}
?>