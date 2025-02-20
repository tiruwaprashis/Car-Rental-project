<?php
// Include the database connection file (adjust the path accordingly)
include 'includes/config.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data from the POST request
    $vehicle_name = $_POST['vehicle_name'];
    $rating = $_POST['rating'];
    $feedback = $_POST['feedback'];

    // Sanitize input to prevent SQL injection
    $vehicle_name = htmlspecialchars($vehicle_name);
    $rating = (int)$rating; // Ensure rating is an integer
    $feedback = htmlspecialchars($feedback);

    // Check if required fields are filled
    if (empty($vehicle_name) || empty($rating) || empty($feedback)) {
        echo "<script>alert('Please fill all fields.'); window.location.href = 'index.php';</script>";
        exit;
    }

    try {
        // Prepare SQL query to insert the review into the database
        $sql = "INSERT INTO vehicle_reviews (vehicle_name, rating, feedback) VALUES (:vehicle_name, :rating, :feedback)";
        $stmt = $dbh->prepare($sql);

        // Bind the parameters to the prepared statement
        $stmt->bindParam(':vehicle_name', $vehicle_name);
        $stmt->bindParam(':rating', $rating);
        $stmt->bindParam(':feedback', $feedback);

        // Execute the query to insert the data
        if ($stmt->execute()) {
            // Show a success message and redirect to the main page (adjust the redirect URL as needed)
            echo "<script>alert('Thank you for your feedback!'); window.location.href = 'index.php';</script>";
        } else {
            // If insert fails, show an error message
            echo "<script>alert('Failed to submit your feedback. Please try again later.'); window.location.href = 'index.php';</script>";
        }

    } catch (PDOException $e) {
        // Catch any exceptions and display the error message
        echo "<script>alert('Error submitting feedback: " . $e->getMessage() . "'); window.location.href = 'index.php';</script>";
    }
} else {
    // Handle invalid access (if accessed without POST)
    echo "Invalid access!";
    exit;
}
?>
