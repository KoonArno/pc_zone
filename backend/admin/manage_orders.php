<?php
// เริ่มต้น session
session_start();

// นำเข้าไฟล์ admin_header.php
include 'admin_header.php';
// include database connection from db.php
include '../db.php';

// Check if the user is an Admin
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
    // ดึงข้อมูลคำสั่งซื้อโดยกรองตามคำค้นหา
    $sql = "SELECT o.order_id, o.user_id, o.total_price, o.order_status, o.created_at, u.username, a.full_name, a.phone_number, a.address_line, a.city, a.postal_code, a.country
            FROM orders o
            JOIN users u ON o.user_id = u.user_id
            JOIN address a ON o.address_id = a.address_id
            WHERE u.username LIKE ? OR a.full_name LIKE ? OR o.order_id LIKE ?
            ORDER BY o.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%$searchQuery%", "%$searchQuery%", "%$searchQuery%"]);
    $orders = $stmt->fetchAll();
} else {
    // ดึงข้อมูลคำสั่งซื้อทั้งหมด
    $sql = "SELECT o.order_id, o.user_id, o.total_price, o.order_status, o.created_at, u.username, a.full_name, a.phone_number, a.address_line, a.city, a.postal_code, a.country
            FROM orders o
            JOIN users u ON o.user_id = u.user_id
            JOIN address a ON o.address_id = a.address_id
            ORDER BY o.created_at DESC";
    $stmt = $pdo->query($sql);
    $orders = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #6366f1;
            --primary-hover: #4f46e5;
            --secondary-color: #f8fafc;
            --dark-color: #1e293b;
            --light-color: #f1f5f9;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --info-color: #06b6d4;
            --danger-color: #ef4444;
            --neutral-color: #64748b;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --border-radius: 0.75rem;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--secondary-color);
            color: var(--dark-color);
            min-height: 100vh;
            line-height: 1.6;
        }
        
        .dashboard-container {
            padding: 2rem 0;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .card {
            background-color: #fff;
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            margin-bottom: 2rem;
            overflow: hidden;
        }
        
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .page-title {
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .page-title i {
            color: var(--primary-color);
            font-size: 1.75rem;
        }
        
        .btn-back {
            color: var(--neutral-color);
            border-color: #e2e8f0;
            font-weight: 500;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
        }
        
        .btn-back:hover {
            background-color: var(--light-color);
            color: var(--dark-color);
            transform: translateY(-1px);
        }
        
        .search-form {
            background-color: #fff;
            padding: 1.5rem 2rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid #e2e8f0;
        }
        
        .input-group {
            box-shadow: var(--shadow-sm);
            border-radius: 0.5rem;
            overflow: hidden;
        }
        
        .input-group-text {
            background-color: #fff;
            border-color: #e2e8f0;
            color: var(--neutral-color);
        }
        
        .form-control {
            border-color: #e2e8f0;
            font-size: 0.95rem;
            padding: 0.75rem 1rem;
            color: var(--dark-color);
        }
        
        .form-control:focus {
            box-shadow: none;
            border-color: var(--primary-color);
        }
        
        .search-button {
            background-color: var(--primary-color);
            color: #fff;
            border: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .search-button:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
        }
        
        .total-orders {
            background-color: var(--light-color);
            padding: 0.75rem 1.25rem;
            border-radius: 0.5rem;
            font-size: 0.95rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            margin-left: 2rem;
            color: var(--neutral-color);
        }
        
        .total-orders i {
            color: var(--primary-color);
        }
        
        .table-responsive {
            padding: 0 1rem;
        }
        
        .table {
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0 0.5rem;
        }
        
        .table thead {
            background-color: transparent;
        }
        
        .table thead th {
            font-weight: 600;
            color: var(--neutral-color);
            border: none;
            padding: 1rem 1.25rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .table tbody td {
            padding: 1.25rem;
            vertical-align: middle;
            border: none;
            background-color: #fff;
        }
        
        .table tbody tr {
            box-shadow: var(--shadow-sm);
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }
        
        .table tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        
        .table tbody tr td:first-child {
            border-top-left-radius: 0.5rem;
            border-bottom-left-radius: 0.5rem;
        }
        
        .table tbody tr td:last-child {
            border-top-right-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
        }
        
        .order-id {
            font-family: monospace;
            font-weight: 600;
            background-color: #f8fafc;
            padding: 0.4rem 0.8rem;
            border-radius: 0.5rem;
            font-size: 0.9rem;
            border: 1px solid #e2e8f0;
        }
        
        .username {
            font-weight: 600;
            color: var(--primary-color);
            display: block;
            margin-bottom: 0.25rem;
        }
        
        .price-column {
            font-weight: 700;
            color: var(--dark-color);
            text-align: right;
            font-size: 1.05rem;
        }
        
        .badge {
            padding: 0.5rem 1rem;
            font-weight: 500;
            border-radius: 2rem;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }
        
        .badge-pending {
            background-color: var(--warning-color);
            color: #fff;
        }
        
        .badge-processing {
            background-color: var(--info-color);
            color: #fff;
        }
        
        .badge-shipped {
            background-color: #818cf8;
            color: #fff;
        }
        
        .badge-delivered {
            background-color: #fb923c;
            color: #fff;
        }
        
        .badge-done {
            background-color: var(--success-color);
            color: #fff;
        }
        
        .badge-rejected {
            background-color: var(--danger-color);
            color: #fff;
        }

        .address-info {
            font-size: 0.9rem;
            line-height: 1.5;
        }
        
        .address-info strong {
            color: var(--dark-color);
            font-weight: 600;
        }
        
        .text-muted {
            color: var(--neutral-color) !important;
        }
        
        .btn-view {
            background-color: var(--primary-color);
            color: #fff;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            font-weight: 500;
            border: none;
        }
        
        .btn-view:hover {
            background-color: var(--primary-hover);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        
        .no-orders {
            padding: 4rem 2rem;
            text-align: center;
            color: var(--neutral-color);
            background-color: #fff;
            border-radius: var(--border-radius);
        }
        
        .no-orders i {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            color: var(--neutral-color);
        }
        
        .no-orders h4 {
            color: var(--dark-color);
            font-weight: 600;
            margin-bottom: 0.75rem;
        }
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
            font-weight: 500;
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
        }
        
        .date-formatted {
            color: var(--neutral-color);
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }
        
        .date-formatted i {
            font-size: 0.9rem;
        }
        
        .alert {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: var(--shadow-sm);
        }
        
        /* Media Queries */
        @media (max-width: 992px) {
            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .btn-back {
                align-self: flex-end;
            }
            
            .table-responsive {
                padding: 0;
            }
        }
        
        @media (max-width: 767px) {
            .dashboard-container {
                padding: 1rem;
            }
            
            .card-header, .search-form {
                padding: 1.25rem;
            }
            
            .page-title {
                font-size: 1.25rem;
            }
            
            .total-orders {
                margin-left: 1.25rem;
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>

<div class="container-fluid dashboard-container">
    <div class="card">
        <div class="card-header">
            <h2 class="page-title"><i class="bi bi-box-seam"></i>Manage Orders</h2>
            <a href="dashboard.php" class="btn btn-back">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
        </div>
        
        <div class="card-body">
            <!-- ฟอร์มค้นหา -->
            <div class="search-form">
                <form method="GET" action="">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Search by Username, Name, or Order ID" 
                               value="<?= htmlspecialchars($searchQuery) ?>">
                        <button type="submit" class="btn search-button">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </div>
                </form>
            </div>
            
            <?php if (count($orders) > 0): ?>
                <div class="total-orders">
                    <i class="bi bi-filter-square-fill"></i>
                    Showing <?= count($orders) ?> order(s) <?= $searchQuery ? "matching \"" . htmlspecialchars($searchQuery) . "\"" : "" ?>
                </div>
                
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>User</th>
                                <th class="price-column">Total</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Shipping Address</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><span class="order-id"><?= htmlspecialchars($order['order_id']) ?></span></td>
                                    <td><span class="username"><?= htmlspecialchars($order['username']) ?></span></td>
                                    <td class="price-column"><?= number_format($order['total_price'], 2) ?> ฿</td>
                                    <td>
                                    <?php
                                        $statusClass = '';
                                        switch(strtolower($order['order_status'])) {
                                            case 'pending':
                                                $statusClass = 'badge-pending';
                                                $icon = 'bi-clock-history';
                                                break;
                                            case 'processing':
                                                $statusClass = 'badge-processing';
                                                $icon = 'bi-gear-wide-connected';
                                                break;
                                            case 'shipping':
                                                $statusClass = 'badge-shipped';
                                                $icon = 'bi-box-seam';
                                                break;
                                            case 'shipped':
                                                $statusClass = 'badge-delivered';
                                                $icon = 'bi-truck';
                                                break;
                                            case 'done':
                                                $statusClass = 'badge-done';
                                                $icon = 'bi-check-circle-fill';
                                                break;
                                            case 'rejected':
                                                $statusClass = 'badge-rejected';
                                                $icon = 'bi-x-circle-fill';
                                                break;
                                            default:
                                                $statusClass = 'bg-secondary';
                                                $icon = 'bi-question-circle';
                                        }
                                        ?>
                                        <span class="badge <?= $statusClass ?>">
                                            <i class="bi <?= $icon ?>"></i>
                                            <?= htmlspecialchars($order['order_status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="date-formatted">
                                            <i class="bi bi-calendar3"></i>
                                            <?php 
                                                $date = new DateTime($order['created_at']);
                                                echo $date->format('d M Y, H:i'); 
                                            ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="address-info">
                                            <strong><?= htmlspecialchars($order['full_name']) ?></strong><br>
                                            <?= htmlspecialchars($order['address_line']) ?><br>
                                            <?= htmlspecialchars($order['city']) ?>, <?= htmlspecialchars($order['postal_code']) ?><br>
                                            <?= htmlspecialchars($order['country']) ?><br>
                                            <span class="text-muted">
                                                <i class="bi bi-telephone me-1"></i><?= htmlspecialchars($order['phone_number']) ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <a href="view_order.php?id=<?= $order['order_id'] ?>" class="btn btn-view">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-orders">
                    <i class="bi bi-inbox"></i>
                    <h4>No Orders Found</h4>
                    <p>There are no orders matching your criteria.</p>
                    <?php if ($searchQuery): ?>
                        <a href="manage_orders.php" class="btn btn-outline-primary mt-3">
                            <i class="bi bi-arrow-counterclockwise me-2"></i>Clear Search
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>