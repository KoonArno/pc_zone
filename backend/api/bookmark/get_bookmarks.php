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
    
    // ตรวจสอบข้อมูลที่จำเป็น
    if (!$user_id) {
        echo json_encode(["status" => "error", "message" => "Missing user_id parameter"]);
        exit;
    }
    
    // ดึงข้อมูลสินค้าที่อยู่ในรายการโปรดของผู้ใช้
    $query = "
        SELECT p.*, b.bookmark_id 
        FROM bookmark b
        JOIN product p ON b.product_id = p.product_id
        WHERE b.user_id = ?
        ORDER BY b.bookmark_id DESC
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$user_id]);
    $bookmarks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        "status" => "success", 
        "bookmarks" => $bookmarks,
        "total" => count($bookmarks)
    ]);
    
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>