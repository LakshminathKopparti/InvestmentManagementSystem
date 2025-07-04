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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['advisor_id'], $_POST['investor_id'])) {
    $advisor_id = $_POST['advisor_id'];
    $investor_id = $_POST['investor_id'];

    $sql = "INSERT INTO user_advisor (advisor_id, investor_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ii", $advisor_id, $investor_id);
        try {
            $stmt->execute();
            echo "<h3>Advisor assigned to investor successfully!</h3>";
        } catch (Exception $e) {
            echo "<h3>Error: " . $e->getMessage() . "</h3>";
        }
        $stmt->close();
    } else {
        echo "<h3>Error preparing statement: " . $conn->error . "</h3>";
    }
}

// Fetch advisors and investors for dropdown
$advisors = $conn->query("SELECT advisor_id, advisor_name FROM financial_advisors");
$investors = $conn->query("SELECT investor_id, name FROM users");

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assign Advisor to Investor</title>
</head>
<body>
    <h3>Assign Advisor to Investor</h3>
    <form action="assign_advisor.php" method="POST">
        <label for="advisor_id">Select Advisor:</label>
        <select name="advisor_id" id="advisor_id" required>
            <option value="">-- Select Advisor --</option>
            <?php while ($advisor = $advisors->fetch_assoc()): ?>
                <option value="<?php echo $advisor['advisor_id']; ?>"><?php echo $advisor['advisor_name']; ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <label for="investor_id">Select Investor:</label>
        <select name="investor_id" id="investor_id" required>
            <option value="">-- Select Investor --</option>
            <?php while ($investor = $investors->fetch_assoc()): ?>
                <option value="<?php echo $investor['investor_id']; ?>"><?php echo $investor['name']; ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <button type="submit">Assign Advisor</button>
    </form>

    <a href="/">Back to Home</a>
</body>
</html>
