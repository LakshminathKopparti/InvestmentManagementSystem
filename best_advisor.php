<?php
$servername = "localhost";
$username = "root";
$password = "Lucky@24";
$dbname = "investment";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT fa.advisor_name, SUM(pp.net_gain_or_loss) AS total_profit
        FROM financial_advisors fa
        JOIN user_advisor ia ON fa.advisor_id = ia.advisor_id
        JOIN portfolio_performance pp ON ia.investor_id = pp.investor_id
        GROUP BY fa.advisor_id
        ORDER BY total_profit DESC
        LIMIT 1";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h1>Best Advisor</h1>";
    while ($row = $result->fetch_assoc()) {
        echo "Advisor Name: " . $row["advisor_name"] . "<br>Total Profit: $" . $row["total_profit"] . "<br>";
    }
} else {
    echo "No data found.";
}

$conn->close();
?>
