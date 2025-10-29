<?php
ob_start(); // Start output buffering
session_start();
include 'admin_header.php';
include '../db.php';

// Check if the user is an Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header('Location: login.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_id = $_POST['payment_id'];
    $action = $_POST['action'];

    // Get the order_id associated with this payment
    $stmt = $pdo->prepare("SELECT order_id FROM payment WHERE payment_id = ?");
    $stmt->execute([$payment_id]);
    $order_id = $stmt->fetchColumn();

    if ($action == 'confirm') {
        // Begin transaction
        $pdo->beginTransaction();
        
        try {
            // Update payment status
            $stmt = $pdo->prepare("UPDATE payment SET payment_status = 'completed' WHERE payment_id = ?");
            $stmt->execute([$payment_id]);
            
            // Update order status to 'paid'
            $stmt = $pdo->prepare("UPDATE orders SET order_status = 'shipping' WHERE order_id = ?");
            $stmt->execute([$order_id]);
            
            // Commit transaction
            $pdo->commit();
            $_SESSION['success_message'] = "การชำระเงินได้รับการยืนยันเรียบร้อยแล้ว!";
        } catch (Exception $e) {
            // Roll back the transaction if something failed
            $pdo->rollBack();
            $_SESSION['error_message'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
        }
    } elseif ($action == 'reject') {
        // Begin transaction
        $pdo->beginTransaction();
        
        try {
            // Update payment status
            $stmt = $pdo->prepare("UPDATE payment SET payment_status = 'rejected' WHERE payment_id = ?");
            $stmt->execute([$payment_id]);
            
            // Update order status to 'payment_rejected'
            $stmt = $pdo->prepare("UPDATE orders SET order_status = 'rejected' WHERE order_id = ?");
            $stmt->execute([$order_id]);
            
            // Commit transaction
            $pdo->commit();
            $_SESSION['error_message'] = "การชำระเงินถูกปฏิเสธเรียบร้อยแล้ว!";
        } catch (Exception $e) {
            // Roll back the transaction if something failed
            $pdo->rollBack();
            $_SESSION['error_message'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
        }
    }
    header('Location: manage_payments.php');
    exit();
}

// Get filter parameters
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Build SQL query with filters
$sql = "SELECT payment.*, orders.user_id, users.username, orders.total_price 
        FROM payment 
        JOIN orders ON payment.order_id = orders.order_id
        JOIN users ON orders.user_id = users.user_id
        WHERE 1=1";

$params = [];

if ($status_filter != 'all') {
    $sql .= " AND payment.payment_status = ?";
    $params[] = $status_filter;
}

if (!empty($search_query)) {
    $sql .= " AND (users.username LIKE ? OR payment.order_id LIKE ?)";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
}

$sql .= " ORDER BY payment.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$payments = $stmt->fetchAll();

// Count payments by status
$stmt = $pdo->query("SELECT payment_status, COUNT(*) as count FROM payment GROUP BY payment_status");
$status_counts = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Helper function to get badge class based on status
function getStatusBadgeClass($status) {
    switch ($status) {
        case 'pending':
            return 'bg-warning text-dark';
        case 'completed':
            return 'bg-success';
        case 'rejected':
            return 'bg-danger';
        default:
            return 'bg-secondary';
    }
}

// Helper function to get status text in Thai
function getStatusText($status) {
    switch ($status) {
        case 'pending':
            return 'รอดำเนินการ';
        case 'completed':
            return 'เสร็จสมบูรณ์';
        case 'rejected':
            return 'ปฏิเสธแล้ว';
        default:
            return $status;
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการการชำระเงิน | Admin Panel</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Google Fonts - Sarabun for Thai language -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 1400px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            border: none;
        }
        .card-header {
            background-color: #4e73df;
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 15px 20px;
            border: none;
        }
        .stats-card {
            border-radius: 8px;
            transition: transform 0.3s;
            margin-bottom: 20px;
            border-left: 4px solid;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .stats-card.pending {
            border-left-color: #f6c23e;
        }
        .stats-card.completed {
            border-left-color: #1cc88a;
        }
        .stats-card.rejected {
            border-left-color: #e74a3b;
        }
        .stats-card .card-body {
            padding: 15px;
        }
        .stats-card .stats-icon {
            font-size: 2rem;
            opacity: 0.3;
        }
        .stats-card .stats-count {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .stats-card .stats-text {
            color: #5a5c69;
            font-weight: 500;
        }
        .proof-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .proof-image:hover {
            transform: scale(1.1);
        }
        .modal-header {
            background-color: #4e73df;
            color: white;
            border-bottom: none;
        }
        .modal-body {
            padding: 20px;
        }
        .modal-dialog {
            max-width: 800px;
        }
        .modal-content {
            border-radius: 10px;
            border: none;
        }
        .btn-close {
            filter: brightness(0) invert(1);
        }
        .table {
            vertical-align: middle;
        }
        .action-buttons {
            white-space: nowrap;
        }
        .action-buttons .btn {
            padding: 5px 10px;
            font-size: 14px;
            margin-right: 5px;
        }
        .action-buttons .btn i {
            margin-right: 5px;
        }
        .badge {
            font-size: 12px;
            padding: 5px 10px;
            border-radius: 4px;
        }
        .filter-container {
            background-color: #fff;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .alert-dismissible {
            padding-right: 20px;
        }
        .page-header {
            margin-bottom: 25px;
            border-bottom: 1px solid #e3e6f0;
            padding-bottom: 20px;
        }
        .price-column {
            font-weight: 600;
            color: #2e59d9;
        }
        .nav-pills .nav-link {
            border-radius: 30px;
            padding: 8px 20px;
            margin-right: 10px;
            color: #5a5c69;
            font-weight: 500;
        }
        .nav-pills .nav-link.active {
            background-color: #4e73df;
            color: white;
        }
        .nav-pills .nav-link .badge {
            margin-left: 5px;
        }
        .dataTables_filter, .dataTables_length {
            margin-bottom: 15px;
        }
        #paymentTable_filter input {
            border-radius: 30px;
            padding: 8px 15px;
            border: 1px solid #d1d3e2;
        }
        #paymentTable_length select {
            border-radius: 20px;
            padding: 5px 10px;
            border: 1px solid #d1d3e2;
        }
        .dataTables_info, .dataTables_paginate {
            margin-top: 15px;
        }
        .pagination {
            display: flex;
            justify-content: flex-end;
        }
        .pagination .page-item .page-link {
            border-radius: 5px;
            margin: 0 3px;
            color: #4e73df;
        }
        .pagination .page-item.active .page-link {
            background-color: #4e73df;
            border-color: #4e73df;
        }
        .payment-date {
            font-size: 12px;
            color: #6c757d;
        }
        .img-fluid {
            max-height: calc(100vh - 200px);
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0 text-gray-800">จัดการการชำระเงิน</h1>
            <p class="text-muted mb-0">ตรวจสอบและจัดการข้อมูลการชำระเงินทั้งหมด</p>
        </div>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($_SESSION['success_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php elseif (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($_SESSION['error_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <!-- Dashboard Stats -->
    <div class="row">
        <div class="col-md-4">
            <div class="card stats-card pending">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col">
                            <div class="stats-text">รอดำเนินการ</div>
                            <div class="stats-count text-warning"><?= isset($status_counts['pending']) ? $status_counts['pending'] : 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-hourglass-split stats-icon text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stats-card completed">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col">
                            <div class="stats-text">เสร็จสมบูรณ์</div>
                            <div class="stats-count text-success"><?= isset($status_counts['completed']) ? $status_counts['completed'] : 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle stats-icon text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stats-card rejected">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col">
                            <div class="stats-text">ปฏิเสธแล้ว</div>
                            <div class="stats-count text-danger"><?= isset($status_counts['rejected']) ? $status_counts['rejected'] : 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-x-circle stats-icon text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-container">
        <form method="GET" action="" class="row align-items-center">
            <div class="col-md-6">
                <ul class="nav nav-pills mb-md-0 mb-3">
                    <li class="nav-item">
                        <a class="nav-link <?= $status_filter == 'all' ? 'active' : '' ?>" href="?status=all&search=<?= htmlspecialchars($search_query) ?>">
                            ทั้งหมด <span class="badge bg-secondary"><?= array_sum($status_counts) ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $status_filter == 'pending' ? 'active' : '' ?>" href="?status=pending&search=<?= htmlspecialchars($search_query) ?>">
                            รอดำเนินการ <span class="badge bg-warning text-dark"><?= isset($status_counts['pending']) ? $status_counts['pending'] : 0 ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $status_filter == 'completed' ? 'active' : '' ?>" href="?status=completed&search=<?= htmlspecialchars($search_query) ?>">
                            เสร็จสมบูรณ์ <span class="badge bg-success"><?= isset($status_counts['completed']) ? $status_counts['completed'] : 0 ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $status_filter == 'rejected' ? 'active' : '' ?>" href="?status=rejected&search=<?= htmlspecialchars($search_query) ?>">
                            ปฏิเสธแล้ว <span class="badge bg-danger"><?= isset($status_counts['rejected']) ? $status_counts['rejected'] : 0 ?></span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <input type="hidden" name="status" value="<?= htmlspecialchars($status_filter) ?>">
                    <input type="text" name="search" class="form-control" placeholder="ค้นหาด้วยชื่อผู้ใช้หรือเลขที่คำสั่งซื้อ..." value="<?= htmlspecialchars($search_query) ?>" style="border-radius: 30px 0 0 30px">
                    <button type="submit" class="btn btn-primary" style="border-radius: 0 30px 30px 0">
                        <i class="bi bi-search"></i> ค้นหา
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Payments Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>รายการชำระเงิน</h5>
            <div class="text-white">
                พบ <?= count($payments) ?> รายการ
                <?php if (!empty($search_query)): ?>
                    สำหรับการค้นหา "<?= htmlspecialchars($search_query) ?>"
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="paymentTable" class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th width="5%">ID</th>
                            <th width="12%">ผู้ใช้</th>
                            <th width="10%">เลขที่คำสั่งซื้อ</th>
                            <th width="15%">หลักฐานการโอน</th>
                            <th width="10%">จำนวนเงิน</th>
                            <th width="13%">สถานะ</th>
                            <th width="15%">วันที่ชำระเงิน</th>
                            <th width="20%">การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($payments) > 0): ?>
                            <?php foreach ($payments as $payment): ?>
                                <tr>
                                    <td><?= htmlspecialchars($payment['payment_id']) ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-light text-dark rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                <i class="bi bi-person"></i>
                                            </div>
                                            <?= htmlspecialchars($payment['username']) ?>
                                        </div>
                                    </td>
                                    <td><a href="view_order.php?id=<?= $payment['order_id'] ?>" class="text-primary">#<?= htmlspecialchars($payment['order_id']) ?></a></td>
                                    <td>
                                        <img src="../uploads/payments/<?= htmlspecialchars($payment['proof_image']) ?>" 
                                             class="proof-image" 
                                             data-bs-toggle="modal" 
                                             data-bs-target="#imageModal<?= $payment['payment_id'] ?>" 
                                             alt="หลักฐานการโอน">
                                        
                                        <div class="modal fade" id="imageModal<?= $payment['payment_id'] ?>" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="imageModalLabel">
                                                            <i class="bi bi-image me-2"></i>หลักฐานการโอนเงิน - คำสั่งซื้อ #<?= htmlspecialchars($payment['order_id']) ?>
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <img src="../uploads/payments/<?= htmlspecialchars($payment['proof_image']) ?>" 
                                                             class="img-fluid" 
                                                             alt="หลักฐานการโอนเงิน">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <div class="row w-100">
                                                            <div class="col-md-6 text-start">
                                                                <p class="mb-0"><strong>ผู้ใช้:</strong> <?= htmlspecialchars($payment['username']) ?></p>
                                                                <p class="mb-0"><strong>จำนวนเงิน:</strong> <?= number_format($payment['total_price'], 2) ?> บาท</p>
                                                            </div>
                                                            <div class="col-md-6 text-end">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                                                                <?php if ($payment['payment_status'] == 'pending'): ?>
                                                                    <form method="POST" action="" class="d-inline">
                                                                        <input type="hidden" name="payment_id" value="<?= $payment['payment_id'] ?>">
                                                                        <button type="submit" name="action" value="confirm" class="btn btn-success">
                                                                            <i class="bi bi-check-circle me-1"></i>ยืนยัน
                                                                        </button>
                                                                        <button type="submit" name="action" value="reject" class="btn btn-danger">
                                                                            <i class="bi bi-x-circle me-1"></i>ปฏิเสธ
                                                                        </button>
                                                                    </form>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="price-column"><?= number_format($payment['total_price'], 2) ?> บาท</td>
                                    <td>
                                        <span class="badge <?= getStatusBadgeClass($payment['payment_status']) ?>">
                                            <?php if ($payment['payment_status'] == 'pending'): ?>
                                                <i class="bi bi-hourglass-split me-1"></i>
                                            <?php elseif ($payment['payment_status'] == 'completed'): ?>
                                                <i class="bi bi-check-circle me-1"></i>
                                            <?php elseif ($payment['payment_status'] == 'rejected'): ?>
                                                <i class="bi bi-x-circle me-1"></i>
                                            <?php endif; ?>
                                            <?= getStatusText($payment['payment_status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="payment-date">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            <?= date('d/m/Y H:i', strtotime($payment['created_at'])) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#imageModal<?= $payment['payment_id'] ?>">
                                                <i class="bi bi-eye"></i> ดูสลิป
                                            </button>

                                            <?php if ($payment['payment_status'] == 'pending'): ?>
                                                <form method="POST" action="" class="d-inline">
                                                    <input type="hidden" name="payment_id" value="<?= $payment['payment_id'] ?>">
                                                    <button type="submit" name="action" value="confirm" class="btn btn-sm btn-success">
                                                        <i class="bi bi-check-circle"></i> ยืนยัน
                                                    </button>
                                                    <button type="submit" name="action" value="reject" class="btn btn-sm btn-danger">
                                                        <i class="bi bi-x-circle"></i> ปฏิเสธ
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="badge bg-light text-dark border">ดำเนินการแล้ว</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="alert alert-info mb-0">
                                        <i class="bi bi-info-circle-fill me-2"></i> ไม่พบข้อมูลการชำระเงิน
                                        <?php if (!empty($search_query)): ?>
                                            ที่ตรงกับคำค้นหา "<?= htmlspecialchars($search_query) ?>"
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
</div>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<!-- Custom Script -->
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#paymentTable').DataTable({
            language: {
                search: "ค้นหาเพิ่มเติม:",
                lengthMenu: "แสดง _MENU_ รายการ",
                info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                infoEmpty: "แสดง 0 ถึง 0 จากทั้งหมด 0 รายการ",
                infoFiltered: "(กรองจากทั้งหมด _MAX_ รายการ)",
                zeroRecords: "ไม่พบรายการที่ตรงกัน",
                paginate: {
                    first: "หน้าแรก",
                    last: "หน้าสุดท้าย",
                    next: "ถัดไป",
                    previous: "ก่อนหน้า"
                }
            },
            pageLength: 10,
            responsive: true,
            order: [[0, 'desc']]
        });

        // Auto-close alerts after 5 seconds
        setTimeout(function() {
            $('.alert-dismissible').alert('close');
        }, 5000);
        
        // Modal image zoom effect
        $('.modal').on('shown.bs.modal', function() {
            var img = $(this).find('.img-fluid');
            img.css('cursor', 'zoom-in');
            
            img.on('click', function() {
                if (img.hasClass('zoomed')) {
                    img.removeClass('zoomed');
                    img.css({
                        'max-height': 'calc(100vh - 200px)',
                        'transform': 'scale(1)',
                        'cursor': 'zoom-in'
                    });
                } else {
                    img.addClass('zoomed');
                    img.css({
                        'max-height': 'none',
                        'transform': 'scale(1.5)',
                        'cursor': 'zoom-out'
                    });
                }
            });
        });
    });
</script>
</body>
</html>
<?php
ob_end_flush(); // End output buffering and send the output to the browser
?>