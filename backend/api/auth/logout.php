<?php
// api/logout.php

header('Content-Type: application/json');
session_start();

// ลบข้อมูลทั้งหมดในเซสชัน
session_unset();
session_destroy();

echo json_encode(['success' => true, 'message' => 'ออกจากระบบสำเร็จ']);
?>
