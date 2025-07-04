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

// SQL query to find the top investor based on total investment
$sql = "
    SELECT 
        u.name, 
        u.email, 
        SUM(i.amount) AS total_invested
    FROM 
        users u
    JOIN 
        investments i ON u.investor_id = i.investor_id
    GROUP BY 
        u.investor_id
    ORDER BY 
        total_invested DESC
    LIMIT 1";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top Investor</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Top Investor</h1>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='investor-info'>";
                echo "<p><strong>Name:</strong> " . htmlspecialchars($row["name"]) . "</p>";
                echo "<p><strong>Email:</strong> " . htmlspecialchars($row["email"]) . "</p>";
                echo "<p><strong>Total Invested:</strong> $" . number_format($row["total_invested"], 2) . "</p>";
                echo "</div>";
            }
        } else {
            echo "<p>No data found.</p>";
        }
        $conn->close();
        ?>
        <a href="/" class="back-link">Back to Home</a> <!-- Update this link as needed -->
    </div>
</body>
</html>
