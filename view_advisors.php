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

// Fetch all financial advisors
$sql = "SELECT advisor_id, advisor_name, contact_number, email FROM financial_advisors";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>
<head>
    <title>View Financial Advisors</title>
</head>
<body>
    <h3>Financial Advisors List</h3>
    <?php if ($result->num_rows > 0): ?>
        <table border="1">
            <tr>
                <th>Advisor ID</th>
                <th>Advisor Name</th>
                <th>Contact Number</th>
                <th>Email</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['advisor_id']; ?></td>
                    <td><?php echo $row['advisor_name']; ?></td>
                    <td><?php echo $row['contact_number']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <h3>No financial advisors found.</h3>
    <?php endif; ?>

    <a href="/">Back to Home</a>
</body>
</html>

<?php
// Close the connection
$conn->close();
?>
