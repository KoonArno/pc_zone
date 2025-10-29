<?php
// เริ่มต้น session
session_start();

// นำเข้าไฟล์ที่เชื่อมต่อกับฐานข้อมูล
include '../db.php';

// ตรวจสอบว่ามีการส่งค่า ID ของสินค้ามาหรือไม่
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // เตรียมคำสั่ง SQL สำหรับลบข้อมูลสินค้า
    $sql = "DELETE FROM product WHERE product_id = :product_id";

    // เตรียม statement สำหรับการลบ
    $stmt = $pdo->prepare($sql);

    // ดำเนินการลบสินค้าโดยส่งค่า product_id
    if ($stmt->execute(['product_id' => $product_id])) {
        // ลบสำเร็จ เก็บข้อความสำเร็จใน session
        $_SESSION['success_message'] = "สินค้าถูกลบเรียบร้อยแล้ว";
    } else {
        // ลบไม่สำเร็จ เก็บข้อความผิดพลาดใน session
        $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการลบสินค้า";
    }
}

// หลังจากลบเสร็จ กลับไปที่หน้า manage_products.php
header("Location: manage_products.php");
exit();
?>