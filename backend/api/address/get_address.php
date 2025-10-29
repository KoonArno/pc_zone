<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include '../database/db.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่าเป็น GET request
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit;
}

try {
    // ตรวจสอบว่ามีการส่ง user_id มาหรือไม่
    if (!isset($_GET['user_id'])) {
        echo json_encode(["status" => "error", "message" => "User ID is required"]);
        exit;
    }
    
    $user_id = $_GET['user_id'];
    
    // ดึงข้อมูลผู้ใช้จากตาราง users
    $sql_user = "SELECT username, email FROM users WHERE user_id = ?";
    $stmt_user = $pdo->prepare($sql_user);
    $stmt_user->execute([$user_id]);
    $user = $stmt_user->fetch();
    
    if (!$user) {
        echo json_encode(["status" => "error", "message" => "User not found"]);
        exit;
    }

    // ดึงข้อมูลที่อยู่จากตาราง address - เพิ่ม address_id และ is_default ในคำสั่ง SQL
    $sql_address = "SELECT address_id, full_name, phone_number, address_line, district, subdistrict, city, postal_code, country, is_default 
                    FROM address 
                    WHERE user_id = ?";
    $stmt_address = $pdo->prepare($sql_address);
    $stmt_address->execute([$user_id]);
    $addresses = $stmt_address->fetchAll();
    
    echo json_encode([
        "status" => "success",
        "message" => "User data retrieved successfully",
        "user" => $user,
        "addresses" => $addresses
    ]);
    
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>