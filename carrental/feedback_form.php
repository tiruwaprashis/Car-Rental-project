<?php
require 'admin/includes/config.php';
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to provide feedback.";
    exit;
}

// Fetch the user's booking details (assuming the booking ID is stored in session)
$bookingId = $_SESSION['booking_id'] ?? null;
if (!$bookingId) {
    echo "Booking ID not found.";
    exit;
}

// Fetch the car details (assuming car_id is associated with the booking)
$stmt = $dbh->prepare("SELECT car_id FROM tblbooking WHERE id = :booking_id");
$stmt->execute([':booking_id' => $bookingId]);
$carDetails = $stmt->fetch(PDO::FETCH_ASSOC);
$carId = $carDetails['car_id'] ?? null;

if (!$carId) {
    echo "Car details not found for this booking.";
    exit;
}

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rating = $_POST['rating'] ?? 0;
    $feedback = $_POST['feedback'] ?? '';
    $photo = $_FILES['photo'] ?? null;

    if ($rating < 1 || $rating > 5) {
        echo "Please provide a rating between 1 and 5.";
        exit;
    }

    // Handle the file upload (if photo is uploaded)
    $photoPath = null;
    if ($photo && $photo['error'] === UPLOAD_ERR_OK) {
        // Validate the file (limit file types and size)
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($photo['type'], $allowedTypes)) {
            echo "Please upload a valid image file (JPEG, PNG, GIF).";
            exit;
        }

        if ($photo['size'] > 2 * 1024 * 1024) { // 2MB size limit
            echo "File size should not exceed 2MB.";
            exit;
        }

        // Define the target directory and file path
        $targetDir = 'uploads/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true); // Create the directory if it doesn't exist
        }

        // Generate a unique file name to avoid overwriting
        $fileName = uniqid('feedback_', true) . '.' . pathinfo($photo['name'], PATHINFO_EXTENSION);
        $photoPath = $targetDir . $fileName;

        // Move the uploaded file to the target directory
        if (!move_uploaded_file($photo['tmp_name'], $photoPath)) {
            echo "Error uploading the image.";
            exit;
        }
    }

    try {
        // Insert the feedback into the database
        $stmt = $dbh->prepare("INSERT INTO car_feedback (booking_id, car_id, user_id, rating, feedback, photo) 
                               VALUES (:booking_id, :car_id, :user_id, :rating, :feedback, :photo)");
        $stmt->execute([
            ':booking_id' => $bookingId,
            ':car_id' => $carId,
            ':user_id' => $_SESSION['user_id'],
            ':rating' => $rating,
            ':feedback' => $feedback,
            ':photo' => $photoPath // Save the photo path in the database
        ]);

        echo "Thank you for your feedback!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Feedback</title>
    <style>
        body {
            background: linear-gradient(to right, #021f28, #607cad);
            color: #fff;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #0cee14;
            font-size: 25px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 20px;
        }
        .feedback-form {
            margin-top: 20px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .feedback-form label {
            font-size: 1.1rem;
            color: #555;
            display: block;
            margin-bottom: 10px;
        }
        .feedback-form textarea {
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .star-rating {
            display: flex;
            justify-content: flex-start;
            margin-bottom: 20px;
        }
        .star-rating input {
            display: none;
        }
        .star-rating label {
            font-size: 2rem;
            color: #ccc;
            cursor: pointer;
            transition: color 0.2s ease;
        }
        .star-rating input:checked ~ label {
            color: #f0c420;
        }
        .star-rating input:hover ~ label {
            color: #f0c420;
        }
        .feedback-form button {
            background: linear-gradient(to right, #11998e, #38ef7d);
            color: white;
            padding: 12px 25px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.2s ease;
        }
        .feedback-form button:hover {
            background: linear-gradient(to right, #38ef7d, #11998e);
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Provide Your Feedback</h2>

        <form method="POST" class="feedback-form" enctype="multipart/form-data">
            <label for="rating">Rating:</label>
            <div class="star-rating">
                <input type="radio" id="star5" name="rating" value="5">
                <label for="star5">&#9733;</label>
                <input type="radio" id="star4" name="rating" value="4">
                <label for="star4">&#9733;</label>
                <input type="radio" id="star3" name="rating" value="3">
                <label for="star3">&#9733;</label>
                <input type="radio" id="star2" name="rating" value="2">
                <label for="star2">&#9733;</label>
                <input type="radio" id="star1" name="rating" value="1">
                <label for="star1">&#9733;</label>
            </div>

            <label for="feedback">Feedback:</label>
            <textarea name="feedback" id="feedback" rows="5" placeholder="Share your feedback about the car..." required></textarea>

            <label for="photo">Upload a Photo (Optional):</label>
            <input type="file" name="photo" id="photo" accept="image/*">

            <button type="submit">Submit Feedback</button>
        </form>
    </div>
</body>
</html>
