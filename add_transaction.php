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

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['investment_id']) && isset($_POST['transaction_date']) && isset($_POST['transaction_type']) && isset($_POST['amount'])) {

    // Get form data
    $investment_id = $_POST['investment_id'];
    $transaction_date = $_POST['transaction_date'];
    $transaction_type = $_POST['transaction_type'];
    $amount = $_POST['amount'];

    // Validate that only "sell" transactions are allowed
    if ($transaction_type !== "sell") {
        echo "<h2>Error: Only 'sell' transactions are allowed.</h2>";
        echo "<a href='/'>Back to Home</a>";  // Update this link to redirect correctly to the root
        exit;
    }

    // Prepare and execute the SQL query
    $sql = "INSERT INTO transactions (investment_id, transaction_date, transaction_type, amount) VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("issd", $investment_id, $transaction_date, $transaction_type, $amount);

        try {
            $stmt->execute();
            echo "<h2>Sell transaction added successfully!</h2>";
            echo "<a href='/'>Back to Home</a>";  // Update this link to redirect correctly to the root
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }

        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
} else {
    // Show Add Transaction Form
    ?>
    <!DOCTYPE html>
<html>
<head>
    <title>Add transaction</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
    <h3>Add Sell Transaction</h3>
    <form action="add_transaction.php" method="post">
        <label for="investment_id">Investment ID:</label><br>
        <input type="number" id="investment_id" name="investment_id" required><br><br>

        <label for="transaction_date">Transaction Date:</label><br>
        <input type="date" id="transaction_date" name="transaction_date" required><br><br>

        <label for="transaction_type">Transaction Type:</label><br>
        <select id="transaction_type" name="transaction_type" required>
            <option value="sell">Sell</option>
        </select><br><br>

        <label for="amount">Amount:</label><br>
        <input type="number" id="amount" name="amount" step="0.01" required><br><br>

        <button type="submit">Add Sell Transaction</button>
    </form>
    <a href="/">Back to Home</a>  <!-- Update this link to redirect correctly to the root -->
    <?php
}

// Close the database connection
$conn->close();
?>
