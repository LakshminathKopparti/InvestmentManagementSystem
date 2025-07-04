<?php
// Database credentials
$host = "localhost";
$dbname = "investment";
$username = "root";
$password = "Lucky@24";

// Connect to the database
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch portfolio performance
$sql = "SELECT pp.performance_id, pp.investor_id, u.name AS investor_name, 
               pp.investment_id, pp.record_date, pp.purchase_value, 
               pp.current_value, pp.net_gain_or_loss
        FROM portfolio_performance pp
        JOIN users u ON pp.investor_id = u.investor_id";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h3>Portfolio Performance</h3>";
    echo "<table border='1'>";
    echo "<tr>
            <th>Performance ID</th>
            <th>Investor ID</th>
            <th>Investor Name</th>
            <th>Investment ID</th>
            <th>Record Date</th>
            <th>Purchase Value</th>
            <th>Current Value</th>
            <th>Net Gain/Loss</th>
          </tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['performance_id']}</td>
                <td>{$row['investor_id']}</td>
                <td>{$row['investor_name']}</td>
                <td>{$row['investment_id']}</td>
                <td>{$row['record_date']}</td>
                <td>{$row['purchase_value']}</td>
                <td>{$row['current_value']}</td>
                <td>{$row['net_gain_or_loss']}</td>
              </tr>";
    }

    echo "</table>";
} else {
    echo "<h3>No portfolio data found.</h3>";
}

echo "<a href='/'>Back to Home</a>";

// Close the database connection
$conn->close();
?>
