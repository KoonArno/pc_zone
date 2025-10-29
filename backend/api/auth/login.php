<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

// เชื่อมต่อฐานข้อมูล
include '../database/db.php';

// รับข้อมูลจาก React Native
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->identifier) && !empty($data->password)) {
    $identifier = htmlspecialchars(strip_tags($data->identifier));
    $password = $data->password;

    // ตรวจสอบว่า $identifier เป็น email หรือ username
    $is_email = filter_var($identifier, FILTER_VALIDATE_EMAIL);

    // ค้นหาผู้ใช้ในฐานข้อมูล (ทั้งจาก email และ username)
    if ($is_email) {
        $sql = "SELECT user_id, username, email, password_hash, role FROM users WHERE email = ?";
    } else {
        $sql = "SELECT user_id, username, email, password_hash, role FROM users WHERE username = ?";
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$identifier]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        echo json_encode([
            "status" => "success",
            "message" => "เข้าสู่ระบบสำเร็จ",
            "user" => [
                "user_id" => $user["user_id"],
                "username" => $user["username"],
                "email" => $user["email"],
                "role" => $user["role"]
            ]
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "ชื่อผู้ใช้/อีเมล หรือรหัสผ่านไม่ถูกต้อง"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "กรุณากรอกข้อมูลให้ครบถ้วน"]);
}
?>