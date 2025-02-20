<?php
$host = 'localhost';
$dbname = 'carrental'; // Change to your actual database name
$username = 'root'; // Your database username
$password = ''; // Your database password (if any)

try {
    $dbh = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
