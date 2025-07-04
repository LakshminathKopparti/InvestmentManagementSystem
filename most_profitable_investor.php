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

// SQL query to find the most profitable investor
$sql = "
    SELECT 
        u.name, 
        u.email, 
        SUM(p.net_gain_or_loss) AS total_profit
    FROM 
        users u
    JOIN 
        portfolio_performance p 
    ON 
        u.investor_id = p.investor_id
    GROUP BY 
        u.investor_id
    ORDER BY 
        total_profit DESC
    LIMIT 1";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Most Profitable Investor</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Most Profitable Investor</h1>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='investor-info'>";
                echo "<p><strong>Name:</strong> " . htmlspecialchars($row["name"]) . "</p>";
                echo "<p><strong>Email:</strong> " . htmlspecialchars($row["email"]) . "</p>";
                echo "<p><strong>Total Profit:</strong> $" . number_format($row["total_profit"], 2) . "</p>";
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
