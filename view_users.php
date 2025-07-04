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

// Retrieve and display the `users` table
$sql = "SELECT * FROM users";
$result = $conn->query($sql);

echo "<style>
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        border: 1px solid black;
        padding: 8px;
        text-align: left;
    }
    th {
        background-color: #f2f2f2;
    }
</style>";


if ($result->num_rows > 0) {
    echo "<h3>Current Users:</h3>";
    echo "<table>
            <tr>
                <th>Investor ID</th>
                <th>Name</th>
                <th>Address</th>
                <th>Email</th>
                <th>Contact Number</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['investor_id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['address']}</td>
                <td>{$row['email']}</td>
                <td>{$row['contact_number']}</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No users found in the database.</p>";
}

// Close the database connection
$conn->close();
?>

<a href="/">Back to Home</a>  <!-- Update this link to redirect correctly to the root -->
