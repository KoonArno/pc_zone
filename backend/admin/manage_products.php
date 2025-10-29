<?php
// เริ่มต้น session
session_start();

// นำเข้าไฟล์ admin_header.php
include 'admin_header.php';
// include database connection from db.php
include '../db.php';

// ตรวจสอบว่ามีข้อความสำเร็จใน session หรือไม่
if (isset($_SESSION['success_message'])) {
    echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
            <i class='fas fa-check-circle me-2'></i>" . $_SESSION['success_message'] . "
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
          </div>";
    // ลบข้อความหลังจากแสดง
    unset($_SESSION['success_message']);
}

// ตรวจสอบว่ามีการส่งคำค้นหาหรือไม่
$searchQuery = "";
if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
    // ดึงข้อมูล products โดยกรองตามคำค้นหา
    $sql = "SELECT * FROM product WHERE product_name LIKE ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%$searchQuery%"]);
    $result = $stmt;
} else {
    // ดึงข้อมูล products ทั้งหมด
    $sql = "SELECT * FROM product";
    $result = $pdo->query($sql);
}

// Count total products
$countSql = "SELECT COUNT(*) FROM product";
if (isset($_GET['search'])) {
    $countSql .= " WHERE product_name LIKE ?";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute(["%$searchQuery%"]);
} else {
    $countStmt = $pdo->query($countSql);
}
$totalProducts = $countStmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products | PC ZONE Admin</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts - Kanit for Thai language support -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #f8f9fa;
        }
        
        .page-header {
            background: linear-gradient(135deg, #4a6fff 0%, #2746e5 100%);
            color: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .search-wrapper {
            background-color: white;
            border-radius: 0.5rem;
            padding: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }
        
        .table-wrapper {
            background-color: white;
            border-radius: 0.5rem;
            padding: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            overflow-x: auto;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table thead {
            background-color: #f8f9fa;
        }
        
        .table thead th {
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #495057;
        }
        
        .table-bordered td, .table-bordered th {
            border: 1px solid #ebedf2;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .product-image {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 4px;
            transition: transform 0.2s;
        }
        
        .product-image:hover {
            transform: scale(1.8);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            z-index: 100;
            position: relative;
        }
        
        .btn-action {
            padding: 0.375rem 0.5rem;
            font-size: 0.875rem;
        }
        
        .btn-action i {
            margin-right: 4px;
        }
        
        .description-column {
            width: 25%;
            max-width: 300px;
        }
        
        .actions-column {
            width: 15%;
            white-space: nowrap;
        }
        
        .truncate-text {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .badge-type {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
            font-weight: 500;
        }
        
        .product-counter {
            background-color: #e9ecef;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.9rem;
            color: #495057;
            display: inline-flex;
            align-items: center;
        }
        
        .product-counter i {
            margin-right: 0.5rem;
            color: #4a6fff;
        }
        
        .search-input {
            border-radius: 50px 0 0 50px;
            padding-left: 1rem;
            border-right: none;
        }
        
        .search-button {
            border-radius: 0 50px 50px 0;
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }
        
        .action-button-group {
            display: flex;
            gap: 0.5rem;
        }
    </style>
</head>

<body>
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-header">
            <div class="header-content">
                <div>
                    <h2 class="mb-1"><i class="fas fa-box-open me-2"></i>จัดการสินค้า</h2>
                    <p class="mb-0 opacity-75">เพิ่ม, แก้ไข หรือลบสินค้าในระบบ</p>
                </div>
                <div class="product-counter">
                    <i class="fas fa-tag"></i> สินค้าทั้งหมด: <?php echo $totalProducts; ?> รายการ
                    <?php if (isset($_GET['search'])): ?>
                    <span class="ms-2">(ค้นหา: "<?php echo htmlspecialchars($searchQuery); ?>")</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Search and Add Product -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="search-wrapper">
                    <form method="GET" action="" class="d-flex">
                        <input type="text" name="search" class="form-control search-input" 
                            placeholder="ค้นหาสินค้าตามชื่อ..." value="<?= htmlspecialchars($searchQuery) ?>">
                        <button type="submit" class="btn btn-primary search-button">
                            <i class="fas fa-search me-1"></i> ค้นหา
                        </button>
                        <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                        <a href="manage_products.php" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-times me-1"></i> ล้างการค้นหา
                        </a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="add_product.php" class="btn btn-success btn-lg w-100">
                    <i class="fas fa-plus-circle me-2"></i> เพิ่มสินค้าใหม่
                </a>
            </div>
        </div>
        
        <!-- Products Table -->
        <div class="table-wrapper">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 5%">ID</th>
                        <th style="width: 15%">ชื่อสินค้า</th>
                        <th class="description-column">รายละเอียด</th>
                        <th class="text-center" style="width: 10%">รูปภาพ</th>
                        <th class="text-center" style="width: 10%">ราคา</th>
                        <th class="text-center" style="width: 10%">ประเภท</th>
                        <th class="actions-column text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $rowCount = 0;
                    while ($row = $result->fetch()): 
                        $rowCount++;
                        // Map product types to badge colors
                        $typeColor = "primary";
                        switch(strtolower($row['type'])) {
                            case 'cpu': $typeColor = "danger"; break;
                            case 'gpu': $typeColor = "success"; break;
                            case 'ram': $typeColor = "info"; break;
                            case 'storage': $typeColor = "warning"; break;
                            case 'motherboard': $typeColor = "dark"; break;
                        }
                    ?>
                    <tr>
                        <td class="text-center align-middle"><?php echo $row['product_id']; ?></td>
                        <td class="align-middle fw-medium"><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td class="align-middle">
                            <div class="truncate-text"><?php echo htmlspecialchars($row['description']); ?></div>
                        </td>
                        <td class="text-center align-middle">
                            <?php if ($row['image']): ?>
                                <img src="../image/<?php echo htmlspecialchars($row['image']); ?>" class="product-image" alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                            <?php else: ?>
                                <span class="text-muted"><i class="fas fa-image"></i> ไม่มีรูปภาพ</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center align-middle">
                            <span class="badge bg-light text-dark p-2 fw-medium">
                                ฿<?php echo number_format($row['price'], 2); ?>
                            </span>
                        </td>
                        <td class="text-center align-middle">
                            <span class="badge bg-<?php echo $typeColor; ?> badge-type">
                                <?php echo htmlspecialchars($row['type']); ?>
                            </span>
                        </td>
                        <td class="text-center align-middle">
                            <div class="action-button-group">
                                <a href="edit_product.php?id=<?php echo $row['product_id']; ?>" class="btn btn-warning btn-action">
                                    <i class="fas fa-edit"></i> แก้ไข
                                </a>
                                <a href="delete_product.php?id=<?php echo $row['product_id']; ?>" 
                                   class="btn btn-danger btn-action"
                                   onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบ <?php echo htmlspecialchars($row['product_name']); ?>?')">
                                    <i class="fas fa-trash-alt"></i> ลบ
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    
                    <?php if ($rowCount == 0): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-search fa-2x mb-3"></i>
                                <p class="mb-0">ไม่พบสินค้าที่ค้นหา</p>
                                <?php if (isset($_GET['search'])): ?>
                                <p class="mb-3">ลองค้นหาด้วยคำค้นอื่น หรือ <a href="manage_products.php">แสดงสินค้าทั้งหมด</a></p>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>