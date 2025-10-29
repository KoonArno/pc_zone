<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include '../database/db.php'; // เชื่อมต่อฐานข้อมูล

try {
    // คำสั่ง SQL ดึงข้อมูลทั้งหมดจากตาราง
    $sql = "SELECT product_id, product_name, description, image, price, type FROM product";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    // ดึงข้อมูลทั้งหมดในรูปแบบ associative array
    $pc_zone = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // ตรวจสอบว่ามีข้อมูลหรือไม่
    if ($pc_zone) {
        echo json_encode(["status" => "success", "product" => $pc_zone]);
    } else {
        echo json_encode(["status" => "error", "message" => "No products found"]);
    }
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
