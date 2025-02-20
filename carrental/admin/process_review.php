<?php
// Include the database connection
include '../includes/config.php'; // Adjust path as necessary

// Check if an action is provided (approve or delete)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $review_id = (int)$_GET['id']; // Ensure the ID is an integer

    try {
        // Approve review
        if ($action == 'approve') {
            $sql = "UPDATE vehicle_reviews SET status = 'approved' WHERE id = :id";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':id', $review_id);
            $stmt->execute();
            echo "<script>alert('Review approved successfully!'); window.location.href = 'admin_reviews.php';</script>";
        }

        // Delete review
        if ($action == 'delete') {
            $sql = "DELETE FROM vehicle_reviews WHERE id = :id";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':id', $review_id);
            $stmt->execute();
            echo "<script>alert('Review deleted successfully!'); window.location.href = 'admin_reviews.php';</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "'); window.location.href = 'admin_reviews.php';</script>";
    }
} else {
    echo "Invalid access!";
    exit;
}
