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

// SQL query to fetch the top 10 investments
$sql = "
    SELECT 
        i.asset_type, 
        i.amount, 
        u.name AS investor_name, 
        i.purchase_date
    FROM 
        investments i
    JOIN 
        users u ON i.investor_id = u.investor_id
    ORDER BY 
        i.amount DESC
    LIMIT 10";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top 10 Investments</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Top 10 Investments</h1>
        <?php
        if ($result->num_rows > 0) {
            echo "<ul class='investment-list'>";
            while ($row = $result->fetch_assoc()) {
                echo "<li class='investment-item'>";
                echo "<p><strong>Investor:</strong> " . htmlspecialchars($row["investor_name"]) . "</p>";
                echo "<p><strong>Asset Type:</strong> " . htmlspecialchars($row["asset_type"]) . "</p>";
                echo "<p><strong>Amount:</strong> $" . number_format($row["amount"], 2) . "</p>";
                echo "<p><strong>Purchase Date:</strong> " . htmlspecialchars($row["purchase_date"]) . "</p>";
                echo "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No data found.</p>";
        }
        $conn->close();
        ?>
        <a href="/" class="back-link">Back to Home</a> <!-- Update the link as needed -->
    </div>
</body>
</html>
