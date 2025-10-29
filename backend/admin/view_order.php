<?php
ob_start(); // Add output buffering at the start
session_start();
include 'admin_header.php';
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: manage_order.php');
    exit();
}

$order_id = $_GET['id'];

$sql = "SELECT o.order_id, o.user_id, o.total_price, o.order_status, o.created_at, u.username, a.full_name, a.phone_number, a.address_line,a.district,a.subdistrict, a.city, a.postal_code, a.country
        FROM orders o
        JOIN users u ON o.user_id = u.user_id
        JOIN address a ON o.address_id = a.address_id
        WHERE o.order_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: manage_order.php');
    exit();
}

$sql = "SELECT oi.order_item_id, oi.product_id, oi.quantity, p.product_name, p.price, p.image as product_image
        FROM orders_item oi
        JOIN product p ON oi.product_id = p.product_id
        WHERE oi.order_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll();

// Get payment information if available
$sql = "SELECT payment_id, payment_status, created_at, proof_image 
        FROM payment 
        WHERE order_id = ? 
        ORDER BY created_at DESC 
        LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([$order_id]);
$payment = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['order_status'];
    $sql = "UPDATE orders SET order_status = ? WHERE order_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$new_status, $order_id]);

    $_SESSION['success_message'] = "Order status updated successfully!";
    header("Location: view_order.php?id=$order_id");
    exit();
}

// Helper function to get status badge class
function getStatusBadgeClass($status) {
    switch ($status) {
        case 'processing':
            return 'bg-warning text-dark';
        case 'shipping':
            return 'bg-info text-dark';
        case 'shipped':
            return 'bg-primary';
        case 'done':
            return 'bg-success';
        default:
            return 'bg-secondary';
    }
}

// Helper function to get payment status badge class
function getPaymentStatusBadgeClass($status) {
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

// Format date to be more readable
function formatDate($dateString) {
    $date = new DateTime($dateString);
    return $date->format('d M Y, H:i');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?= htmlspecialchars($order['order_id']) ?> - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;700&display=swap" rel="stylesheet">
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
            border: none;
        }
        .card-header {
            background-color: #4e73df;
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 15px 20px;
            border: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .card-header .badge {
            font-size: 14px;
            padding: 8px 12px;
        }
        .card-body {
            padding: 20px;
        }
        .order-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        @media (max-width: 768px) {
            .order-info-grid {
                grid-template-columns: 1fr;
            }
        }
        .info-item {
            margin-bottom: 15px;
        }
        .info-item strong {
            color: #4e73df;
            min-width: 150px;
            display: inline-block;
        }
        .info-value {
            color: #5a5c69;
            font-weight: 500;
        }
        .table {
            vertical-align: middle;
        }
        .table thead {
            background-color: #f2f4f8;
        }
        .table th {
            color: #4e73df;
            font-weight: 600;
        }
        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            margin-right: 10px;
        }
        .product-info {
            display: flex;
            align-items: center;
        }
        .order-summary {
            background-color: #f8f9fc;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e3e6f0;
        }
        .summary-item:last-child {
            border-bottom: none;
            font-weight: bold;
            color: #4e73df;
        }
        .status-tracker {
            display: flex;
            justify-content: space-between;
            margin: 30px 0;
            position: relative;
        }
        .status-tracker:before {
            content: '';
            position: absolute;
            top: 20px;
            left: 40px;
            right: 40px;
            height: 4px;
            background-color: #e3e6f0;
            z-index: 1;
        }
        .status-step {
            position: relative;
            z-index: 2;
            text-align: center;
            width: 60px;
        }
        .status-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e3e6f0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
        }
        .status-icon.active {
            background-color: #4e73df;
            color: white;
        }
        .status-text {
            font-size: 12px;
            color: #858796;
        }
        .status-text.active {
            font-weight: bold;
            color: #4e73df;
        }
        .update-status-form {
            background-color: #f8f9fc;
            padding: 20px;
            border-radius: 8px;
        }
        .proof-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 6px;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .proof-image:hover {
            transform: scale(1.1);
        }
        .modal-dialog {
            max-width: 800px;
        }
        .modal-content {
            border-radius: 10px;
            border: none;
        }
        .modal-header {
            background-color: #4e73df;
            color: white;
            border-radius: 10px 10px 0 0;
            border-bottom: none;
        }
        .btn-close {
            filter: brightness(0) invert(1);
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e3e6f0;
        }
        .alert {
            border-radius: 8px;
            margin-bottom: 25px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        .breadcrumb {
            margin-bottom: 0;
        }
        .breadcrumb-item a {
            color: #4e73df;
            text-decoration: none;
        }
        .breadcrumb-item.active {
            color: #5a5c69;
        }
        .btn-primary {
            background-color: #4e73df;
            border-color: #4e73df;
        }
        .btn-primary:hover {
            background-color: #2e59d9;
            border-color: #2e59d9;
        }
        .btn-secondary {
            background-color: #858796;
            border-color: #858796;
        }
        .btn-secondary:hover {
            background-color: #717384;
            border-color: #717384;
        }
        .avatar {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #4e73df;
            color: white;
            border-radius: 50%;
            margin-right: 10px;
            font-weight: bold;
        }
        .user-info {
            display: flex;
            align-items: center;
            color: #000;
        }
        .img-fluid {
            max-height: calc(100vh - 200px);
        }
    </style>
</head>
<body>
<div class="container mt-4 mb-5">
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($_SESSION['success_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <div class="page-header">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php"><i class="bi bi-house-door"></i> Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="manage_orders.php">Orders</a></li>
                    <li class="breadcrumb-item active">Order #<?= htmlspecialchars($order['order_id']) ?></li>
                </ol>
            </nav>
            <h1 class="h3 mt-2 text-gray-800">Order #<?= htmlspecialchars($order['order_id']) ?></h1>
        </div>
        <a href="manage_orders.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Orders
        </a>
    </div>

    <!-- Status Tracker -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-truck me-2"></i>Order Status</h5>
            <span class="badge <?= getStatusBadgeClass($order['order_status']) ?>">
                <?= ucfirst(htmlspecialchars($order['order_status'])) ?>
            </span>
        </div>
        <div class="card-body">
            <div class="status-tracker">
                <div class="status-step">
                    <div class="status-icon <?= in_array($order['order_status'], ['processing', 'shipping', 'shipped', 'done']) ? 'active' : '' ?>">
                        <i class="bi bi-box"></i>
                    </div>
                    <div class="status-text <?= in_array($order['order_status'], ['processing', 'shipping', 'shipped', 'done']) ? 'active' : '' ?>">Processing</div>
                </div>
                
                <div class="status-step">
                    <div class="status-icon <?= in_array($order['order_status'], ['shipping', 'shipped', 'done']) ? 'active' : '' ?>">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div class="status-text <?= in_array($order['order_status'], ['shipping', 'shipped', 'done']) ? 'active' : '' ?>">Shipping</div>
                </div>
                
                <div class="status-step">
                    <div class="status-icon <?= in_array($order['order_status'], ['shipped', 'done']) ? 'active' : '' ?>">
                        <i class="bi bi-truck"></i>
                    </div>
                    <div class="status-text <?= in_array($order['order_status'], ['shipped', 'done']) ? 'active' : '' ?>">Shipped</div>
                </div>
                
                <div class="status-step">
                    <div class="status-icon <?= $order['order_status'] == 'done' ? 'active' : '' ?>">
                        <i class="bi bi-check-lg"></i>
                    </div>
                    <div class="status-text <?= $order['order_status'] == 'done' ? 'active' : '' ?>">Delivered</div>
                </div>
            </div>

            <!-- Update Order Status -->
            <div class="update-status-form">
                <form method="POST" action="">
                    <div class="row align-items-end">
                        <div class="col-md-8">
                            <label for="order_status" class="form-label"><i class="bi bi-pencil-square me-1"></i>Update Order Status</label>
                            <select class="form-select" id="order_status" name="order_status" required>
                                <option value="processing" <?= $order['order_status'] == 'processing' ? 'selected' : '' ?>>Processing</option>
                                <option value="shipping" <?= $order['order_status'] == 'shipping' ? 'selected' : '' ?>>Shipping</option>
                                <option value="shipped" <?= $order['order_status'] == 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                <option value="done" <?= $order['order_status'] == 'done' ? 'selected' : '' ?>>Done</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" name="update_status" class="btn btn-primary w-100">
                                <i class="bi bi-save me-1"></i> Update Status
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Order Items -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-cart3 me-2"></i>Order Items</h5>
                    <span class="badge bg-primary"><?= count($order_items) ?> Items</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-center">Price</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order_items as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="product-info">
                                                <img src="../image/<?= !empty($item['product_image']) ? htmlspecialchars($item['product_image']) : 'default.jpg' ?>" 
                                                    class="product-img" alt="<?= htmlspecialchars($item['product_name']) ?>">
                                                <div>
                                                    <div class="fw-bold"><?= htmlspecialchars($item['product_name']) ?></div>
                                                    <small class="text-muted">ID: <?= htmlspecialchars($item['product_id']) ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center"><?= number_format($item['price'], 2) ?> ฿</td>
                                        <td class="text-center"><?= htmlspecialchars($item['quantity']) ?></td>
                                        <td class="text-end fw-bold"><?= number_format($item['price'] * $item['quantity'], 2) ?> ฿</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Order Summary -->
                    <div class="order-summary">
                        <div class="summary-item">
                            <span>Subtotal</span>
                            <span><?= number_format($order['total_price'], 2) ?> ฿</span>
                        </div>
                        <div class="summary-item">
                            <span>Shipping</span>
                            <span>0.00 ฿</span>
                        </div>
                        <div class="summary-item">
                            <span>Total</span>
                            <span><?= number_format($order['total_price'], 2) ?> ฿</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Information (if available) -->
            <?php if ($payment): ?>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>Payment Information</h5>
                    <span class="badge <?= getPaymentStatusBadgeClass($payment['payment_status']) ?>">
                        <?= ucfirst(htmlspecialchars($payment['payment_status'])) ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="info-item">
                                <strong>Payment ID:</strong>
                                <span class="info-value"><?= htmlspecialchars($payment['payment_id']) ?></span>
                            </div>
                            <div class="info-item">
                                <strong>Date:</strong>
                                <span class="info-value"><?= formatDate($payment['created_at']) ?></span>
                            </div>
                            <div class="info-item">
                                <strong>Amount:</strong>
                                <span class="info-value"><?= number_format($order['total_price'], 2) ?> ฿</span>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <?php if (!empty($payment['proof_image'])): ?>
                                <p><strong>Payment Proof</strong></p>
                                <img src="../uploads/payments/<?= htmlspecialchars($payment['proof_image']) ?>" 
                                     class="proof-image" 
                                     data-bs-toggle="modal" 
                                     data-bs-target="#paymentProofModal" 
                                     alt="Payment Proof">
                                
                                <!-- Payment Proof Modal -->
                                <div class="modal fade" id="paymentProofModal" tabindex="-1" aria-labelledby="paymentProofModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="paymentProofModalLabel">
                                                    <i class="bi bi-image me-2"></i>Payment Proof - Order #<?= htmlspecialchars($order['order_id']) ?>
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <img src="../uploads/payments/<?= htmlspecialchars($payment['proof_image']) ?>" 
                                                     class="img-fluid" 
                                                     alt="Payment Proof">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">No payment proof available</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <!-- Customer Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-person me-2"></i>Customer Information</h5>
                </div>
                <div class="card-body">
                    <div class="user-info mb-3">
                        <div class="avatar">
                            <?= strtoupper(substr($order['username'], 0, 1)) ?>
                        </div>
                        <div>
                            <div class="fw-bold"><?= htmlspecialchars($order['username']) ?></div>
                            <small class="text-muted">User ID: <?= htmlspecialchars($order['user_id']) ?></small>
                        </div>
                    </div>
                    <hr>
                    <h6 class="text-primary mb-3">Order Information</h6>
                    <div class="info-item">
                        <strong>Created:</strong>
                        <span class="info-value"><?= formatDate($order['created_at']) ?></span>
                    </div>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Shipping Address</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong class="d-block text-primary mb-2">Contact</strong>
                        <p class="mb-1"><?= htmlspecialchars($order['full_name']) ?></p>
                        <p class="mb-1"><?= htmlspecialchars($order['phone_number']) ?></p>
                    </div>
                    <hr>
                    <div>
                        <strong class="d-block text-primary mb-2">Address</strong>
                        <p class="mb-1"><?= htmlspecialchars($order['address_line']) ?> <?= htmlspecialchars($order['district']) ?> <?= htmlspecialchars($order['subdistrict']) ?></p>
                        <p class="mb-1"><?= htmlspecialchars($order['city']) ?>, <?= htmlspecialchars($order['postal_code']) ?></p>
                        <p class="mb-1"><?= htmlspecialchars($order['country']) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Auto-close alerts after 5 seconds
    setTimeout(function() {
        document.querySelectorAll('.alert-dismissible').forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
    
    // Modal image zoom effect
    document.addEventListener('DOMContentLoaded', function() {
        var modal = document.getElementById('paymentProofModal');
        if (modal) {
            modal.addEventListener('shown.bs.modal', function() {
                var img = modal.querySelector('.img-fluid');
                img.style.cursor = 'zoom-in';
                
                img.addEventListener('click', function() {
                    if (img.classList.contains('zoomed')) {
                        img.classList.remove('zoomed');
                        img.style.maxHeight = 'calc(100vh - 200px)';
                        img.style.transform = 'scale(1)';
                        img.style.cursor = 'zoom-in';
                    } else {
                        img.classList.add('zoomed');
                        img.style.maxHeight = 'none';
                        img.style.transform = 'scale(1.5)';
                        img.style.cursor = 'zoom-out';
                    }
                });
            });
        }
    });
</script>
</body>
</html>
<?php ob_end_flush(); // End and flush the output buffer ?>