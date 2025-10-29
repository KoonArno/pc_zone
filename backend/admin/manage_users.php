<?php
// เริ่มต้น session
session_start();

// นำเข้าไฟล์ admin_header.php
include 'admin_header.php';
// include database connection from db.php
include '../db.php';

// ตรวจสอบว่าผู้ใช้เป็น Admin หรือไม่
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header('Location: login.php');
    exit();
}

// ตรวจสอบว่ามีข้อความสำเร็จใน session หรือไม่
if (isset($_SESSION['success_message'])) {
    echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
            <i class='bi bi-check-circle-fill me-2'></i>" . htmlspecialchars($_SESSION['success_message']) . "
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
          </div>";
    // ลบข้อความหลังจากแสดง
    unset($_SESSION['success_message']);
}

// ตรวจสอบว่ามีการส่งคำค้นหาหรือไม่
$searchQuery = "";
if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
    // ดึงข้อมูล Users โดยกรองตามคำค้นหา
    $sql = "SELECT * FROM users WHERE username LIKE ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%$searchQuery%"]);
    $users = $stmt->fetchAll();
} else {
    // ดึงข้อมูล Users ทั้งหมด
    $sql = "SELECT * FROM users";
    $stmt = $pdo->query($sql);
    $users = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการผู้ใช้งาน | Admin Panel</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <!-- Google Fonts - Sarabun for Thai language -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 1200px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        .card-header {
            background-color: #4e73df;
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 15px 20px;
        }
        .table {
            margin-bottom: 0;
        }
        .table th {
            background-color: #f2f4f8;
            vertical-align: middle;
        }
        .btn-primary {
            background-color: #4e73df;
            border-color: #4e73df;
        }
        .btn-primary:hover {
            background-color: #2e59d9;
            border-color: #2653d4;
        }
        .btn-danger {
            background-color: #e74a3b;
            border-color: #e74a3b;
        }
        .btn-danger:hover {
            background-color: #d52a1a;
            border-color: #c9271a;
        }
        .search-box {
            border-radius: 30px;
            overflow: hidden;
        }
        .search-input {
            border-right: none;
            padding-left: 20px;
        }
        .search-btn {
            border-radius: 0 30px 30px 0 !important;
            padding-right: 25px;
        }
        .badge-role {
            font-size: 12px;
            padding: 5px 10px;
            border-radius: 30px;
        }
        .badge-admin {
            background-color: #4e73df;
        }
        .badge-user {
            background-color: #1cc88a;
        }
        .table-responsive {
            border-radius: 0 0 10px 10px;
            overflow: hidden;
        }
        .page-header {
            margin-bottom: 25px;
            border-bottom: 1px solid #e3e6f0;
            padding-bottom: 20px;
        }
        .user-count {
            color: #5a5c69;
            font-size: 16px;
        }
        .action-btn {
            padding: 5px 10px;
            font-size: 14px;
        }
        .action-btn i {
            margin-right: 5px;
        }
        .created-date {
            font-size: 13px;
            color: #6c757d;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0 text-gray-800">จัดการผู้ใช้งาน</h1>
            <p class="user-count mt-2 mb-0">
                <i class="bi bi-people-fill me-1"></i> จำนวนผู้ใช้ทั้งหมด: <?= count($users) ?> คน
                <?php if (!empty($searchQuery)): ?>
                    <span class="ms-2">(กำลังค้นหา: "<?= htmlspecialchars($searchQuery) ?>")</span>
                <?php endif; ?>
            </p>
        </div>
        <a href="add_user.php" class="btn btn-success">
            <i class="bi bi-person-plus-fill"></i> เพิ่มผู้ใช้ใหม่
        </a>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>รายชื่อผู้ใช้ทั้งหมด</h5>
            <!-- ฟอร์มค้นหา -->
            <form method="GET" action="" class="mb-0">
                <div class="input-group search-box">
                    <input type="text" name="search" class="form-control search-input" placeholder="ค้นหาด้วยชื่อผู้ใช้..." value="<?= htmlspecialchars($searchQuery) ?>">
                    <button type="submit" class="btn btn-primary search-btn">
                        <i class="bi bi-search me-1"></i> ค้นหา
                    </button>
                </div>
            </form>
        </div>
        
        <div class="table-responsive">
            <!-- ตารางแสดงข้อมูลผู้ใช้ -->
            <table class="table table-hover align-middle">
                <thead>
                    <tr class="text-center">
                        <th width="5%">ID</th>
                        <th width="20%">ชื่อผู้ใช้</th>
                        <th width="25%">อีเมล</th>
                        <th width="15%">สถานะ</th>
                        <th width="20%">วันที่สร้าง</th>
                        <th width="15%">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($users) > 0): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="text-center"><?= htmlspecialchars($user['user_id']) ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-light text-dark rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="bi bi-person"></i>
                                        </div>
                                        <?= htmlspecialchars($user['username']) ?>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td class="text-center">
                                    <?php if ($user['role'] == 'Admin'): ?>
                                        <span class="badge badge-role badge-admin"><i class="bi bi-shield-lock-fill me-1"></i><?= htmlspecialchars($user['role']) ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-role badge-user"><i class="bi bi-person-fill me-1"></i><?= htmlspecialchars($user['role']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="created-date">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        <?= date('d/m/Y H:i', strtotime($user['created_at'])) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="edit_user.php?id=<?= $user['user_id'] ?>" class="btn btn-sm btn-primary action-btn">
                                            <i class="bi bi-pencil-square"></i> แก้ไข
                                        </a>
                                        <a href="delete_user.php?id=<?= $user['user_id'] ?>" class="btn btn-sm btn-danger action-btn" 
                                           onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบผู้ใช้ <?= htmlspecialchars($user['username']) ?>?')">
                                            <i class="bi bi-trash"></i> ลบ
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="alert alert-info mb-0">
                                    <i class="bi bi-info-circle-fill me-2"></i> ไม่พบข้อมูลผู้ใช้
                                    <?php if (!empty($searchQuery)): ?>
                                        ที่ตรงกับคำค้นหา "<?= htmlspecialchars($searchQuery) ?>"
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script>
    // เพิ่มการเปลี่ยนสีแถวเมื่อ hover
    document.addEventListener('DOMContentLoaded', function() {
        // ทำให้ alert สามารถปิดได้อัตโนมัติหลังจาก 5 วินาที
        const alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                const closeButton = alert.querySelector('.btn-close');
                if (closeButton) {
                    closeButton.click();
                }
            }, 5000);
        });
    });
</script>
</body>
</html>