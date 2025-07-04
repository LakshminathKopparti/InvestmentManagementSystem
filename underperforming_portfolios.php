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

// SQL query to fetch underperforming portfolios
$sql = "
    SELECT 
        u.name, 
        u.email, 
        pp.net_gain_or_loss
    FROM 
        users u
    JOIN 
        portfolio_performance pp ON u.investor_id = pp.investor_id
    WHERE 
        pp.net_gain_or_loss < 0";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Underperforming Portfolios</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Underperforming Portfolios</h1>
        <?php
        if ($result->num_rows > 0) {
            echo "<ul class='portfolio-list'>";
            while ($row = $result->fetch_assoc()) {
                echo "<li class='portfolio-item'>";
                echo "<p><strong>Name:</strong> " . htmlspecialchars($row["name"]) . "</p>";
                echo "<p><strong>Email:</strong> " . htmlspecialchars($row["email"]) . "</p>";
                echo "<p><strong>Net Loss:</strong> $" . number_format($row["net_gain_or_loss"], 2) . "</p>";
                echo "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No underperforming portfolios found.</p>";
        }
        $conn->close();
        ?>
        <a href="/" class="back-link">Back to Home</a> <!-- Update this link as needed -->
    </div>
</body>
</html>
