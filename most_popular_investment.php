<?php
$servername = "localhost";
$username = "root";
$password = "Lucky@24";
$dbname = "investment";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to fetch the most popular investment
$sql = "
    SELECT 
        asset_type, 
        COUNT(*) AS total_count
    FROM 
        investments
    GROUP BY 
        asset_type
    ORDER BY 
        total_count DESC
    LIMIT 1";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Most Popular Investment</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Most Popular Investment</h1>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='investment-info'>";
                echo "<p><strong>Asset Type:</strong> " . htmlspecialchars($row["asset_type"]) . "</p>";
                echo "<p><strong>Total Count:</strong> " . $row["total_count"] . "</p>";
                echo "</div>";
            }
        } else {
            echo "<p>No data found.</p>";
        }
        $conn->close();
        ?>
        <a href="/" class="back-link">Back to Home</a> <!-- Update the link as needed -->
    </div>
</body>
</html>
