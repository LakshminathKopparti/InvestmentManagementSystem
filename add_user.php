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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['name']) && isset($_POST['email']) && isset($_POST['contact_number'])) {

    // Get form data
    $name = $_POST['name'];
    $address = $_POST['address']; // Optional field
    $email = $_POST['email'];
    $contact_number = $_POST['contact_number'];

    // Prepare and execute the SQL query
    $sql = "INSERT INTO users (name, address, email, contact_number) VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("sssi", $name, $address, $email, $contact_number);

        try {
            $stmt->execute();
            $user_id = $stmt->insert_id; // Get the ID of the newly added user
            echo "<h2>User added successfully! User ID: $user_id</h2>";
            echo "<a href='/'>Back to Home</a>";  // Update this link to redirect correctly to the root
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            exit;
        }

        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
        exit;
    }
} else {
    // Show Add User Form
    ?>
<!DOCTYPE html>
<html>
<head>
    <title>Add user</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
    
    <h3>Add New User</h3>
    <form action="add_user.php" method="post">
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name" required><br><br>

        <label for="address">Address:</label><br>
        <input type="text" id="address" name="address"><br><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="contact_number">Contact Number:</label><br>
        <input type="text" id="contact_number" name="contact_number" required><br><br>

        <button type="submit">Add User</button>
    </form>
    <a href="/">Back to Home</a>  <!-- Update this link to redirect correctly to the root -->
    <?php
}

// Close the database connection
$conn->close();
?>
