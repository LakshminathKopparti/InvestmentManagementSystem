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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['payment_id'], $_POST['payment_method'], $_POST['transaction_id'])) {
    $payment_id = $_POST['payment_id'];
    $payment_method = $_POST['payment_method'];
    $transaction_id = $_POST['transaction_id'];

    // Fetch current payment method for display
    $result = $conn->query("SELECT payment_method FROM payment_history WHERE payment_id = '$payment_id' AND transaction_id = '$transaction_id'");
    $current_payment_method = null;
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $current_payment_method = $row['payment_method'];
    }

    // If current payment method exists, update it
    if ($current_payment_method !== null) {
        $sql = "UPDATE payment_history 
                SET payment_method = ? 
                WHERE payment_id = ? AND transaction_id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sis", $payment_method, $payment_id, $transaction_id);

            try {
                $stmt->execute();
                if ($stmt->affected_rows > 0) {
                    echo "<h3>Payment method updated successfully!</h3>";
                } else {
                    echo "<h3>No matching payment record found or no update needed.</h3>";
                }
            } catch (Exception $e) {
                echo "<h3>Error: " . $e->getMessage() . "</h3>";
            }
            $stmt->close();
        } else {
            echo "<h3>Error preparing statement: " . $conn->error . "</h3>";
        }
    } else {
        echo "<h3>No payment record found with the provided Payment ID and Transaction ID.</h3>";
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Payment Method</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to external CSS file -->
</head>
<body>
    <div class="container">
        <h3>Update Payment Method</h3>

        <form action="update_payment_method.php" method="POST">
            <label for="payment_id">Payment ID:</label>
            <input type="text" name="payment_id" id="payment_id" required><br><br>

            <label for="transaction_id">Transaction ID:</label>
            <input type="text" name="transaction_id" id="transaction_id" required><br><br>

            <?php
            // Fetch payment record for the entered payment ID and transaction ID
            if (isset($_POST['payment_id']) && isset($_POST['transaction_id'])) {
                $payment_id = $_POST['payment_id'];
                $transaction_id = $_POST['transaction_id'];
                $conn = new mysqli($host, $username, $password, $dbname);
                $result = $conn->query("SELECT payment_method FROM payment_history WHERE payment_id = '$payment_id' AND transaction_id = '$transaction_id'");
                
                if ($result && $result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $current_payment_method = $row['payment_method'];
                    echo "<p><strong>Current Payment Method:</strong> " . $current_payment_method . "</p>";
                } else {
                    echo "<p><strong>No matching payment record found for the provided Payment ID and Transaction ID.</strong></p>";
                }
                $conn->close();
            }
            ?>

            <label for="payment_method">New Payment Method:</label>
            <select name="payment_method" id="payment_method" required>
                <option value="">-- Select Method --</option>
                <option value="cash">Cash</option>
                <option value="upi">UPI</option>
                <option value="creditcard">Credit Card</option>
                <option value="debitcard">Debit Card</option>
                <option value="bank transfer">Bank Transfer</option>
            </select><br><br>

            <button type="submit">Update Payment Method</button>
        </form>

        <a href="/" class="back-link">Back to Home</a>
    </div>
</body>
</html>
