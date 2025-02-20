<?php
require 'admin/includes/config.php';  // Ensure the path is correct

// Start the session to access the stored transaction_id
session_start();

// Retrieve the stored transaction ID from session
$transactionId = $_SESSION['transaction_id'] ?? null;
if (!$transactionId) {
    echo "Error: Transaction ID not found.";
    exit;
}

// Handle the form submission for review and rating
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = $_POST['rating'] ?? null;
    $review = $_POST['review'] ?? null;

    // Validate the input
    if (!$rating || !$review) {
        echo "Error: Rating and Review are required.";
        exit;
    }

    // Insert the review and rating into the database
    try {
        $stmt = $dbh->prepare("INSERT INTO tblreviews (BookingNumber, rating, review) VALUES (:transaction_id, :rating, :review)");
        $stmt->execute([
            ':transaction_id' => $transactionId,
            ':rating' => $rating,
            ':review' => $review
        ]);
        echo "Thank you for your review!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
}

// Query to retrieve booking details from tblbooking
try {
    $stmt = $dbh->prepare("SELECT * FROM tblbooking WHERE BookingNumber = :transaction_id");
    $stmt->execute([':transaction_id' => $transactionId]);
    $bookingDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$bookingDetails) {
        echo "Error: No booking found for this transaction ID.";
        exit;
    }

    // Example of displaying booking information
    echo "<h2>Booking Details</h2>";
    echo "Booking Number: " . htmlspecialchars($bookingDetails['BookingNumber']) . "<br>";
    echo "User Email: " . htmlspecialchars($bookingDetails['userEmail']) . "<br>";
    echo "From Date: " . htmlspecialchars($bookingDetails['FromDate']) . "<br>";
    echo "To Date: " . htmlspecialchars($bookingDetails['ToDate']) . "<br>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Your Car</title>
    <style>
        /* Similar styling to the previous page */
    </style>
</head>
<body>
    <div class="container">
        <h2>Leave a Review for the Car</h2>
        
        <form method="POST">
            <label for="rating">Rating (1-5 stars):</label>
            <select name="rating" id="rating" required>
                <option value="1">1 Star</option>
                <option value="2">2 Stars</option>
                <option value="3">3 Stars</option>
                <option value="4">4 Stars</option>
                <option value="5">5 Stars</option>
            </select>
            
            <label for="review">Your Review:</label>
            <textarea name="review" id="review" rows="4" required></textarea>

            <button type="submit" class="submit-button">Submit Review</button>
        </form>
    </div>
</body>
</html>
