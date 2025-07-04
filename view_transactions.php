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

// Fetch all transactions and corresponding investor details
$sql = "SELECT t.transaction_id, t.investment_id, t.transaction_date, t.transaction_type, t.amount, 
        i.investor_id, u.name AS investor_name
        FROM transactions t
        JOIN investments i ON t.investment_id = i.investment_id
        JOIN users u ON i.investor_id = u.investor_id";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h3>Transactions List</h3>";
    echo "<table border='1'>";
    echo "<tr>
            <th>Transaction ID</th>
            <th>Investment ID</th>
            <th>Investor ID</th>
            <th>Investor Name</th>
            <th>Transaction Date</th>
            <th>Transaction Type</th>
            <th>Amount</th>
          </tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['transaction_id']}</td>
                <td>{$row['investment_id']}</td>
                <td>{$row['investor_id']}</td>
                <td>{$row['investor_name']}</td>
                <td>{$row['transaction_date']}</td>
                <td>{$row['transaction_type']}</td>
                <td>{$row['amount']}</td>
              </tr>";
    }

    echo "</table>";
} else {
    echo "<h3>No transactions found.</h3>";
}

echo "<a href='/'>Back to Home</a>"; // Update this link to redirect correctly to the root

// Close the database connection
$conn->close();
?>
