<?php
include 'includes/config.php';

if (isset($_GET['id'])) {
    $review_id = $_GET['id'];

    try {
        $sql = "UPDATE user_review SET status = 'rejected' WHERE id = :id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $review_id);
        $stmt->execute();

        echo "<script>alert('Review Rejected!'); window.location.href = 'admin_reviews.php';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Error rejecting review: " . $e->getMessage() . "');</script>";
    }
}
?>
