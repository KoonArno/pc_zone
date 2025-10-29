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

    if (!isset($data->address_id) || !isset($data->user_id)) {
        echo json_encode(["status" => "error", "message" => "Missing required data: address_id and user_id"]);
        exit;
    }

    // ตรวจสอบข้อมูลที่จำเป็นต้องมี
    $required_fields = ['full_name', 'phone_number', 'address_line', 'district', 'subdistrict', 'city', 'postal_code', 'country'];
    foreach ($required_fields as $field) {
        if (!isset($data->$field) || trim($data->$field) === '') {
            echo json_encode(["status" => "error", "message" => "Missing required data: $field"]);
            exit;
        }
    }

    // ตรวจสอบความถูกต้องของข้อมูล (validation)
    if (!preg_match("/^[0-9]{10}$/", $data->phone_number)) {
        echo json_encode(["status" => "error", "message" => "Invalid phone number format"]);
        exit;
    }

    if (!preg_match("/^[0-9]{5}$/", $data->postal_code)) {
        echo json_encode(["status" => "error", "message" => "Invalid postal code format"]);
        exit;
    }

    // ตรวจสอบว่าที่อยู่มีอยู่จริงและเป็นของผู้ใช้หรือไม่
    $check_sql = "SELECT * FROM address WHERE address_id = ? AND user_id = ?";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([$data->address_id, $data->user_id]);

    if (!$check_stmt->fetch()) {
        echo json_encode(["status" => "error", "message" => "Address not found or not authorized"]);
        exit;
    }

    // อัปเดตข้อมูลที่อยู่
    $update_sql = "UPDATE address SET full_name = ?, phone_number = ?, address_line = ?, district = ?, subdistrict = ?, city = ?, postal_code = ?, country = ? WHERE address_id = ? AND user_id = ?";
    $update_stmt = $pdo->prepare($update_sql);
    $update_stmt->execute([$data->full_name, $data->phone_number, $data->address_line, $data->district, $data->subdistrict, $data->city, $data->postal_code, $data->country, $data->address_id, $data->user_id]);

    echo json_encode([
        "status" => "success",
        "message" => "Address updated successfully",
        "address_id" => $data->address_id
    ]);

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>