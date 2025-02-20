<?php
include 'includes/config.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) { // Sanitize and validate the ID
    $review_id = $_GET['id'];

    try {
        // Update the review status to 'approved'
        $sql = "UPDATE user_review SET status = 'approved' WHERE id = :id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $review_id, PDO::PARAM_INT); // Ensure correct parameter type
        $stmt->execute();

        // Redirect back to the admin reviews page
        echo "<script>alert('Review Approved!'); window.location.href = 'admin_reviews.php';</script>";
    } catch (PDOException $e) {
        // Log the error for debugging (optional)
        error_log("Error approving review: " . $e->getMessage());
        echo "<script>alert('Error approving review: Please try again later.');</script>";
    }
} else {
    echo "<script>alert('Invalid review ID.'); window.location.href = 'admin_reviews.php';</script>";
}
?>
