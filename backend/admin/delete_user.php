<?php
session_start();
include '../db.php'; // Include the database connection file

// Check if the user ID is provided in the URL
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Prepare the SQL statement to delete the user
    $sql = "DELETE FROM users WHERE user_id = ?";
    $stmt = $pdo->prepare($sql); // Use $pdo instead of $conn (as per your db.php connection)

    // Execute the statement and check if it was successful
    if ($stmt->execute([$user_id])) {
        $_SESSION['success_message'] = "User deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Error deleting user.";
    }
}

// Redirect back to the manage_users.php page
header("Location: manage_users.php");
exit();
?>