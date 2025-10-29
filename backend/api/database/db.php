<?php
// db.php - เชื่อมต่อฐานข้อมูล MySQL ด้วย PDO

$host = 'localhost';  // ชื่อเซิร์ฟเวอร์ของฐานข้อมูล
$dbname = 'pc_zone';  // ชื่อฐานข้อมูล
$username = 'pc';  // ชื่อผู้ใช้ฐานข้อมูล
$password = 'password';  // รหัสผ่านฐานข้อมูล
$charset = 'utf8mb4';  // รองรับภาษาไทยและอักขระพิเศษ

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // โหมดแสดงข้อผิดพลาด
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,  // ดึงข้อมูลแบบ associative array
    PDO::ATTR_EMULATE_PREPARES   => false,  // ปิดการใช้ Emulated Prepared Statements
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    error_log("Database connection error: " . $e->getMessage()); // บันทึก Log ข้อผิดพลาด
    die(json_encode(["status" => "error", "message" => "Database connection failed"]));
}
?>
