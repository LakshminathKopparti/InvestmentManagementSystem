<?php
// Database credentials
$host = "localhost";
$dbname = "investment";
$username = "root";
$password = "Lucky@24";

// Connect to the database
$conn = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all investments
$sql = "SELECT i.investment_id, i.investor_id, u.name AS investor_name, i.asset_type, i.amount, i.purchase_date 
        FROM investments i
        JOIN users u ON i.investor_id = u.investor_id";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h3>Investments List</h3>";
    echo "<table border='1'>";
    echo "<tr>
            <th>Investment ID</th>
            <th>Investor ID</th>
            <th>Investor Name</th>
            <th>Asset Type</th>
            <th>Amount</th>
            <th>Purchase Date</th>
          </tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['investment_id']}</td>
                <td>{$row['investor_id']}</td>
                <td>{$row['investor_name']}</td>
                <td>{$row['asset_type']}</td>
                <td>{$row['amount']}</td>
                <td>{$row['purchase_date']}</td>
              </tr>";
    }

    echo "</table>";
} else {
    echo "<h3>No investments found.</h3>";
}

echo "<a href='/'>Back to Home</a>"; // Update this link to redirect correctly to the root

// Close the database connection
$conn->close();
?>
