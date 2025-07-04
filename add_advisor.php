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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['advisor_name'], $_POST['contact_number'], $_POST['email'])) {
    $advisor_name = $_POST['advisor_name'];
    $contact_number = $_POST['contact_number'];
    $email = $_POST['email'];

    $sql = "INSERT INTO financial_advisors (advisor_name, contact_number, email) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sis", $advisor_name, $contact_number, $email);
        try {
            $stmt->execute();
            echo "<h3>Financial Advisor added successfully!</h3>";
        } catch (Exception $e) {
            echo "<h3>Error: " . $e->getMessage() . "</h3>";
        }
        $stmt->close();
    } else {
        echo "<h3>Error preparing statement: " . $conn->error . "</h3>";
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Financial Advisor</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
    <h3>Add Financial Advisor</h3>
    <form action="add_advisor.php" method="POST">
        <label for="advisor_name">Advisor Name:</label>
        <input type="text" name="advisor_name" id="advisor_name" required><br><br>

        <label for="contact_number">Contact Number:</label>
        <input type="text" name="contact_number" id="contact_number" required><br><br>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required><br><br>

        <button type="submit">Add Advisor</button>
    </form>

    <a href="/">Back to Home</a>
</body>
</html>
