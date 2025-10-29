<?php
// Start session first
session_start();

// Include header and database connection before any output
include '../db.php';
include 'admin_header.php';

// Initialize variables for form values to persist after errors
$product_name = '';
$description = '';
$price = '';
$type = '';
$error_message = '';

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Sanitize inputs
        $product_name = htmlspecialchars(trim($_POST['product_name']));
        $description = htmlspecialchars(trim($_POST['description']));
        $price = filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $type = htmlspecialchars(trim($_POST['type']));
        
        // Validate inputs
        if (empty($product_name) || empty($price) || empty($type)) {
            throw new Exception("Required fields cannot be empty");
        }
        
        // File upload handling with validation
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            // Validate file type
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $file_type = $_FILES['image']['type'];
            
            if (!in_array($file_type, $allowed_types)) {
                throw new Exception("Only JPG, PNG, GIF, and WEBP images are allowed");
            }
            
            // Validate file size (5MB max)
            if ($_FILES['image']['size'] > 5000000) {
                throw new Exception("File is too large. Maximum size is 5MB");
            }
            
            // Generate unique filename to prevent overwriting
            $image = time() . '_' . basename($_FILES['image']['name']);
            $image_target = "../image/" . $image;
            
            // Move file to destination folder
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $image_target)) {
                throw new Exception("Failed to upload image");
            }
        }
        
        // Insert data into database
        $sql = "INSERT INTO product (product_name, description, image, price, type) 
                VALUES (:product_name, :description, :image, :price, :type)";
                
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'product_name' => $product_name,
            'description' => $description,
            'image' => $image,
            'price' => $price,
            'type' => $type
        ]);
        
        // Set success message and redirect
        $_SESSION['success_message'] = "เพิ่มข้อมูลสินค้าสำเร็จ";
        header("Location: manage_products.php");
        exit();
    }
} catch (Exception $e) {
    $error_message = $e->getMessage();
}

// Close database connection
$pdo = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .product-form-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 20px;
            margin-bottom: 40px;
        }
        .form-heading {
            color: #343a40;
            border-left: 5px solid #0d6efd;
            padding-left: 15px;
            margin-bottom: 25px;
        }
        .form-label {
            font-weight: 500;
            color: #495057;
        }
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        .btn-primary {
            background-color: #0d6efd;
            border: none;
            padding: 10px 20px;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
            transform: translateY(-2px);
        }
        .btn-secondary {
            padding: 10px 20px;
            transition: all 0.3s;
        }
        .btn-secondary:hover {
            transform: translateY(-2px);
        }
        .preview-area {
            width: 100%;
            height: 200px;
            border: 2px dashed #ced4da;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            overflow: hidden;
        }
        .preview-area img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }
        .file-input-wrapper input[type=file] {
            font-size: 100px;
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
        }
        .custom-file-button {
            display: inline-block;
            padding: 8px 16px;
            background-color: #e9ecef;
            border: 1px solid #ced4da;
            border-radius: 4px;
            color: #495057;
            cursor: pointer;
            transition: all 0.2s;
        }
        .custom-file-button:hover {
            background-color: #dee2e6;
        }
        .selected-file-name {
            margin-left: 10px;
            font-style: italic;
            color: #6c757d;
        }
        .required-field::after {
            content: "*";
            color: red;
            margin-left: 4px;
        }
        .description-counter {
            float: right;
            font-size: 0.8rem;
            color: #6c757d;
        }
        .card-header-custom {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 15px 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="card product-form-container">
            <div class="card-header-custom">
                <h2 class="form-heading mb-0"><i class="bi bi-plus-circle me-2"></i>Add New Product</h2>
            </div>
            <div class="card-body">
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $error_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data" id="productForm">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Basic Product Information -->
                            <div class="mb-4">
                                <label for="product_name" class="form-label required-field">Product Name</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                    <input type="text" class="form-control" id="product_name" name="product_name" value="<?php echo $product_name; ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="description" class="form-label">Description <span class="description-counter"><span id="charCount">0</span>/500</span></label>
                                <textarea class="form-control" id="description" name="description" rows="5" maxlength="500"><?php echo $description; ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="price" class="form-label required-field">Price</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-currency-dollar"></i></span>
                                            <input type="text" class="form-control" id="price" name="price" value="<?php echo $price; ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="type" class="form-label required-field">Product Type</label>
                                        <select class="form-select" id="type" name="type" required>
                                            <option value="" disabled <?php echo (empty($type)) ? 'selected' : ''; ?>>Select Type</option>
                                            <option value="mouse" <?php echo ($type == 'mouse') ? 'selected' : ''; ?>>Mouse</option>
                                            <option value="keyboard" <?php echo ($type == 'keyboard') ? 'selected' : ''; ?>>Keyboard</option>
                                            <option value="mouse_pad" <?php echo ($type == 'mouse_pad') ? 'selected' : ''; ?>>Mouse Pad</option>
                                            <option value="mic" <?php echo ($type == 'mic') ? 'selected' : ''; ?>>Mic</option>
                                            <option value="monitor" <?php echo ($type == 'monitor') ? 'selected' : ''; ?>>Monitor</option>
                                            <option value="headset" <?php echo ($type == 'headset') ? 'selected' : ''; ?>>Headset</option>
                                            <option value="other" <?php echo ($type == 'other') ? 'selected' : ''; ?>>Other</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <!-- Product Image Upload -->
                            <div class="mb-4">
                                <label for="image" class="form-label">Product Image</label>
                                <div class="preview-area mb-2" id="imagePreview">
                                    <i class="bi bi-image" style="font-size: 3rem; color: #ced4da;"></i>
                                </div>
                                <div class="file-input-wrapper">
                                    <label class="custom-file-button">
                                        <i class="bi bi-upload me-2"></i>Select Image
                                        <input type="file" class="form-control" id="image" name="image" accept=".jpg,.jpeg,.png,.gif,.webp">
                                    </label>
                                    <span class="selected-file-name" id="fileName">No file selected</span>
                                </div>
                                <div class="form-text text-muted mt-2">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Accepted formats: JPG, PNG, GIF, WEBP. Max size: 5MB
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="d-flex justify-content-between">
                        <a href="dashboard.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Add Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Image preview functionality
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('imagePreview');
            const fileName = document.getElementById('fileName');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                };
                reader.readAsDataURL(file);
                fileName.textContent = file.name;
            } else {
                preview.innerHTML = `<i class="bi bi-image" style="font-size: 3rem; color: #ced4da;"></i>`;
                fileName.textContent = 'No file selected';
            }
        });
        
        // Character counter for description
        document.getElementById('description').addEventListener('input', function() {
            document.getElementById('charCount').textContent = this.value.length;
        });
        
        // Initialize character count on page load
        window.addEventListener('DOMContentLoaded', function() {
            document.getElementById('charCount').textContent = document.getElementById('description').value.length;
        });
        
        // Form validation
        document.getElementById('productForm').addEventListener('submit', function(e) {
            let isValid = true;
            const price = document.getElementById('price').value;
            
            // Validate price is a number
            if (isNaN(parseFloat(price)) || !isFinite(price)) {
                alert('Please enter a valid price');
                e.preventDefault();
                isValid = false;
            }
            
            return isValid;
        });
    </script>
</body>
</html>