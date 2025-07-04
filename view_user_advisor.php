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

// Fetch assigned advisors
$sql = "SELECT ua.advisor_id, ua.investor_id, fa.advisor_name, u.name AS investor_name
        FROM user_advisor ua
        JOIN financial_advisors fa ON ua.advisor_id = fa.advisor_id
        JOIN users u ON ua.investor_id = u.investor_id";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Assigned Advisors</title>
</head>
<body>
    <h3>Assigned Advisors List</h3>
    <?php if ($result->num_rows > 0): ?>
        <table border="1">
            <tr>
                <th>Advisor ID</th>
                <th>Advisor Name</th>
                <th>Investor ID</th>
                <th>Investor Name</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['advisor_id']; ?></td>
                    <td><?php echo $row['advisor_name']; ?></td>
                    <td><?php echo $row['investor_id']; ?></td>
                    <td><?php echo $row['investor_name']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <h3>No assigned advisors found.</h3>
    <?php endif; ?>

    <a href="/">Back to Home</a>
</body>
</html>

<?php
// Close the connection
$conn->close();
?>
