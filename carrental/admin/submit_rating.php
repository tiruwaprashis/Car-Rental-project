<?php
// Assuming you have the vehicle_id and rating data from the POST request
if (isset($_POST['vehicle_id']) && isset($_POST['rating'])) {
    $vehicleId = $_POST['vehicle_id'];
    $rating = $_POST['rating'];

    // Assuming your table is named "vehicle_ratings"
    // You can either insert or update the rating in the database
    $stmt = $dbh->prepare("INSERT INTO vehicle_ratings (vehicle_id, rating) VALUES (?, ?) ON DUPLICATE KEY UPDATE rating = ?");
    $stmt->execute([$vehicleId, $rating, $rating]);

    echo 'Rating saved successfully';
} else {
    echo 'Error: Missing data';
}
?>
