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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['investor_id']) && isset($_POST['asset_type']) && isset($_POST['amount']) && isset($_POST['purchase_date'])) {

    // Get form data
    $investor_id = $_POST['investor_id'];
    $asset_type = $_POST['asset_type'];
    $amount = $_POST['amount'];
    $purchase_date = $_POST['purchase_date'];

    // Call the stored procedure to add the investment
    $sql = "CALL add_investment(?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("isds", $investor_id, $asset_type, $amount, $purchase_date);

        try {
            $stmt->execute();
            echo "<div class='container'><h2>Investment added successfully!</h2>";
            echo "<a href='/' class='back-link'>Back to Home</a></div>"; // Update this link to redirect correctly to the root
        } catch (Exception $e) {
            echo "<div class='container'><h3>Error: " . $e->getMessage() . "</h3></div>";
        }

        $stmt->close();
    } else {
        echo "<div class='container'><h3>Error preparing statement: " . $conn->error . "</h3></div>";
    }
} else {
    // Show Add Investment Form
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Add New Investment</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <div class="container">
            <h3>Add New Investment</h3>
            <form action="add_investment.php" method="post" class="form-container">
                <label for="investor_id">Investor ID:</label><br>
                <input type="number" id="investor_id" name="investor_id" required><br><br>

                <label for="asset_type">Asset Type:</label><br>
                <select id="asset_type" name="asset_type" required>
                    <option value="stocks">Stocks</option>
                    <option value="gold">Gold</option>
                    <option value="real_estate">Real Estate</option>
                    <option value="mutual_funds">Mutual Funds</option>
                    <option value="fixed_deposits">Fixed Deposits</option>
                    <option value="bonds">Bonds</option>
                </select><br><br>

                <label for="amount">Amount:</label><br>
                <input type="number" id="amount" name="amount" step="0.01" required><br><br>

                <label for="purchase_date">Purchase Date:</label><br>
                <input type="date" id="purchase_date" name="purchase_date" required><br><br>

                <button type="submit" class="submit-button">Add Investment</button>
            </form>
            <a href="/" class="back-link">Back to Home</a> <!-- Update this link to redirect correctly to the root -->
        </div>
    </body>
    </html>
    <?php
}

// Close the database connection
$conn->close();
?>
