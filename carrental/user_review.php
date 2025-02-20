<?php
// Include database connection
include 'includes/config.php';

// Get the vehicle name from the URL
$vehicle_name = isset($_GET['vehicle_name']) ? htmlspecialchars($_GET['vehicle_name']) : '';

// Check if the delete_photo parameter is set
if (isset($_GET['delete_photo']) && $_GET['delete_photo'] == 1 && isset($_GET['review_id'])) {
    $review_id = intval($_GET['review_id']);

    try {
        // Fetch the photo path
        $sql = "SELECT photo_path FROM user_review WHERE id = :review_id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':review_id', $review_id);
        $stmt->execute();
        $review = $stmt->fetch();

        // Delete the photo file from the server
        if ($review && !empty($review['photo_path']) && file_exists($review['photo_path'])) {
            unlink($review['photo_path']);
        }

        // Update the database to remove the photo path
        $sql = "UPDATE user_review SET photo_path = NULL WHERE id = :review_id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':review_id', $review_id);
        $stmt->execute();

        echo "<script>alert('Photo deleted successfully.'); window.location.href = 'user_review.php?vehicle_name=" . urlencode($vehicle_name) . "';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Error deleting photo: " . $e->getMessage() . "');</script>";
    }
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vehicle_name = $_POST['vehicle_name'];
    $rating = $_POST['rating'];
    $feedback = $_POST['feedback'];
    $photo_path = '';

    // Check if a file was uploaded
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        // Define the directory where the photo will be uploaded
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_name = basename($_FILES['photo']['name']);
        $file_path = $upload_dir . time() . '_' . $file_name;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $file_path)) {
            $photo_path = $file_path;
        } else {
            echo "<script>alert('Error uploading photo.');</script>";
        }
    }

    // Insert the review into the user_review table
    try {
        $sql = "INSERT INTO user_review (vehicle_name, rating, feedback, status, photo_path) 
                VALUES (:vehicle_name, :rating, :feedback, 'pending', :photo_path)";
        $stmt = $dbh->prepare($sql);

        $stmt->bindParam(':vehicle_name', $vehicle_name);
        $stmt->bindParam(':rating', $rating);
        $stmt->bindParam(':feedback', $feedback);
        $stmt->bindParam(':photo_path', $photo_path);

        $stmt->execute();

        echo "<script>alert('Thank you for your feedback! Your review is under approval.'); window.location.href = 'user_review.php?vehicle_name=" . urlencode($vehicle_name) . "';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Error submitting feedback: " . $e->getMessage() . "');</script>";
    }
}

// Fetch existing reviews for this vehicle
try {
    $sql_reviews = "SELECT * FROM user_review WHERE vehicle_name = :vehicle_name AND status = 'approved'";
    $stmt_reviews = $dbh->prepare($sql_reviews);
    $stmt_reviews->bindParam(':vehicle_name', $vehicle_name);
    $stmt_reviews->execute();
    $reviews = $stmt_reviews->fetchAll();
    $total_reviews = count($reviews); // Get the total number of approved reviews
} catch (PDOException $e) {
    echo "Error fetching reviews: " . $e->getMessage();
}

// Fetch star ratings count
try {
    $sql_star_count = "SELECT rating, COUNT(*) as count FROM user_review WHERE vehicle_name = :vehicle_name AND status = 'approved' GROUP BY rating";
    $stmt_star_count = $dbh->prepare($sql_star_count);
    $stmt_star_count->bindParam(':vehicle_name', $vehicle_name);
    $stmt_star_count->execute();
    $star_counts = $stmt_star_count->fetchAll(PDO::FETCH_KEY_PAIR); // Returns an associative array of [rating => count]

    // Ensure all ratings (1 to 5) are included, even if there are no reviews
    $star_data = [];
    for ($i = 1; $i <= 5; $i++) {
        $star_data[$i] = isset($star_counts[$i]) ? $star_counts[$i] : 0;
    }
} catch (PDOException $e) {
    echo "Error fetching star ratings: " . $e->getMessage();
}

// Function to display stars based on rating
function displayStars($rating) {
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rating) {
            $stars .= '<span class="fa fa-star checked"></span>';
        } else {
            $stars .= '<span class="fa fa-star"></span>';
        }
    }
    return $stars;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Review</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container mt-5">
    <h2>Submit Your Review for <?php echo $vehicle_name; ?></h2>
    <!-- Display Total Review Count -->
    <p><strong>Total Reviews:</strong> <?php echo $total_reviews; ?></p>
    <!-- View Ratings Distribution Button -->
    <button id="viewRatingsBtn" class="btn btn-info mb-4">View Ratings Distribution</button>

    <!-- Ratings Distribution Chart -->
    <div id="ratingsDistribution" style="display: none;">
        <h3>Ratings Distribution</h3>
        <canvas id="ratingsChart" width="400" height="200"></canvas>
    </div>

    <!-- Review Form -->
    <form method="POST" action="user_review.php" enctype="multipart/form-data">
        <input type="hidden" name="vehicle_name" value="<?php echo $vehicle_name; ?>">

        <!-- Star Rating Section -->
        <div class="form-group">
            <label for="rating">Rating:</label>
            <div class="star-rating" id="star-rating">
                <?php for ($i = 1; $i <= 5; $i++) : ?>
                    <span class="fa fa-star" data-value="<?php echo $i; ?>" onclick="selectRating(<?php echo $i; ?>)"></span>
                <?php endfor; ?>
            </div>
            <input type="hidden" name="rating" id="selected-rating" value="0">
            <p id="rating-text">Rating: 0</p>
        </div>

        <!-- Feedback Section -->
        <div class="form-group">
            <label for="feedback">Feedback:</label>
            <textarea class="form-control" id="feedback" name="feedback" rows="4" required></textarea>
        </div>

        <!-- Photo Upload Section -->
        <div class="form-group">
            <label for="photo">Upload a Photo (optional):</label>
            <input type="file" id="photo" name="photo" class="form-control" accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary">Submit Review</button>
    </form>

    <hr>

    <h3>User Reviews:</h3>
    <?php if (count($reviews) > 0) : ?>
        <div class="list-group">
            <?php foreach ($reviews as $review) : ?>
                <div class="list-group-item">
                    <strong>Rating:</strong>
                    <div class="star-rating">
                        <?php echo displayStars($review['rating']); ?>
                    </div>
                    <strong>Feedback:</strong>
                    <p><?php echo nl2br(htmlspecialchars($review['feedback'])); ?></p>
                    <?php if (!empty($review['photo_path'])) : ?>
                        <div class="mt-3">
                            <strong>Photo:</strong>
                            <img src="<?php echo $review['photo_path']; ?>" alt="Review Photo" class="img-thumbnail" width="150">
                            <a href="?vehicle_name=<?php echo urlencode($vehicle_name); ?>&delete_photo=1&review_id=<?php echo $review['id']; ?>" class="btn btn-danger btn-sm">Delete Photo</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <p>No reviews yet for this vehicle.</p>
    <?php endif; ?>
</div>

<script>
    // Toggle Ratings Distribution Display
    const viewRatingsBtn = document.getElementById('viewRatingsBtn');
    const ratingsDistribution = document.getElementById('ratingsDistribution');
    viewRatingsBtn.addEventListener('click', () => {
        ratingsDistribution.style.display = ratingsDistribution.style.display === 'none' ? 'block' : 'none';
    });

    // Ratings Chart Data
    const ctx = document.getElementById('ratingsChart').getContext('2d');
    const starData = <?php echo json_encode(array_values($star_data)); ?>;
    const labels = ['1 Star', '2 Stars', '3 Stars', '4 Stars', '5 Stars'];
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Number of Reviews',
                data: starData,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    function selectRating(value) {
        const stars = document.querySelectorAll('.star-rating .fa-star');
        document.getElementById('selected-rating').value = value;
        document.getElementById('rating-text').textContent = `Rating: ${value}`;
        stars.forEach((star, index) => {
            if (index < value) {
                star.classList.add('checked');
            } else {
                star.classList.remove('checked');
            }
        });
    }
</script>

<style>
    .star-rating .fa-star {
        color: gray;
        cursor: pointer;
    }
    .star-rating .fa-star.checked {
        color: gold;
    }
</style>
</body>
</html>
