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
        echo json_encode(["status" => "error", "message" => "Missing required data"]);
        exit;
    }
    
    $check_sql = "SELECT * FROM address WHERE address_id = ? AND user_id = ?";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([$data->address_id, $data->user_id]);
    
    if (!$check_stmt->fetch()) {
        echo json_encode(["status" => "error", "message" => "Address not found or not authorized"]);
        exit;
    }
    
    $delete_sql = "DELETE FROM address WHERE address_id = ? AND user_id = ?";
    $delete_stmt = $pdo->prepare($delete_sql);
    $delete_stmt->execute([$data->address_id, $data->user_id]);
    
    echo json_encode([
        "status" => "success", 
        "message" => "Address deleted successfully",
        "address_id" => $data->address_id
    ]);
    
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>