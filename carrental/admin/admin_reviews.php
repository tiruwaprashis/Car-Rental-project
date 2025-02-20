<?php
include 'includes/config.php';

// Automatically approve reviews
try {
    $sql = "UPDATE user_review SET status = 'approved' WHERE status = 'pending'";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
} catch (PDOException $e) {
    echo "Error automatically approving reviews: " . $e->getMessage();
}

// Fetch the highest rated car review
try {
    $sql = "SELECT * FROM user_review WHERE status = 'approved' ORDER BY rating DESC LIMIT 1";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $highestRatedReview = $stmt->fetch();
} catch (PDOException $e) {
    echo "Error fetching highest rated review: " . $e->getMessage();
}

// Fetch all reviews ordered by rating (highest first)
try {
    $sql = "SELECT * FROM user_review ORDER BY rating DESC, date_submitted DESC";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $reviews = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error fetching reviews: " . $e->getMessage();
}

// Display stars for rating
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
    <title>Admin Review Management</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background: #1e272e;
            font-family: Arial, sans-serif;
            color: #fff; /* Set text color to white */
        }

        .container {
            background: #2f3640;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            margin-top: 30px;
        }

        h2 {
            text-align: center;
            color: #4cd137;
        }

        .fa-star {
            font-size: 20px;
            color: #57606f;
        }

        .fa-star.checked {
            color: #4cd137;
        }

        .btn-success {
            background: #4cd137;
            border: none;
        }

        .btn-danger {
            background: #e84118;
            border: none;
        }

        table {
            background: #2f3640;
            color: #fff; /* Ensure table text is white */
        }

        th {
            background: #44bd32;
            color: #fff;
        }

        td {
            color: #fff; /* Ensure table data text is white */
        }

        .highest-rated {
            background: #44bd32;
            padding: 15px;
            color: #fff;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Admin Review Management</h2>

        <?php if ($highestRatedReview): ?>
            <div class="highest-rated">
                <h4>Highest Rated Car: <?php echo htmlspecialchars($highestRatedReview['vehicle_name']); ?></h4>
                <p>Rating: <?php echo displayStars($highestRatedReview['rating']); ?></p>
                <!-- <p>Feedback: <?php echo nl2br(htmlspecialchars($highestRatedReview['feedback'])); ?></p> -->
            </div>
        <?php endif; ?>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Vehicle Name</th>
                    <th>Rating</th>
                    <th>Feedback</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reviews as $review) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($review['vehicle_name']); ?></td>
                        <td><?php echo displayStars($review['rating']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($review['feedback'])); ?></td>
                        <td><?php echo ucfirst(htmlspecialchars($review['status'])); ?></td>
                        <td>
                            <?php if ($review['status'] === 'pending') : ?>
                                <a href="?approve_id=<?php echo $review['id']; ?>" class="btn btn-success btn-sm">Approve</a>
                                <a href="?reject_id=<?php echo $review['id']; ?>" class="btn btn-danger btn-sm">Reject</a>
                            <?php else : ?>
                                <span class="badge badge-<?php echo $review['status'] === 'approved' ? 'success' : 'danger'; ?>">
                                    <?php echo ucfirst($review['status']); ?>
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
