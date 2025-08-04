<?php
include 'db_connect.php';
$filename = 'db.sql';

$sql = file_get_contents($filename);
if ($sql === false) {
    die("Failed to read SQL file.");
}

// Split SQL statements by semicolon
$queries = array_filter(array_map('trim', explode(';', $sql)));

foreach ($queries as $query) {
    if (!empty($query)) {
        if (!$conn->query($query)) {
            echo "Error in query: " . $conn->error . "<br>";
        }
    }
}

echo "SQL file executed successfully.";
?>
