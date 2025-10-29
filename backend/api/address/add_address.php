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
    
    // ตรวจสอบข้อมูลที่จำเป็น
    if (!isset($data->user_id) || !isset($data->full_name) || 
        !isset($data->phone_number) || !isset($data->address_line) || 
        !isset($data->district) || !isset($data->subdistrict) || 
        !isset($data->city) || !isset($data->postal_code) || 
        !isset($data->country)) {
        echo json_encode(["status" => "error", "message" => "Missing required data"]);
        exit;
    }
    
    // ตรวจสอบว่า user มีอยู่ในระบบหรือไม่
    $check_user = "SELECT * FROM users WHERE user_id = ?";
    $user_stmt = $pdo->prepare($check_user);
    $user_stmt->execute([$data->user_id]);
    
    if (!$user_stmt->fetch()) {
        echo json_encode(["status" => "error", "message" => "User not found"]);
        exit;
    }
    
    // ตรวจสอบว่าจะตั้งเป็นที่อยู่หลักหรือไม่ (0 = default, 1 = not default)
    $is_default = isset($data->is_default) ? $data->is_default : 1; // ถ้าไม่ระบุ ให้เป็น 1 (ไม่ใช่ที่อยู่หลัก)
    
    // ถ้าตั้งเป็นที่อยู่หลัก (is_default = 0) ให้ยกเลิกที่อยู่หลักเดิม โดยเปลี่ยนเป็น 1 ทั้งหมด
    if ($is_default == 0) {
        $reset_default = "UPDATE address SET is_default = 1 WHERE user_id = ? AND is_default = 0";
        $reset_stmt = $pdo->prepare($reset_default);
        $reset_stmt->execute([$data->user_id]);
    }
    
    // เพิ่มที่อยู่ใหม่
    $insert_sql = "INSERT INTO address (user_id, full_name, phone_number, address_line, district, subdistrict, city, postal_code, country, is_default) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $insert_stmt = $pdo->prepare($insert_sql);
    $insert_stmt->execute([
        $data->user_id,
        $data->full_name,
        $data->phone_number,
        $data->address_line,
        $data->district,
        $data->subdistrict,
        $data->city,
        $data->postal_code,
        $data->country,
        $is_default
    ]);
    
    // ดึง address_id ที่เพิ่งเพิ่ม
    $address_id = $pdo->lastInsertId();
    
    echo json_encode([
        "status" => "success", 
        "message" => "Address added successfully",
        "address_id" => $address_id
    ]);
    
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>