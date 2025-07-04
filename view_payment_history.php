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

// Handle sorting request
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'payment_method';

// Fetch payment history sorted by the selected mode
$sql = "SELECT ph.payment_id, ph.transaction_id, ph.payment_date, ph.amount, ph.payment_method, 
        t.investment_id 
        FROM payment_history ph
        JOIN transactions t ON ph.transaction_id = t.transaction_id
        ORDER BY $sort_by";

$result = $conn->query($sql);

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Payment History</title>
</head>
<body>
    <h3>Payment History</h3>
    <form method="get" action="view_payment_history.php">
        <label for="sort_by">Sort By:</label>
        <select name="sort_by" id="sort_by" onchange="this.form.submit()">
            <option value="payment_method" <?php if ($sort_by == 'payment_method') echo 'selected'; ?>>Payment Method</option>
            <option value="payment_date" <?php if ($sort_by == 'payment_date') echo 'selected'; ?>>Payment Date</option>
            <option value="amount" <?php if ($sort_by == 'amount') echo 'selected'; ?>>Amount</option>
        </select>
    </form>

    <?php if ($result->num_rows > 0): ?>
        <table border="1">
            <tr>
                <th>Payment ID</th>
                <th>Transaction ID</th>
                <th>Investment ID</th>
                <th>Payment Date</th>
                <th>Amount</th>
                <th>Payment Method</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['payment_id']; ?></td>
                    <td><?php echo $row['transaction_id']; ?></td>
                    <td><?php echo $row['investment_id']; ?></td>
                    <td><?php echo $row['payment_date']; ?></td>
                    <td><?php echo $row['amount']; ?></td>
                    <td><?php echo $row['payment_method']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <h3>No payment records found.</h3>
    <?php endif; ?>

    <a href="/">Back to Home</a>
</body>
</html>
