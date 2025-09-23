<?php
// Database credentials
$host = "localhost";            // Change if not local (e.g., hosting server IP)
$dbname = "smart_saving_track"; // Your database name
$user = "root";                 // Your MySQL username
$pass = "";                     // Your MySQL password
$charset = "utf8mb4";

// Data Source Name
$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // throw exceptions
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // fetch assoc arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // use real prepared statements
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    // Uncomment below line for debugging
    // echo "✅ Database connected successfully!";
} catch (PDOException $e) {
    die("❌ Database connection failed: " . $e->getMessage());
}
?>
