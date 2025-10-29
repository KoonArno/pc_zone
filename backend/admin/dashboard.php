<?php
// เริ่มต้น session
session_start();
// นำเข้าไฟล์ admin_header.php
include 'admin_header.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-tachometer-alt fa-2x me-3"></i>
                        <div>
                            <h2 class="mb-0">Admin Dashboard</h2>
                            <p class="mb-0 opacity-75">ยินดีต้อนรับสู่ระบบจัดการร้านค้า</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-4">
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <div class="feature-icon bg-primary bg-gradient text-white rounded-circle mb-3">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <h4 class="card-title">Manage Products</h4>
                    <p class="card-text text-muted">จัดการสินค้าในระบบ เพิ่ม/ลบ/แก้ไขข้อมูลสินค้า</p>
                    <a href="manage_products.php" class="btn btn-outline-primary btn-lg mt-2 w-100">
                        <i class="fas fa-arrow-right me-2"></i>จัดการสินค้า
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <div class="feature-icon bg-success bg-gradient text-white rounded-circle mb-3">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h4 class="card-title">Manage Orders</h4>
                    <p class="card-text text-muted">ตรวจสอบและจัดการคำสั่งซื้อจากลูกค้า</p>
                    <a href="manage_orders.php" class="btn btn-outline-success btn-lg mt-2 w-100">
                        <i class="fas fa-arrow-right me-2"></i>จัดการคำสั่งซื้อ
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <div class="feature-icon bg-info bg-gradient text-white rounded-circle mb-3">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h4 class="card-title">Manage Payments</h4>
                    <p class="card-text text-muted">จัดการการชำระเงินและตรวจสอบสถานะการชำระ</p>
                    <a href="manage_payments.php" class="btn btn-outline-info btn-lg mt-2 w-100">
                        <i class="fas fa-arrow-right me-2"></i>จัดการการเงิน
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <div class="feature-icon bg-warning bg-gradient text-white rounded-circle mb-3">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4 class="card-title">Manage Users</h4>
                    <p class="card-text text-muted">จัดการผู้ใช้งานในระบบและกำหนดสิทธิ์การใช้งาน</p>
                    <a href="manage_users.php" class="btn btn-outline-warning btn-lg mt-2 w-100">
                        <i class="fas fa-arrow-right me-2"></i>จัดการผู้ใช้
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS for hover effects -->
<style>
    .hover-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    
    .feature-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }
</style>

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>