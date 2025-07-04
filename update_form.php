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

// Initialize message variable
$message = "";

// Update current value
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['investment_id']) && !empty($_POST['current_value'])) {
        $investment_id = intval($_POST['investment_id']);
        $current_value = floatval($_POST['current_value']);

        // Update the investment value
        $sql = "UPDATE portfolio_performance 
                SET current_value = ?, 
                    net_gain_or_loss = ? - purchase_value 
                WHERE investment_id = ?";

        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ddi", $current_value, $current_value, $investment_id);

            try {
                $stmt->execute();
                if ($stmt->affected_rows > 0) {
                    $message = "Investment updated successfully!";
                } else {
                    $message = "No matching investment found or no update was needed.";
                }
            } catch (Exception $e) {
                $message = "Error: " . $e->getMessage();
            }

            $stmt->close();
        } else {
            $message = "Error preparing statement: " . $conn->error;
        }
    } else {
        $message = "Invalid request. Please make sure both 'Investment ID' and 'Current Value' are provided.";
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
    <title>Update Investment</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to the CSS file -->
</head>
<body>
    <div class="container">
        <h1>Update Investment</h1>

        <!-- Display message -->
        <?php if (!empty($message)) : ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <!-- HTML form to update the investment -->
        <form action="" method="POST" class="update-form">
            <label for="investment_id">Investment ID:</label>
            <input type="text" name="investment_id" id="investment_id" required><br><br>
            
            <label for="current_value">Current Value:</label>
            <input type="number" name="current_value" id="current_value" step="0.01" required><br><br>

            <button type="submit">Update Investment</button>
        </form>
        <a href="/" class="back-link">Back to Home</a>
    </div>
</body>
</html>
